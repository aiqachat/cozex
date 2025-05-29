<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: chenzs
 */
Yii::$app->loadViewComponent('app-volcengine-choose');
$url = $_SERVER['HTTP_REFERER'];
?>
<style type="text/css">
    @import "<?= \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/css/table.css' ?>";
</style>
<style>
    .table-body {
        padding: 20px 20px 0 20px;
        background-color: #fff;
    }

    .input-item {
        display: inline-block;
        width: 290px;
        margin: 0 0 20px;
    }

    .bi {
        font-size: 20px;
    }
</style>
<div id="app" v-cloak>
    <app-volcengine-choose @account="changeAccount" :dialog="newDialog" @close="closeDialog" url="<?=$url;?>" title="大模型语音识别-火山引擎"></app-volcengine-choose>
    <el-alert style="margin-bottom: 10px;" :closable="false"
              type="success">
        语音识别（Automatic SpeechRecognition，ASR）采用业内领先的端到端算法模型，准确地将语音内容转写成文字。产品支持时间戳，区分讲话人，数字格式智能转换，智能标点等功能。适用于录音质检、会议总结、音频内容分析、课堂内容分析等场景。
    </el-alert>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div class="table-body">
            <div class="input-item">
                <el-input @keyup.enter.native="search" size="small" placeholder="请输入名称" v-model="searchData.keyword" clearable @clear="search">
                    <el-button slot="append" icon="el-icon-search" @click="search"></el-button>
                </el-input>
            </div>
            <div style="float: right;">
                <el-button type="primary" @click="open" size="small">添加</el-button>
            </div>
            <el-alert title="温馨提示" type="warning" :closable="false" show-icon style="margin-bottom: 10px;">
                <div style="padding-left: 4px;">生成的文件和批量上传的文本将在<span style="font-weight: bold">3天</span>后自动删除，请及时下载保存重要文件。</div>
            </el-alert>
            <el-table :data="form" border style="width: 100%" v-loading="listLoading">
                <el-table-column type="selection" width="55"></el-table-column>
                <el-table-column prop="file" label="文件"></el-table-column>
                <el-table-column label="状态" width="90">
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
                <el-table-column label="操作" width="130" fixed="right">
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
                        <el-tooltip effect="dark" content="删除" placement="top">
                            <el-button circle type="text" size="mini" @click="destroy(scope.row)">
                                <i class="bi bi-trash"></i>
                            </el-button>
                        </el-tooltip>
                    </template>
                </el-table-column>
            </el-table>
            <div flex="dir:right" style="margin-top: 20px;">
                <el-pagination @current-change="pagination" hide-on-single-page background layout="total, prev, pager, next, jumper"
                               :total="pageCount" :page-size="pageSize" :current-page="currentPage"></el-pagination>
            </div>
        </div>
        <el-dialog title="操作" :visible.sync="dialog" width="30%">
            <el-form :model="data" label-width="100px" :rules="rules" ref="data">
                <el-form-item label="文件" prop="file">
                    <el-input v-model="data.file">
                        <template slot="append">
                            <app-attachment v-model="data.file" :type="'video'">
                                <el-button>上传文件</el-button>
                            </app-attachment>
                        </template>
                    </el-input>
                </el-form-item>
                <el-form-item prop="is_del">
                    <template slot='label'>
                        <span>文件删除</span>
                        <el-tooltip effect="dark" content="文件处理完后自动删除"
                                    placement="top">
                            <i class="el-icon-info"></i>
                        </el-tooltip>
                    </template>
                    <el-switch v-model="data.data.is_del" :active-value="1"
                               :inactive-value="0"></el-switch>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialog = false">取消</el-button>
                <el-button :loading="btnLoading" type="primary" @click="submit">确认</el-button>
            </div>
        </el-dialog>
    </el-card>
</div>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                searchData: {
                    keyword: '',
                    account_id: '',
                },
                form: [],
                pageCount: 0,
                pageSize: 10,
                page: 1,
                currentPage: null,
                listLoading: false,
                btnLoading: false,

                newDialog: false,
                dialog: false,
                data: {
                    data: {is_del: 1}
                },
                rules: {
                    file: [
                        {required: true, message: '文件不能为空', trigger: 'blur'},
                    ],
                },
            };
        },
        watch: {
            'searchData.account_id': function (val, oldValue){
                this.getList();
            },
        },
        methods: {
            closeDialog() {
                this.newDialog = false;
            },
            open(){
                if(!this.searchData.account_id){
                    this.newDialog = true;
                    return;
                }
                this.dialog = true
            },
            changeAccount(val){
                this.searchData.account_id = val;
            },
            submit() {
                this.$refs.data.validate((valid) => {
                    if (valid) {
                        this.btnLoading = true;
                        request({
                            params: {r: 'netb/volcengine/auc'},
                            method: 'post',
                            data: Object.assign(this.data, {account_id: this.searchData.account_id}),
                        }).then(e => {
                            if (e.data.code === 0) {
                                this.getList()
                                this.dialog = false;
                            } else {
                                this.$message.error(e.data.msg);
                            }
                            this.btnLoading = false;
                        }).catch(e => {
                            this.btnLoading = false;
                        });
                    }
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
            search() {
                this.page = 1;
                this.getList();
            },
            pagination(currentPage) {
                this.page = currentPage;
                this.getList();
            },
            getList(type = 1) {
                if(!this.searchData.account_id){
                    return;
                }
                if(type === 1) {
                    this.listLoading = true;
                }
                let param = Object.assign({r: 'netb/volcengine/auc', page: this.page}, this.searchData);
                request({
                    params: param,
                }).then(e => {
                    if (e.data.code === 0) {
                        this.form = e.data.data.list;
                        this.pageCount = e.data.data.pagination.total_count;
                        this.pageSize = e.data.data.pagination.pageSize;
                        this.currentPage = e.data.data.pagination.current_page;
                    } else {
                        this.$message.error(e.data.msg);
                    }
                    this.listLoading = false;
                }).catch(e => {
                    this.listLoading = false;
                });
            },
            destroy: function (column) {
                this.$confirm('确认删除该记录吗?', '提示', {
                    type: 'warning'
                }).then(() => {
                    this.listLoading = true;
                    request({
                        params: {r: 'netb/volcengine/destroy'},
                        data: {id: column.id, account_id: this.searchData.account_id},
                        method: 'post'
                    }).then(e => {
                        if (e.data.code !== 0) {
                            this.$message.error(e.data.msg);
                        }
                        this.getList()
                    }).catch(e => {
                        this.listLoading = false;
                    });
                });
            },
        },
        mounted: function () {
            this.page = getQuery('page') ? getQuery('page') : 1;
            this.getList();
            setInterval(() => {
                let s = false;
                for(let item of this.form) {
                    if(item.status === 1) {
                        s = true;
                    }
                }
                if(s){
                    this.getList(0);
                }
            }, 5000)
        }
    });
</script>
