<?php
/**
 * Created by PhpStorm.
 * User: chenzs
 * Date: 2019/7/22
 * Time: 11:05
 * @copyright: ©2021 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */
?>
<style>
    .el-dialog {
        min-width: 950px;
    }
</style>
<template id="app-attachment-edit">
    <div class="app-attachment-edit">
        <el-dialog :visible.sync="dialogVisible" title="存储位置编辑" :close-on-click-modal="false">
            <el-form ref="form" :model="form" :rules="rules" label-position="left">
                <el-form-item label="存储位置" prop="type">
                    <template v-for="(name, key) in storageTypes">
                        <el-radio :disabled="form.id != null" v-model="form.type" :label="key" border size="medium">
                            {{name}}
                        </el-radio>
                    </template>
                </el-form-item>
                <template v-if="form.type == 1">
                    <el-form-item>
                        <div>本地服务器存储无需额外配置</div>
                    </el-form-item>
                </template>
                <template v-if="form.type == 2">
                    <el-form-item label="存储空间名称（Bucket）" prop="config.bucket" :rules="requiredRules">
                        <el-input v-model.trim="form.config.bucket"></el-input>
                    </el-form-item>
                    <el-form-item label="使用自定义域名" prop="config.is_cname">
                        <el-switch v-model.trim="form.config.is_cname" active-value="1" inactive-value="0"></el-switch>
                    </el-form-item>
                    <el-form-item label="Endpoint或自定义域名" prop="config.domain" :rules="requiredRules">
                        <el-input v-model.trim="form.config.domain"></el-input>
                        <div style="font-size: 12px;color: #909399;line-height: normal;">
                            <span>示例: </span>
                            <span style="color: #ff8576">http://oss-xx-xxx-1.aliyuncs.com</span>
                            <span>或</span>
                            <span style="color: #ff8576">http://mydomain.com</span>
                        </div>
                    </el-form-item>
                    <el-form-item label="Access Key ID" prop="config.access_key" :rules="requiredRules">
                        <el-input v-model.trim="form.config.access_key"></el-input>
                    </el-form-item>
                    <el-form-item label="Access Key Secret" prop="config.secret_key" :rules="requiredRules">
                        <el-input v-model.trim="form.config.secret_key"></el-input>
                    </el-form-item>
                    <el-form-item label="图片样式接口（选填）">
                        <el-input v-model.trim="form.config.style_api"></el-input>
                        <div style="font-size: 12px;color: #909399;line-height: normal;">
                            <span>示例: </span>
                            <span style="color: #ff8576">?x-oss-process=style/stylename</span>
                        </div>
                    </el-form-item>
                </template>
                <template v-if="form.type == 3">
                    <el-form-item label="空间名称（Bucket）" prop="config.bucket" :rules="requiredRules">
                        <el-input v-model.trim="form.config.bucket"></el-input>
                        <div style="font-size: 12px;color: #909399;line-height: normal;">
                            <span>示例: </span>
                            <span style="color: #ff8576">xxxxxx-125000000</span>
                        </div>
                    </el-form-item>
                    <el-form-item label="所属地域" prop="config.region" :rules="requiredRules">
                        <el-input v-model.trim="form.config.region"></el-input>
                        <div style="font-size: 12px;color: #909399;line-height: normal;">
                            <span>示例: </span>
                            <span style="color: #ff8576">ap-shanghai</span>
                            <span>或</span>
                            <span style="color: #ff8576">ap-shenzhen</span>
                            <span>或</span>
                            <span style="color: #ff8576">ap-xxxxxx</span>
                        </div>
                    </el-form-item>
                    <el-form-item label="自定义域名" prop="config.domain">
                        <el-input v-model.trim="form.config.domain"></el-input>
                        <div style="font-size: 12px;color: #909399;line-height: normal;">
                            <span>示例: </span>
                            <span style="color: #ff8576">http://mydomain.com</span>
                        </div>
                    </el-form-item>
                    <el-form-item label="SecretId" prop="config.secret_id" :rules="requiredRules">
                        <el-input v-model.trim="form.config.secret_id"></el-input>
                    </el-form-item>
                    <el-form-item label="SecretKey" prop="config.secret_key" :rules="requiredRules">
                        <el-input v-model.trim="form.config.secret_key"></el-input>
                    </el-form-item>
                </template>
                <template v-if="form.type == 4">
                    <el-form-item label="存储空间名称（Bucket）" prop="config.bucket" :rules="requiredRules">
                        <el-input v-model.trim="form.config.bucket"></el-input>
                    </el-form-item>
                    <el-form-item label="绑定域名" prop="config.domain" :rules="requiredRules">
                        <el-input v-model.trim="form.config.domain"></el-input>
                        <div style="font-size: 12px;color: #909399;line-height: normal;">
                            <span>示例: </span>
                            <span style="color: #ff8576">http://mydomain.com</span>
                            <span>或</span>
                            <span style="color: #ff8576">http://xxxxxx.bkt.clouddn.com</span>
                        </div>
                    </el-form-item>
                    <el-form-item label="AccessKey（AK）" prop="config.access_key" :rules="requiredRules">
                        <el-input v-model.trim="form.config.access_key"></el-input>
                    </el-form-item>
                    <el-form-item label="SecretKey（SK）" prop="config.secret_key" :rules="requiredRules">
                        <el-input v-model.trim="form.config.secret_key"></el-input>
                    </el-form-item>
                    <el-form-item label="图片样式接口（选填）">
                        <el-input v-model.trim="form.config.style_api"></el-input>
                        <div style="font-size: 12px;color: #909399">
                            <span>示例: </span>
                            <span style="color: #ff8576">?imageView2/0/w/1080/h/1080/q/85|imageslim</span>
                        </div>
                    </el-form-item>
                </template>
                <template v-if="form.type == 5">
                    <el-form-item label="存储桶名称（Bucket）" prop="config.bucket" :rules="requiredRules">
                        <el-input v-model.trim="form.config.bucket"></el-input>
                    </el-form-item>
                    <el-form-item label="所属地域" prop="config.region" :rules="requiredRules">
                        <el-input v-model.trim="form.config.region"></el-input>
                        <div style="font-size: 12px;color: #909399;line-height: normal;">
                            <span>示例: </span>
                            <span style="color: #ff8576">xx-xxx</span>
                        </div>
                    </el-form-item>
                    <el-form-item label="Access Key ID" prop="config.access_key" :rules="requiredRules">
                        <el-input v-model.trim="form.config.access_key"></el-input>
                    </el-form-item>
                    <el-form-item label="Secret Access Key" prop="config.secret_key" :rules="requiredRules">
                        <el-input v-model.trim="form.config.secret_key"></el-input>
                    </el-form-item>
                    <el-form-item label="Endpoint" prop="config.endpoint" :rules="requiredRules">
                        <el-input v-model.trim="form.config.endpoint"></el-input>
                        <div style="font-size: 12px;color: #909399;line-height: normal;">
                            <span>示例: </span>
                            <span style="color: #ff8576">tos-xx-xxx.volces.com</span>
                        </div>
                    </el-form-item>
                </template>
                <el-form-item>
                    <el-button type="primary" size="small" @click="handleSaveStorage('form')">保存</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>
        <div style="line-height: normal;" @click="handleEdit()"
             :style="'display:'+(display?display:'inline-block')">
            <slot></slot>
        </div>
    </div>
