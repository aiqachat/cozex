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
            <span>coze授权账号</span>
            <div style="float: right;margin-top: -5px">
                <el-button type="primary" @click="edit" size="small">添加Coze账号授权</el-button>
            </div>
        </div>
        <div class="table-body">
            <el-table :data="form" border style="width: 100%" v-loading="listLoading">
                <el-table-column type="selection" width="55"></el-table-column>
                <el-table-column label="名称" prop="name" width="250"></el-table-column>
                <el-table-column prop="remark" label="备注"></el-table-column>
                <el-table-column prop="coze_secret" label="令牌">
                    <template slot-scope="scope">
                        <div style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">{{scope.row.coze_secret}}</div>
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
        <el-dialog :title="data.id ? '编辑Coze账号授权' : '添加Coze账号授权'" :visible.sync="dialog" width="30%">
            <div style="color: #a4a4a4;padding: 0 0 10px 35px;">说明：在Coze点击头像-扣子API-授权-添加新令牌，最好打开全部权限勾选全部权限</div>
            <el-form :model="data" label-width="80px" :rules="dataRules" ref="data">
                <el-form-item label="名称" prop="name" size="small">
                    <el-input v-model.trim="data.name"></el-input>
                </el-form-item>
                <el-form-item label="访问令牌" prop="coze_secret" size="small">
                    <el-input v-model.trim="data.coze_secret" maxlength="150" show-word-limit></el-input>
                </el-form-item>
                <el-form-item label="备注" prop="remark" size="small">
                    <el-input v-model.trim="data.remark"></el-input>
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
                    coze_secret: [
                        {required: true, message: '访问令牌不能为空', trigger: 'blur'},
                    ],
                    name: [
                        {required: true, message: '名称不能为空', trigger: 'blur'},
                    ],
                },
            };
        },
        methods: {
            edit(row){
                this.data = {};
                if(row){
                    this.data = Object.assign({}, row);
                }
                this.dialog = true;
            },
            destroy: function (column) {
                this.$confirm('确认删除该记录吗?', '提示', {
                    type: 'warning'
                }).then(() => {
                    this.listLoading = true;
                    request({
                        params: {r: 'mall/setting/coze-destroy'},
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
                            params: {r: 'mall/setting/coze'},
                            method: 'post',
                            data: this.data,
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
            pagination(currentPage) {
                this.page = currentPage;
                this.getList();
            },
            getList() {
                this.listLoading = true;
                request({
                    params: {r: 'mall/setting/coze', page: this.page},
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
        },
        mounted: function () {
            this.page = getQuery('page') ? getQuery('page') : 1;
            this.getList();
        }
    });
</script>