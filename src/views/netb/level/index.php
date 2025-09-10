<?php
/**
 * link: https://www.wegouer.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */
?>
<style>
    .table-body {
        padding: 20px;
        background-color: #fff;
    }

    .input-item {
        display: inline-block;
        width: 250px;
        margin: 0 0 20px;
    }
</style>
<div id="app" v-cloak>
    <el-card shadow="never" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <span>用户等级</span>
            <div style="float: right; margin: -5px 0">
                <el-button type="primary" @click="edit(0)" size="small">添加等级</el-button>
            </div>
        </div>
        <div class="table-body">
            <div class="input-item">
                <el-input @keyup.enter.native="search" size="small"
                          placeholder="请输入用户名称搜索"
                          v-model="keyword"
                          clearable
                          @clear="search">
                    <el-button slot="append" icon="el-icon-search" @click="search"></el-button>
                </el-input>
            </div>
            <el-table v-loading="listLoading" :data="list" border style="width: 100%">
                <el-table-column label="等级名称" min-width="120">
                    <template slot-scope="scope">
                        <app-ellipsis :line="1">{{scope.row.name}}</app-ellipsis>
                    </template>
                </el-table-column>

                <el-table-column label="推广佣金比例">
                    <template slot-scope="scope">
                        {{scope.row.promotion_commission_ratio}}
                        <el-tag size="small">{{scope.row.promotion_status == 1 ? '开启' : '关闭'}}</el-tag>
                    </template>
                </el-table-column>

                <el-table-column label="默认" width="80">
                    <template slot-scope="scope">
                        <el-switch
                                active-value="1"
                                inactive-value="0"
                                @change="setDefault(scope.row)"
                                v-model="scope.row.is_default">
                        </el-switch>
                    </template>
                </el-table-column>

                <el-table-column label="等级启用" width="80">
                    <template slot-scope="scope">
                        <el-switch
                                active-value="1"
                                inactive-value="0"
                                @change="switchStatus(scope.row)"
                                v-model="scope.row.status">
                        </el-switch>
                    </template>
                </el-table-column>
                <el-table-column label="操作" fixed="right" width="150">
                    <template slot-scope="scope">
                        <el-button circle type="text" @click="edit(scope.row.id)">
                            <el-tooltip class="item" effect="dark" content="编辑" placement="top">
                                <i class="bi bi-pencil-square"></i>
                            </el-tooltip>
                        </el-button>
                        <el-button circle type="text" @click="destroy(scope.row, scope.$index)">
                            <el-tooltip class="item" effect="dark" content="删除" placement="top">
                                <i class="bi bi-trash"></i>
                            </el-tooltip>
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>

            <div flex="dir:right" style="margin-top: 20px;">
                <el-pagination
                        hide-on-single-page
                        @current-change="pagination"
                        background
                        layout="prev, pager, next, jumper"
                        :page-count="pageCount">
                </el-pagination>
            </div>
        </div>
    </el-card>
</div>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                list: [],
                keyword: '',
                listLoading: false,
                page: 1,
                pageCount: 0,
            };
        },
        methods: {
            search() {
                this.page = 1;
                this.getList();
            },

            pagination(currentPage) {
                let self = this;
                self.page = currentPage;
                self.getList();
            },
            getList() {
                let self = this;
                self.listLoading = true;
                request({
                    params: {
                        r: 'netb/level/index',
                        page: self.page,
                        keyword: this.keyword
                    },
                    method: 'get',
                }).then(e => {
                    self.listLoading = false;
                    self.list = e.data.data.list;
                    self.pageCount = e.data.data.pagination.page_count;
                }).catch(e => {
                    console.log(e);
                });
            },
            edit(id) {
                if (id) {
                    navigateTo({
                        r: 'netb/level/edit',
                        id: id,
                    });
                } else {
                    navigateTo({
                        r: 'netb/level/edit',
                    });
                }
            },
            switchStatus(row) {
                let self = this;
                self.listLoading = true;
                request({
                    params: {
                        r: 'netb/level/switch-status',
                    },
                    method: 'post',
                    data: {
                        id: row.id,
                    }
                }).then(e => {
                    self.listLoading = false;
                    if (e.data.code === 0) {
                        self.$message.success(e.data.msg);
                    } else {
                        self.$message.error(e.data.msg);
                    }
                    self.getList();
                }).catch(e => {
                    console.log(e);
                });
            },
            setDefault(row) {
                let self = this;
                self.listLoading = true;
                request({
                    params: {
                        r: 'netb/level/set-default',
                    },
                    method: 'post',
                    data: {id: row.id}
                }).then(e => {
                    self.listLoading = false;
                    if (e.data.code === 0) {
                        self.$message.success(e.data.msg);
                    } else {
                        self.$message.error(e.data.msg);
                    }
                    self.getList();
                }).catch(e => {
                    console.log(e);
                });
            },
            destroy(row, index) {
                let self = this;
                self.$confirm('删除该会员, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    self.listLoading = true;
                    request({
                        params: {
                            r: 'netb/level/destroy',
                        },
                        method: 'post',
                        data: {
                            id: row.id,
                        }
                    }).then(e => {
                        self.listLoading = false;
                        if (e.data.code === 0) {
                            self.$message.success(e.data.msg);
                            self.list.splice(index, 1);
                        } else {
                            self.$message.error(e.data.msg);
                        }
                    }).catch(e => {
                        console.log(e);
                    });
                }).catch(() => {
                    self.$message.info('已取消删除')
                });
            },
        },
        mounted: function () {
            this.getList();
        }
    });
</script>
