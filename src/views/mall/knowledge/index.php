<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: chenzs
 */
Yii::$app->loadViewComponent('app-coze-choose')
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

    a {
        text-decoration: none;
    }

    .el-table__row {
        cursor: pointer;
    }
</style>
<div id="app" v-cloak>
    <app-coze-choose @account="changeAccount" @space="changeSpace"></app-coze-choose>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <span>资源库管理</span>
            <div style="float: right;margin-top: -5px">
                <el-button type="primary" @click="edit" size="small">关联资源库资源(文件夹)</el-button>
            </div>
        </div>
        <div class="table-body">
            <div class="input-item">
                <el-input @keyup.enter.native="search" size="small" placeholder="请输入名称" v-model="searchData.keyword" clearable @clear="search">
                    <el-button slot="append" icon="el-icon-search" @click="search"></el-button>
                </el-input>
            </div>
            <el-table
                    ref="multipleTable" :data="form" @sort-change="sortChange" border
                    style="width: 100%" v-loading="listLoading" @row-click="handleRowClick">
                <el-table-column
                  type="selection"
                  width="55">
                </el-table-column>
                <el-table-column label="资源(文件夹)">
                    <template slot-scope="scope">
                        <div flex="dir:left cross:center">
                            <app-image mode="aspectFill" style="margin-right: 8px;flex-shrink: 0" :src="scope.row.img"></app-image>
                            <div style="width: 100%;">
                                <div>{{scope.row.name}}</div>
                                <el-tooltip effect="dark" placement="bottom-start" :content="`${scope.row.desc}`">
                                    <div style="color: rgb(6 7 5 / 40%); font-size: 12px;">{{scope.row.desc}}</div>
                                </el-tooltip>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="dataset_id" label="资源(文件夹)ID" width="180"></el-table-column>
                <el-table-column prop="format_type" label="类型" width="80">
                    <template slot-scope="scope">
                        <el-tag v-if="scope.row.format_type == 0" size="mini" type="success">文本</el-tag>
                        <el-tag v-else-if="scope.row.format_type == 1" size="mini" type="success">表格</el-tag>
                        <el-tag v-else-if="scope.row.format_type == 2" size="mini" type="success">照片</el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="size" label="文件大小" width="100"></el-table-column>
                <el-table-column prop="updated_at" label="编辑时间" width="180" sortable="false"></el-table-column>
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
        <el-dialog title="关联资源库资源(文件夹)ID" :visible.sync="dialogLibrary" width="30%" :before-close="close">
            <el-form :model="libraryForm" label-width="120px" :rules="libraryFormRules" ref="libraryForm">
                <el-form-item label="资源(文件夹)ID" prop="dataset_id" size="small">
                    <el-input v-model.trim="libraryForm.dataset_id"></el-input>
                    <div style="color: #a4a4a4;">注意：在空间资源库-资源列表点击后网址显示的末尾 https://www.coze.cn/space/*******/knowledge/7423**** ，资源库ID为：7423****</div>
                </el-form-item>
                <el-form-item label="名称" prop="name" size="small">
                    <el-input v-model.trim="libraryForm.name" maxlength="100" show-word-limit></el-input>
                </el-form-item>
                <el-form-item label="简介" prop="desc" size="small">
                    <el-input type="textarea" v-model.trim="libraryForm.desc" maxlength="255" show-word-limit></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="close">取消</el-button>
                <el-button :loading="btnLoading" type="primary" @click="librarySubmit">确认</el-button>
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
                    account_id: null,
                    space_id: null,
                },
                form: [],
                pageCount: 0,
                pageSize: 10,
                page: 1,
                currentPage: null,
                listLoading: false,
                btnLoading: false,

                dialogLibrary: false,
                libraryForm: {},
                libraryFormRules: {
                    dataset_id: [
                        {required: true, message: '知识库ID不能为空', trigger: 'blur'},
                    ],
                },
                is_jump: true,
            };
        },
        watch: {
            'searchData.space_id': function (val, oldValue){
                this.getList();
            },
        },
        methods: {
            changeAccount(val){
                this.searchData.account_id = val;
            },
            changeSpace(val){
                this.searchData.space_id = val;
            },
            close() {
                this.dialogLibrary = false
                this.is_jump = true;
            },
            handleRowClick(row) {
                if(!this.is_jump){
                    return;
                }
                navigateTo({
                    r: 'mall/knowledge/file-list',
                    id: row.id,
                });
            },
            // 排序排列
            sortChange(row) {
                if (row.prop && row.order) {
                    this.searchData.sort_prop = row.prop;
                    this.searchData.sort_type = row.order === "descending" ? 0 : 1;
                } else {
                    this.searchData.sort_prop = '';
                    this.searchData.sort_type = '';
                }
                this.getList();
            },
            edit(row){
                if(!this.searchData.account_id){
                    this.$confirm('请先在本系统添加coze授权账号', '提示', {
                        type: 'warning'
                    }).then(() => {
                        navigateTo({r: 'mall/setting/coze'});
                    }).catch(e => {
                    });
                    return;
                }
                this.is_jump = false;
                this.libraryForm = {
                    account_id: this.searchData.account_id,
                    space_id: this.searchData.space_id,
                };
                if(row){
                    this.libraryForm = Object.assign({}, this.libraryForm, row);
                }
                this.dialogLibrary = true;
            },
            destroy: function (column) {
                this.is_jump = false;
                this.$confirm('确认删除该记录吗?', '提示', {
                    type: 'warning'
                }).then(() => {
                    this.listLoading = true;
                    request({
                        params: {r: 'mall/knowledge/destroy'},
                        data: {id: column.id},
                        method: 'post'
                    }).then(e => {
                        this.getList()
                    }).catch(e => {
                        this.listLoading = false;
                    });
                    this.is_jump = true;
                }).catch(e => {
                    this.is_jump = true;
                });
            },
            librarySubmit() {
                this.$refs.libraryForm.validate((valid) => {
                    if (valid) {
                        this.btnLoading = true;
                        request({
                            params: {r: 'mall/knowledge/edit'},
                            method: 'post',
                            data: this.libraryForm,
                        }).then(e => {
                            if (e.data.code === 0) {
                                this.getList()
                                this.dialogLibrary = false;
                            } else {
                                this.$message.error(e.data.msg);
                            }
                            this.btnLoading = false;
                        }).catch(e => {
                            this.btnLoading = false;
                        });
                        this.is_jump = true;
                    }
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
            getList() {
                if(!this.searchData.space_id){
                    return;
                }
                this.listLoading = true;
                let param = Object.assign({r: 'mall/knowledge/index', page: this.page}, this.searchData);
                request({
                    params: param,
                }).then(e => {
                    if (e.data.code === 0) {
                        this.form = e.data.data.list;
                        for(let item of this.form) {
                            item.img = '';
                            if (item.format_type === '0') {
                                item.img = 'statics/img/mall/file.png';
                            }
                            if (item.format_type === '1') {
                                item.img = 'statics/img/mall/table.png';
                            }
                            if (item.format_type === '2') {
                                item.img = 'statics/img/mall/image.png';
                            }
                        }
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
