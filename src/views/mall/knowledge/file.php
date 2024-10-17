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
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>
                    <span style="color: #409EFF;cursor: pointer" @click="$navigate({r:'mall/knowledge/index'})">资源库管理</span></el-breadcrumb-item>
                <el-breadcrumb-item>文件列表</el-breadcrumb-item>
            </el-breadcrumb>
            <div style="float: right;margin-top: -20px">
                <el-button type="primary" @click="$navigate({r:'mall/knowledge/add-file', id: id})"
                           size="small">添加内容(文件)
                </el-button>
            </div>
        </div>
        <div class="table-body">
            <el-table ref="multipleTable" :data="form" border style="width: 100%" v-loading="listLoading">
                <el-table-column
                  type="selection"
                  width="55">
                </el-table-column>
                <el-table-column label="资源文件">
                    <template slot-scope="scope">
                        <div flex="dir:left cross:center">
                            <app-image mode="aspectFill" style="margin-right: 8px;flex-shrink: 0" src="statics/img/mall/file.png"></app-image>
                            <div style="width: 100%;">
                                <div>{{scope.row.name}}</div>
                                <el-tooltip effect="dark" placement="bottom-start" :content="`${scope.row.desc}`">
                                    <div style="color: rgb(6 7 5 / 40%); font-size: 12px;">{{scope.row.desc}}</div>
                                </el-tooltip>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="update_time" label="编辑时间" width="180">
                </el-table-column>
                <el-table-column label="操作" width="150" fixed="right">
                    <template slot-scope="scope">
<!--                        <el-tooltip class="item" effect="dark" content="编辑" placement="top">-->
<!--                            <el-button circle type="text" size="mini" @click="edit(scope.row)">-->
<!--                                <img src="statics/img/mall/edit.png" alt="">-->
<!--                            </el-button>-->
<!--                        </el-tooltip>-->
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
    </el-card>
</div>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                id: getQuery('id'),
                searchData: {keyword: ''},
                form: [],
                pageCount: 0,
                pageSize: 10,
                page: 1,
                currentPage: null,
                listLoading: false,
                btnLoading: false,
            };
        },
        methods: {
            edit(row){

            },
            destroy: function (column) {
                this.$confirm('确认删除该记录吗?', '提示', {
                    type: 'warning'
                }).then(() => {
                    this.listLoading = true;
                    request({
                        params: {r: 'mall/knowledge/file-destroy'},
                        data: {document_id: column.document_id, id: this.id},
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
            pagination(currentPage) {
                this.page = currentPage;
                this.getList();
            },
            getList() {
                this.listLoading = true;
                let param = Object.assign({
                    r: 'mall/knowledge/file-list',
                    page: this.page,
                    id: this.id
                }, this.searchData);
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
        },
        mounted: function () {
            this.page = getQuery('page') ? getQuery('page') : 1;
            this.getList();
        }
    });
</script>
