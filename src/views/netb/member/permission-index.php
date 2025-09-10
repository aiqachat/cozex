<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
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

    .table-info .el-button {
        padding: 0!important;
        border: 0;
        margin: 0 5px;
    }

    .input-item {
        display: inline-block;
        width: 360px;
        margin: 0;
    }

    .input-item .el-input {
        height: 32px;
    }

    .input-item .el-input__inner {
        height: 32px;
        line-height: 32px;
    }

    .search-container {
        margin-bottom: 20px;
    }

    .search-row {
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
        flex-wrap: wrap;
        gap: 15px;
    }

    .filter-group {
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .filter-group .el-select {
        flex-shrink: 0;
    }

    .filter-group .el-select .el-input__inner {
        text-align: left;
    }

    .remark {
        word-break: break-all;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 4;
        overflow: hidden;
        line-height: 25px;
        max-height: 100px;
    }

    .bi {
        font-size: 20px;
    }

    .el-alert .el-button {
        margin-left: 20px;
    }

    .el-alert__content {
        display: flex;
        align-items: center;
    }

    .table-body .el-alert__title {
        margin-top: 5px;
    }

    .el-select .el-input__inner {
        text-align: center;
    }

    .permission-type-tag {
        margin: 2px;
    }

    .status-tag {
        cursor: pointer;
    }

    .system-permission {
        background-color: #f0f9ff;
    }

    .custom-permission {
        background-color: #f0f9ff;
    }

    .permission-name {
        font-weight: 500;
        color: #303133;
    }

    .permission-code {
        font-family: 'Courier New', monospace;
        background-color: #f5f7fa;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 12px;
        color: #606266;
    }

    .permission-desc {
        color: #666;
        font-size: 12px;
        line-height: 1.4;
    }

    /* 表格响应式优化 */
    .table-info {
        width: 100%;
        overflow-x: auto;
    }

    .table-info .el-table {
        min-width: 800px;
    }

    .table-info .el-table__body-wrapper {
        overflow-x: auto;
    }

    /* 操作列按钮组样式 */
    .operation-buttons {
        display: flex;
        gap: 5px;
        justify-content: center;
        align-items: center;
    }

    /* 确保表格内容不会溢出 */
    .el-table .cell {
        word-break: break-word;
        white-space: normal;
    }

    /* 响应式设计 */
    @media (max-width: 1200px) {
        .input-item {
            width: 300px;
        }
        
        .filter-group .el-select {
            width: 100px !important;
        }
    }

    @media (max-width: 768px) {
        .search-row {
            flex-direction: column;
            align-items: stretch;
        }
        
        .input-item {
            width: 100%;
            margin-bottom: 10px;
        }
        
        .filter-group {
            justify-content: space-between;
        }
        
        .table-info .el-table {
            min-width: 600px;
        }
    }
</style>

<div id="app" v-cloak>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <div flex="main:justify">
                <span style="line-height: 32px;">会员权限管理</span>
                <el-button type="primary" size="small" icon="el-icon-plus" @click="$navigate({r: 'netb/member/permission-edit'})">添加权限</el-button>
            </div>
        </div>
        <div class="table-body">
            <div class="search-container">
                <div class="search-row">
                    <div class="input-item">
                        <el-input @keyup.enter.native="search" size="small" placeholder="请输入权限名称或代码" v-model="searchData.keyword" clearable @clear="search">
                            <el-button slot="append" icon="el-icon-search" @click="search"></el-button>
                        </el-input>
                    </div>
                    <div class="filter-group">
                        <el-select v-model="searchData.permission_type" size="small" placeholder="权限类型" @change="search" clearable style="width: 120px; margin-right: 10px;">
                            <el-option label="全部类型" value=""></el-option>
                            <el-option label="系统权限" value="system"></el-option>
                            <el-option label="自定义权限" value="custom"></el-option>
                        </el-select>
                        <el-select v-model="searchData.status" size="small" placeholder="状态" @change="search" clearable style="width: 120px;">
                            <el-option label="全部状态" value=""></el-option>
                            <el-option label="启用" value="1"></el-option>
                            <el-option label="禁用" value="0"></el-option>
                        </el-select>
                    </div>
                </div>
            </div>

            <el-table
                ref="multipleTable"
                class="table-info"
                :data="list"
                border
                style="width: 100%"
                v-loading="listLoading"
                @sort-change="changeSort">
                
                <el-table-column
                    type="selection"
                    width="55">
                </el-table-column>
                
                <el-table-column
                    width="60"
                    prop="id"
                    label="ID"
                    sortable>
                </el-table-column>
                
                <el-table-column
                    min-width="180"
                    label="权限名称">
                    <template slot-scope="scope">
                        <div>
                            <div class="permission-name">{{ scope.row.name }}</div>
                            <div class="permission-desc">{{ scope.row.description }}</div>
                        </div>
                    </template>
                </el-table-column>
                
                <el-table-column
                    width="100"
                    prop="permission_type"
                    label="权限类型">
                    <template slot-scope="scope">
                        <el-tag 
                            :type="scope.row.permission_type === 'system' ? 'primary' : 'success'"
                            class="permission-type-tag">
                            {{ scope.row.permission_type === 'system' ? '系统' : '自定义' }}
                        </el-tag>
                    </template>
                </el-table-column>
                
                <el-table-column
                    min-width="140"
                    prop="code"
                    label="权限代码">
                    <template slot-scope="scope">
                        <span class="permission-code">{{ scope.row.code }}</span>
                    </template>
                </el-table-column>
                
                <el-table-column
                    width="80"
                    prop="status"
                    label="状态">
                    <template slot-scope="scope">
                        <el-tag 
                            :type="scope.row.status == 1 ? 'success' : 'info'"
                            class="status-tag"
                            @click="toggleStatus(scope.row)">
                            {{ scope.row.status == 1 ? '启用' : '禁用' }}
                        </el-tag>
                    </template>
                </el-table-column>
                
                <el-table-column
                    width="70"
                    prop="sort_order"
                    label="排序"
                    sortable>
                </el-table-column>
                
                <el-table-column
                    width="120"
                    label="操作">
                    <template slot-scope="scope">
                        <div class="operation-buttons">
                            <el-tooltip effect="dark" content="编辑" placement="top" v-if="scope.row.permission_type === 'custom'">
                                <el-button circle type="text" size="mini" @click="$navigate({r: 'netb/member/permission-edit', id: scope.row.id})">
                                    <i class="bi bi-pencil-square"></i>
                                </el-button>
                            </el-tooltip>
                            <el-tooltip effect="dark" content="删除" placement="top" v-if="scope.row.permission_type === 'custom'">
                                <el-button circle type="text" size="mini" @click="deletePermission(scope.row.id)" style="color: #F56C6C;">
                                    <i class="bi bi-trash"></i>
                                </el-button>
                            </el-tooltip>
                            <el-tooltip effect="dark" content="系统权限不可编辑" placement="top" v-if="scope.row.permission_type === 'system'">
                                <el-button circle type="text" size="mini" disabled style="color: #C0C4CC;">
                                    <i class="bi bi-shield-lock"></i>
                                </el-button>
                            </el-tooltip>
                        </div>
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
new Vue({
    el: '#app',
    data() {
        return {
            list: [],
            listLoading: false,
            pageCount: 0,
            pageSize: 10,
            page: 1,
            currentPage: null,
            searchData: {
                keyword: '',
                permission_type: '',
                status: ''
            }
        }
    },
    created() {
        this.getList();
    },
    methods: {
        search() {
            this.page = 1;
            this.getList();
        },
        pagination(currentPage) {
            this.page = currentPage;
            this.getList();
        },
        changeSort(column) {
            this.loading = true;
            if(column.order == "descending") {
                this.searchData.order = column.prop + '_desc'
            }else if (column.order == "ascending") {
                this.searchData.order = column.prop + '_asc'
            }else {
                this.searchData.order = null
            }
            this.getList();
        },
        getList() {
            this.listLoading = true;
            request({
                params: {
                    r: 'netb/member/permission-index',
                    page: this.page,
                    keyword: this.searchData.keyword,
                    permission_type: this.searchData.permission_type,
                    status: this.searchData.status,
                    sort: this.searchData.order,
                },
            }).then(response => {
                if (response.data.code === 0) {
                    this.list = response.data.data.list;
                    this.pageCount = response.data.data.pagination.total_count;
                    this.pageSize = response.data.data.pagination.pageSize;
                    this.currentPage = response.data.data.pagination.current_page;
                } else {
                    this.$message.error(response.data.msg || '获取数据失败');
                }
            }).catch(error => {
                this.$message.error('获取数据失败');
            }).finally(() => {
                this.listLoading = false;
            });
        },
        
        deletePermission(id) {
            this.$confirm('确定要删除这个权限吗？删除后无法恢复。', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                request({
                    params: {
                        r: 'netb/member/permission-delete',
                    },
                    method: 'post',
                    data: {id: id},
                }).then(response => {
                    if (response.data.code === 0) {
                        this.$message.success(response.data.msg || '删除成功');
                        this.getList();
                    } else {
                        this.$message.error(response.data.msg || '删除失败');
                    }
                }).catch(error => {
                    this.$message.error('删除失败');
                });
            });
        },
        
        toggleStatus(row) {
            this.$confirm(`确定要${row.status == 1 ? '禁用' : '启用'}这个权限吗？`, '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                request({
                    params: {
                        r: 'netb/member/permission-toggle-status',
                    },
                    method: 'post',
                    data: {id: row.id},
                }).then(response => {
                    if (response.data.code === 0) {
                        row.status = response.data.data.status;
                        this.$message.success(response.data.msg || '操作成功');
                    } else {
                        this.$message.error(response.data.msg || '操作失败');
                    }
                }).catch(error => {
                    this.$message.error('操作失败');
                });
            });
        }
    }
});
</script> 