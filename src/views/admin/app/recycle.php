<?php
/**
 * @copyright ©2022 wstianxia
 * author: wstianxia
 * Created by IntelliJ IDEA
 */
?>
<style>
    .input-item {
        display: inline-block;
        width: 250px;
        margin-bottom: 20px;
    }

    .input-item .el-input__inner {
        border-right: 0;
    }

    .input-item .el-input__inner:hover{
        border: 1px solid #dcdfe6;
        border-right: 0;
        outline: 0;
    }

    .input-item .el-input__inner:focus{
        border: 1px solid #dcdfe6;
        border-right: 0;
        outline: 0;
    }

    .input-item .el-input-group__append {
        background-color: #fff;
        border-left: 0;
        width: 10%;
        padding: 0;
    }

    .input-item .el-input-group__append .el-button {
        padding: 0;
    }

    .input-item .el-input-group__append .el-button {
        margin: 0;
    }


    #app .el-table .el-button {
        border-radius: 16px;
    }
</style>
<div id="app" v-cloak>
    <el-card shadow="never">
        <div slot="header">
            <span>回收站列表</span>
        </div>
        <div class="input-item">
            <el-input size="small" placeholder="请输入商城名称或用户名搜索" v-model="searchForm.keyword" clearable @clear="search">
                <el-button slot="append" icon="el-icon-search" @click="search"></el-button>
            </el-input>
        </div>
        <el-table v-loading="loading" border :data="list" style="width: 100%;margin-bottom: 15px">
            <!-- <el-table-column type="selection" width="35"></el-table-column> -->
            <el-table-column prop="id" label="ID" width="60"></el-table-column>
            <el-table-column prop="user.nickname" label="账户" width="150"></el-table-column>
            <el-table-column prop="name" label="商城名称"></el-table-column>
            <el-table-column label="操作" width="180">
                <template slot-scope="scope">
                    <el-button plain size="mini" type="info" @click="unsetRecycle(scope.row)">恢复</el-button>
                    <el-button plain size="mini" type="info" @click="setDelete(scope.row)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <div flex="box:last cross:center">
            <div style="visibility: hidden">
                <el-button plain type="warning" size="small">1</el-button>
                <el-button plain type="success" size="small">2</el-button>
            </div>
            <div>
                <el-pagination
                        v-if="pagination"
                        style="display: inline-block;float: right;"
                        background
                        @current-change="pageChange"
                        layout="prev, pager, next"
                        :total="pagination.totalCount">
                </el-pagination>
            </div>
        </div>
    </el-card>
</div>
<script>
    new Vue({
        el: '#app',
        data() {
            return {
                loading: false,
                list: [],
                pagination: null,
                searchForm: {
                    isRecycle: '1',
                    keyword: ''
                },
            };
        },
        created() {
            this.loadList({});
        },
        methods: {
            search() {
                this.loadList({});
            },

            loadList(params) {
                params['r'] = 'admin/mall/index';
                params['is_recycle'] = this.searchForm.isRecycle;
                params['keyword'] = this.searchForm.keyword;
                this.loading = true;
                request({
                    params: params,
                }).then(e => {
                    this.loading = false;
                    if (e.data.code === 0) {
                        this.list = e.data.data.list;
                        this.pagination = e.data.data.pagination;
                    }
                }).catch(e => {
                });
            },

            pageChange(page) {
                this.loadList({
                    page: page,
                });
            },

            unsetRecycle(row) {
                row.is_recycle = 0;
                this.update(row);
            },
            setDelete(row) {
                this.$confirm("确认删除？此操作无法恢复！", "警告", {
                    type: 'warning',
                }).then(e => {
                    request({
                        params: {
                            r: 'admin/mall/delete',
                            id: row.id
                        },
                        method: 'get'
                    }).then(e => {
                        if (e.data.code === 0) {
                            this.$message.success(e.data.msg);
                            this.loadList({});
                        } else {
                            this.$message.error(e.data.msg);
                        }
                    }).catch(e => {
                    });
                }).catch(e => {
                });
            },
            update(row) {
                this.loading = true;
                request({
                    params: {
                        r: 'admin/mall/update',
                    },
                    method: 'post',
                    data: row,
                }).then(e => {
                    if (e.data.code === 0) {
                        this.$message.success(e.data.msg);
                        this.loadList({});
                    } else {
                        this.$message.error(e.data.msg);
                    }
                }).catch(e => {
                });
            },
        }
    });
</script>