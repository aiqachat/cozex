<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: chenzs
 */
Yii::$app->loadViewComponent('app-volcengine-choose');
$url = Yii::$app->request->absoluteUrl;
?>
<style type="text/css">
    @import "<?= \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/css/table.css' ?>";
</style>
<style>
    .table-body {
        padding: 10px;
        background-color: #fff;
    }

    .bi {
        font-size: 20px;
    }
</style>
<div id="app" v-cloak>
    <app-volcengine-choose @account="changeAccount" url="<?=$url;?>" title="大模型语音识别-火山引擎"></app-volcengine-choose>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <div style="color: #a4a4a4;">
                录音文件识别：支持将音频文件（≤4小时）转写成文本数据，内置自动标点、语义顺滑、数字规整、智能分句等功能，可根据需要任意搭配。适用于非实时的语音识别场景，如会议记录总结、智能外呼质检、课后教辅和学情分析等。
            </div>
        </div>
        <div class="table-body" style="margin-top: 10px;">
            <div flex="box:last cross:center" style="margin-bottom: 5px;">
                <el-upload action="" accept="audio/*" :show-file-list="false" :data="data" :on-success="success">
                    <el-button size="small" type="primary">音频文件上传</el-button>
                </el-upload>
                <el-button type="primary" size="small" @click="$navigate({r:'netb/volcengine/auc'})">更多</el-button>
            </div>
            <el-alert title="温馨提示" type="warning" :closable="false" show-icon style="margin-bottom: 10px;">
                <div style="padding-left: 4px;">生成的文件和批量上传的文本将在<span style="font-weight: bold">3天</span>后自动删除，请及时下载保存重要文件。</div>
            </el-alert>
            <el-table :data="list" border style="width: 100%" v-loading="loading">
                <el-table-column type="selection" width="55"></el-table-column>
                <el-table-column prop="file" label="文件"></el-table-column>
                <el-table-column label="状态" width="80">
                    <template slot-scope="scope">
                        <span v-if="scope.row.status == 1">处理中</span>
                        <span v-if="scope.row.status == 2">成功</span>
                        <span v-if="scope.row.status == 3">
                            <el-tooltip effect="dark" :content="scope.row.err_msg" placement="top">
                                <el-button type="text">失败</el-button>
                            </el-tooltip>
                        </span>
                    </template>
                </el-table-column>
                <el-table-column prop="created_at" label="创建时间" width="180" sortable="false"></el-table-column>
                <el-table-column label="操作" width="200" fixed="right">
                    <template slot-scope="scope">
                        <el-dropdown @command="(format) => down(scope.row, format)" v-if="scope.row.status == 2">
                            <el-button circle type="text" size="mini" :disabled="!!scope.row.is_data_deleted">
                                <i class="bi bi-download"></i>
                            </el-button>
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item command="txt">TXT格式</el-dropdown-item>
                                <el-dropdown-item command="srt">SRT格式</el-dropdown-item>
                                <el-dropdown-item command="lrc">LRC格式</el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                        <el-tooltip effect="dark" content="重试" placement="top" v-if="scope.row.status == 3">
                            <el-button circle type="text" size="mini" @click="refresh(scope.row)">
                                <i class="bi bi-arrow-repeat"></i>
                            </el-button>
                        </el-tooltip>
                    </template>
                </el-table-column>
            </el-table>
        </div>
    </el-card>
</div>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                searchData: {
                    account_id: '',
                },
                loading: false,
                list: [],
                data: {_csrf: _csrf}
            };
        },
        watch: {
            'searchData.account_id': function (val, oldValue){
                this.getData();
            },
        },
        methods: {
            changeAccount(val){
                this.searchData.account_id = val;
                this.data.account_id = val;
            },
            success(){
                this.getData();
            },
            getData(type = 1) {
                if(!this.searchData.account_id){
                    return;
                }
                if(type === 1) {
                    this.loading = true;
                }
                request({
                    params: Object.assign({r: 'netb/volcengine/auc-model'}, this.searchData),
                }).then(e => {
                    if (e.data.code === 0) {
                        this.list = e.data.data.list;
                    } else {
                        this.$message.error(e.data.msg);
                    }
                    this.loading = false;
                }).catch(e => {
                    this.loading = false;
                });
            },
            down(row, format = 'txt') {
                this.listLoading = true;
                request({
                    params: {
                        r: 'netb/volcengine/download',
                        id: row.id,
                        format: format,
                    },
                }).then(e => {
                    if (e.data.code === 0) {
                        const url = e.data.data.url;
                        let urlObject = new URL(url);
                        let pathname = urlObject.pathname;
                        let fileName = pathname.split('/').pop();

                        // 如果文件名不包含格式后缀，添加它
                        if (!fileName.includes('.')) {
                            fileName = fileName + '.' + format;
                        }

                        const a = document.createElement('a');
                        a.href = url;
                        a.download = fileName;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                    } else {
                        this.$message.error(e.data.msg);
                    }
                    this.listLoading = false;
                }).catch(e => {
                    this.$message.error('下载失败');
                    this.listLoading = false;
                });
            },
            refresh: function (column) {
                this.$confirm('确认重试该记录吗?', '提示', {
                    type: 'warning'
                }).then(() => {
                    this.listLoading = true;
                    request({
                        params: {r: 'netb/volcengine/refresh'},
                        data: {id: column.id, account_id: this.searchData.account_id},
                        method: 'post'
                    }).then(e => {
                        if (e.data.code !== 0) {
                            this.$message.error(e.data.msg);
                        }
                        this.getData()
                    }).catch(e => {
                        this.listLoading = false;
                    });
                });
            },
        },
        mounted: function () {
            this.getData();
            setInterval(() => {
                let s = false;
                for(let item of this.list) {
                    if(item.status === 1) {
                        s = true;
                    }
                }
                if(s){
                    this.getData(0)
                }
            }, 5000)
        }
    });
</script>
