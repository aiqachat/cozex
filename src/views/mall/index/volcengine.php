<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: chenzs
 */
?>
<style type="text/css">
    @import "<?= \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/css/table.css' ?>";
</style>
<style>
    .table-body {
        padding: 20px 20px 0 20px;
        background-color: #fff;
    }
</style>
<div id="app" v-cloak>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <span>密钥列表</span>
            <div style="float: right;margin-top: -5px">
                <el-button type="primary" @click="edit" size="small">添加密钥</el-button>
            </div>
            <el-alert style="margin-top: 10px;" :closable="false"
                      type="success">
                API访问密钥（Access Key）是请求火山引擎API的安全凭证，包含Access Key ID和Secret Access Key，请您妥善保管并定期轮换密钥，不要将密钥信息共享至公开环境（如上传GitHub），以保障云资源的安全性。建议您使用最小化授权的IAM用户的密钥进行API访问，不建议直接使用主账号密钥或使用权限过大的IAM用户密钥。）
            </el-alert>
        </div>
        <div class="table-body">
            <el-table :data="form" border style="width: 100%" v-loading="listLoading">
                <el-table-column type="selection" width="55"></el-table-column>
                <el-table-column label="密钥名称" prop="name" width="250"></el-table-column>
                <el-table-column prop="access_id" label="Access Key ID" min-width="150">
                    <template slot-scope="scope">
                        <div style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">{{scope.row.access_id}}</div>
                    </template>
                </el-table-column>
                <el-table-column label="Secret Access Key" min-width="150">
                    <template slot-scope="scope">
                        <div style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">{{scope.row.secret_key}}</div>
                    </template>
                </el-table-column>
                <el-table-column prop="created_at" label="添加时间" width="180"></el-table-column>
                <el-table-column label="操作" width="150" fixed="right">
                    <template slot-scope="scope">
                        <el-tooltip class="item" effect="dark" content="编辑" placement="top">
                            <el-button circle type="text" size="mini" @click="edit(scope.row)">
                                <img src="statics/img/mall/edit.png" alt="">
                            </el-button>
                        </el-tooltip>
                        <el-tooltip class="item" effect="dark" content="删除" placement="top">
                            <el-button circle type="text" size="mini" @click="destroy(scope.row)">
                                <img src="statics/img/mall/del.png" alt="">
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
        <!-- 编辑 -->
        <el-dialog :title="data.id ? '编辑火山密钥' : '添加火山密钥'" :visible.sync="dialog" width="40%">
            <el-form :model="data" label-width="150px" :rules="dataRules" ref="data">
                <el-form-item label="密钥名称" prop="name" size="small">
                    <el-input v-model.trim="data.name"></el-input>
                </el-form-item>
                <el-form-item label="Access Key ID" prop="access_id" size="small">
                    <el-input v-model.trim="data.access_id" maxlength="47" show-word-limit></el-input>
                </el-form-item>
                <el-form-item label="Secret Access Key" prop="secret_key" size="small">
                    <el-input v-model.trim="data.secret_key" maxlength="60" show-word-limit></el-input>
                </el-form-item>
                <el-form-item label="关联应用" prop="account" size="small">
                    <el-checkbox-group v-model="data.account">
                        <el-checkbox v-for="item in accounts" :label="item.id" :key="item.id">{{item.name}}</el-checkbox>
                    </el-checkbox-group>
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
                form: [],
                pageCount: 0,
                pageSize: 10,
                page: 1,
                currentPage: null,
                listLoading: false,
                btnLoading: false,

                dialog: false,
                data: {},
                dataRules: {
                    access_id: [
                        {required: true, message: '不能为空', trigger: 'blur'},
                    ],
                    secret_key: [
                        {required: true, message: '不能为空', trigger: 'blur'},
                    ],
                },
                accounts: []
            };
        },
        methods: {
            edit(row){
                this.data = {account: []};
                if(row){
                    this.data = Object.assign({}, this.data, row);
                }
                if(this.$refs.data) {
                    this.$refs.data.clearValidate();
                }
                this.dialog = true;
            },
            destroy: function (column) {
                this.$confirm('确认删除该记录吗?', '提示', {
                    type: 'warning'
                }).then(() => {
                    this.listLoading = true;
                    request({
                        params: {r: 'mall/index/volcengine-destroy'},
                        data: {id: column.id},
                        method: 'post'
                    }).then(e => {
                        this.getList()
                    }).catch(e => {
                        this.listLoading = false;
                    });
                }).catch(action => {
                    this.listLoading = false;
                });
            },
            submit() {
                this.$refs.data.validate((valid) => {
                    if (valid) {
                        this.btnLoading = true;
                        request({
                            params: {r: 'mall/index/volcengine'},
                            method: 'post',
                            data: this.data,
                        }).then(e => {
                            if (e.data.code === 0) {
                                this.$message.success(e.data.msg);
                                this.dialog = false;
                                this.getList()
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
            pagination(currentPage) {
                this.page = currentPage;
                this.getList();
            },
            getList() {
                this.listLoading = true;
                request({
                    params: {r: 'mall/index/volcengine', page: this.page},
                }).then(e => {
                    if (e.data.code === 0) {
                        this.form = e.data.data.list;
                        this.accounts = e.data.data.accounts;
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
        },
        mounted: function () {
            this.page = getQuery('page') ? getQuery('page') : 1;
            this.getList();
        }
    });
</script>