</template>
<script>
    Vue.component('app-attachment-edit', {
        template: '#app-attachment-edit',
        props: {
            display: String,
            item: Object,
            submitUrl: {
                type: String,
                default: 'admin/setting/attachment-create-storage'
            },
            storageTypes: Object
        },
        data() {
            return {
                requiredRules: [{required: true, message: '不能为空'}],
                dialogVisible: false,
                form: {
                    id: null,
                    type: null,
                    config: {},
                },
                rules: {
                    type: [
                        {required: true, message: '请选择存储位置',},
                    ]
                }
            };
        },
        methods: {
            handleSaveStorage(formName) {
                this.$refs[formName].validate(valid => {
                    if (valid) {
                        this.$request({
                            params: {
                                r: this.submitUrl
                            },
                            data: this.form,
                            method: 'post',
                        }).then(e => {
                            if (e.data.code === 0) {
                                this.$message.success('保存成功。');
                                this.dialogVisible = false;
                                this.$emit('save');
                            } else {
                                this.$message.error(e.data.msg);
                            }
                        }).catch(e => {
                        });
                    } else {
                        this.$message.error('提交内容有误，请仔细检查后提交。');
                    }
                });
            },
            handleEdit() {
                if (this.item) {
                    this.form = this.item;
                } else {
                    this.form = {
                        id: null,
                        type: null,
                        config: {},
                    };
                }
                this.dialogVisible = true;
            },
        },
    });
</script>