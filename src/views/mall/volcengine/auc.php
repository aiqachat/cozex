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

    .input-item {
        display: inline-block;
        width: 290px;
        margin: 0 0 20px;
    }
</style>
<div id="app" v-cloak>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <span>大模型录音文件识别列表</span>
            <div style="float: right;margin-top: -5px">
                <el-button type="primary" @click="dialog = true" size="small">添加</el-button>
            </div>
        </div>
        <div class="table-body">
            <div class="input-item">
                <el-input @keyup.enter.native="search" size="small" placeholder="请输入名称" v-model="searchData.keyword" clearable @clear="search">
                    <el-button slot="append" icon="el-icon-search" @click="search"></el-button>
                </el-input>
            </div>
            <el-table :data="form" border style="width: 100%" v-loading="listLoading">
                <el-table-column type="selection" width="55"></el-table-column>
                <el-table-column prop="file" label="文件"></el-table-column>
                <el-table-column label="状态" width="90">
                    <template slot-scope="scope">
                        <span v-if="scope.row.status == 1">
                            <i class="el-icon-loading"></i>处理中
                        </span>
                        <span v-if="scope.row.status == 2">
                            <i class="el-icon-check"></i>成功
                        </span>
                        <span v-if="scope.row.status == 3">
                            <i class="el-icon-close"></i>失败
                        </span>
                    </template>
                </el-table-column>
                <el-table-column prop="created_at" label="创建时间" width="180" sortable="false"></el-table-column>
                <el-table-column label="操作" width="150" fixed="right">
                    <template slot-scope="scope">
                        <el-tooltip class="item" effect="dark" content="下载" placement="top" v-if="scope.row.status == 2">
                            <el-button circle type="text" size="mini" @click="down(scope.row)">
                                <img src="statics/img/mall/download.png" alt="">
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
                    <el-switch v-model="data.is_del" :active-value="1"
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
                },
                form: [],
                pageCount: 0,
                pageSize: 10,
                page: 1,
                currentPage: null,
                listLoading: false,
                btnLoading: false,

                dialog: false,
                data: {},
                rules: {
                    file: [
                        {required: true, message: '文件不能为空', trigger: 'blur'},
                    ],
                },
            };
        },
        methods: {
            submit() {
                this.$refs.data.validate((valid) => {
                    if (valid) {
                        this.btnLoading = true;
                        request({
                            params: {r: 'mall/volcengine/auc'},
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
            down(row) {
                window.open(row.result);
            },
            search() {
                this.page = 1;
                this.getList();
            },
            pagination(currentPage) {
                this.page = currentPage;
                this.getList();
            },
            getList() {
                this.listLoading = true;
                let param = Object.assign({r: 'mall/volcengine/auc', page: this.page}, this.searchData);
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
                        params: {r: 'mall/volcengine/destroy'},
                        data: {id: column.id},
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
            this.timer = setInterval(() => {
                this.getList();
            }, 5000)
        }
    });
</script>
