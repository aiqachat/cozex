<?php
/**
 * Created by IntelliJ IDEA.
 * author: chenzs
 * Date: 2019/3/16
 * Time: 10:16
 */
$r = 'netb/volcengine/batch';
$url = Yii::$app->urlManager->createUrl([$r]);
?>
<style>
    .batch-container {
        display: flex;
        align-items: center;
    }

    .batch-btns {
        display: flex;
        align-items: center;
    }

    .batch-btn {
        margin-left: 10px;
    }

    .batch-alert {
        margin-left: 10px;
        max-width: 500px;
        flex: 1;
        border-radius: 8px;
        box-shadow: 0 2px 8px #f0f1f2;
    }

    .batch-alert-content {
        padding-left: 4px;
    }
</style>
<template id="app-batch-upload">
    <div class="batch-container">
        <div class="batch-btns">
            <el-upload :action="url" accept="text/plain" :show-file-list="false" :data="data" :disabled="loading"
                :on-success="handleUploadSuccess" :before-upload="handleBeforeUpload" multiple :limit="30"
                :on-exceed="handleExceed">
                <el-button size="small" type="primary" :loading="loading">上传文本(批量)</el-button>
            </el-upload>
            <el-button size="small" type="primary" :loading="loading" class="batch-btn"
                @click="handleAction('handle', '合成')">合成</el-button>
            <el-button size="small" type="primary" :loading="loading" class="batch-btn"
                @click="handleAction('delete', '删除')">删除</el-button>
            <el-button size="small" type="primary" :loading="loading" class="batch-btn"
                @click="handleAction('down', '下载')">批量下载</el-button>
        </div>
        <el-alert title="温馨提示" type="warning" :closable="false" show-icon class="batch-alert">
            <div class="batch-alert-content">生成的文件和批量上传的文本将在<span style="font-weight: bold">3天</span>后自动删除，请及时下载保存重要文件。
            </div>
        </el-alert>
    </div>
</template>
<script>
    Vue.component('app-batch-upload', {
        template: '#app-batch-upload',
        props: {
            url: {
                type: String,
                default: '<?= $url ?>',
            },
            type: Number,
            data: Object,
            selectData: Array,
        },
        data() {
            return {
                loading: false,
                uploadCount: 0,
            };
        },
        methods: {
            handleUploadSuccess(response, file, fileList) {
                this.uploadCount++;
                const isComplete = this.uploadCount === fileList.length;
                this.$emit('success', file, isComplete);
                if (isComplete) {
                    this.uploadCount = 0;
                }
                this.loading = false;
            },
            handleExceed() {
                this.$message.error('最多只能上传30个文件');
                return false;
            },
            handleBeforeUpload(file) {
                if (!this.validateVoiceType()) {
                    return false;
                }
                return this.detectBOM(file).then(encoding => {
                    if (encoding !== 'UTF-8') {
                        this.$message.error('请选择UTF-8编码的文本');
                        return Promise.reject();
                    }
                    this.data._csrf = _csrf;
                    this.data.type = this.type;
                    this.loading = true;
                    return true;
                });
            },
            validateVoiceType() {
                if (!this.data.data) {
                    this.$message.error('请选择音色');
                    return false;
                }
                if (this.data.data instanceof Object) {
                    if (!this.data.data.voice_type) {
                        this.$message.error('请选择音色');
                        return false;
                    }
                    this.data.data = JSON.stringify(this.data.data);
                }
                return true;
            },
            detectBOM(file) {
                return new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.readAsArrayBuffer(file.slice(0, 4));
                    reader.onload = (e) => {
                        const bytes = new Uint8Array(e.target.result);
                        const encodings = {
                            'UTF-8': [0xEF, 0xBB, 0xBF],
                            'UTF-16LE': [0xFF, 0xFE],
                            'UTF-16BE': [0xFE, 0xFF],
                            'UTF-32BE': [0x00, 0x00, 0xFE, 0xFF],
                            'UTF-32LE': [0xFF, 0xFE, 0x00, 0x00]
                        };

                        for (const [encoding, signature] of Object.entries(encodings)) {
                            if (signature.every((byte, index) => bytes[index] === byte)) {
                                return resolve(encoding);
                            }
                        }
                        resolve('UTF-8');
                    };
                });
            },
            async handleAction(op, actionName) {
                if (!this.selectData.length) {
                    this.$message.error(`请选择${actionName}数据`);
                    return false;
                }

                try {
                    await this.$confirm(`确认${actionName}数据吗?`, '提示', { type: 'warning' });
                    this.loading = true;

                    if (op === 'down') {
                        // 拼接下载URL，参数用encodeURIComponent处理
                        const baseUrl = '<?= $url ?>';
                        const params = [
                            `account_id=${encodeURIComponent(this.data.account_id)}`,
                            `data=${encodeURIComponent(JSON.stringify(this.selectData))}`,
                            `op=down`
                        ];
                        const url = baseUrl + '&' + params.join('&');
                        window.open(url, '_blank');
                        this.loading = false;
                        return;
                    }

                    const response = await request({
                        params: { r: '<?= $r ?>' },
                        method: 'post',
                        data: {
                            account_id: this.data.account_id,
                            data: this.selectData,
                            op
                        }
                    });

                    if (response.data.code === 0) {
                        this.$message.success(response.data.msg);
                        this.$emit('success');
                    } else {
                        this.$message.error(response.data.msg);
                    }
                } catch (error) {
                    if (error !== 'cancel') {
                        this.$message.error(`${actionName}操作失败`);
                    }
                } finally {
                    this.loading = false;
                }
            }
        }
    });
</script>