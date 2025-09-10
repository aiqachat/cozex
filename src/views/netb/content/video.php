<?php defined('YII_ENV') or exit('Access Denied');
Yii::$app->loadViewComponent('app-content-audit', __DIR__);
?>
<style type="text/css">
    @import "<?= \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/css/table.css' ?>";
</style>
<style>
    .table-body {
        padding: 20px 20px 0 20px;
        background-color: #fff;
    }

    .filter-container {
        margin-bottom: 20px;
        padding: 15px;
        background-color: #f9f9f9;
        border-radius: 4px;
    }

    .filter-item {
        margin-right: 20px;
        display: inline-block;
        vertical-align: middle;
    }

    .filter-item .el-select {
        width: 200px;
    }

    .pagination-container {
        margin-top: 20px;
        text-align: center;
    }

    .video-player {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .action {
        display: flex;
        gap: 5px;
        flex-wrap: wrap
    }

    .action .el-button+.el-button {
        margin-left: 0;
    }
</style>
<div id="app" v-cloak>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            视频审核
        </div>
    </el-card>
    <div class="table-body">
        <div class="filter-container">
            <div class="filter-item">
                <el-select v-model="filters.user_id" filterable remote reserve-keyword placeholder="请输入用户名搜索"
                    :remote-method="searchUsers" :loading="userLoading" clearable @change="handleFilter">
                    <el-option v-for="item in userList" :key="item.user_id"
                        :label="item.nickname + '(' + item.uid + ')'" :value="item.user_id">
                    </el-option>
                </el-select>
            </div>
            <div class="filter-item">
                <el-select v-model="filters.type" placeholder="请选择类型" clearable @change="handleFilter">
                    <el-option label="即梦AI" value="1"></el-option>
                    <el-option label="火山方舟" value="2"></el-option>
                    <el-option label="Byteplus" value="3"></el-option>
                </el-select>
            </div>
            <div class="filter-item">
                <el-select v-model="filters.public" placeholder="请选择" clearable @change="handleFilter">
                    <el-option label="展示中" value="1"></el-option>
                    <el-option label="未展示" value="0"></el-option>
                    <el-option label="后台关闭" value="2"></el-option>
                </el-select>
            </div>
        </div>
        <!-- 批量操作工具栏 -->
        <div class="filter-container" style="margin-top: 10px; background-color: #f0f9eb; border: 1px solid #e1f3d8;">
            <div style="display: flex; align-items: center;">
                <div style="margin-right: 20px; font-weight: bold; color: #67c23a;">批量操作：</div>
                <el-button size="small" type="warning" @click="handleBatchDisable" :disabled="selectedIds.length === 0">批量禁用拉黑</el-button>
                <el-button size="small" type="success" @click="handleBatchShowDialog('approve')" :disabled="selectedIds.length === 0">批量审核展示</el-button>
                <el-button size="small" type="danger" @click="handleBatchDelete" :disabled="selectedIds.length === 0">批量永久删除</el-button>
                <div style="margin-left: 20px; color: #909399;">已选择 {{ selectedIds.length }} 项</div>
            </div>
        </div>
        <el-table :data="list" border style="width: 100%" v-loading="loading" @selection-change="handleSelectionChange">
            <el-table-column type="selection" width="55"></el-table-column>
            <el-table-column label="视频信息" min-width="160" align="center">
                <template slot-scope="scope">
                    <video :src="scope.row.video_url" controls preload="metadata" class="video-player"></video>
                </template>
            </el-table-column>
            <el-table-column label="提示词" min-width="200">
                <template slot-scope="scope">
                    <app-ellipsis :line="5">
                        {{ scope.row.prompt }}
                    </app-ellipsis>
                </template>
            </el-table-column>
            <el-table-column label="类型" min-width="110">
                <template slot-scope="scope">
                    <el-tag v-if="scope.row.type == 1" type="success">即梦AI</el-tag>
                    <span v-else>
                        <el-tag v-if="scope.row.is_home == 1">火山方舟</el-tag>
                        <el-tag v-else>Byteplus</el-tag>
                    </span>
                </template>
            </el-table-column>
            <el-table-column prop="created_at" label="生成日期" width="160"></el-table-column>
            <el-table-column label="用户信息" min-width="180">
                <template slot-scope="scope">
                    <div>用户名：{{ scope.row.user.nickname }}</div>
                    <div>UID：{{ scope.row.user.uid }}</div>
                    <div v-if="scope.row.user.userInfo && scope.row.user.userInfo.remark">备注：{{ scope.row.user.userInfo.remark }}</div>
                </template>
            </el-table-column>

            <!-- 使用通用内容审核组件 -->
            <app-content-audit content-type="video" @refresh-list="getList"
                @show-confirm-dialog="showConfirmDialog" ref="contentAudit"></app-content-audit>
        </el-table>
        <div class="pagination-container">
            <el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange"
                :current-page="pagination.current_page" :page-sizes="[10, 20, 30, 50]" :page-size="pagination.pageSize"
                layout="total, sizes, prev, pager, next, jumper" :total="pagination.total_count">
            </el-pagination>
        </div>
    </div>

    <!-- 审核确认弹窗 -->
    <el-dialog title="审核展示" :visible.sync="confirmDialog.visible" width="480px">
        <div style="margin-bottom: 20px; text-align: center;">
            <el-switch v-model="confirmDialog.permanent" active-text="永久保持此决定，不再次审核" inactive-text=""
                style="display: flex; justify-content: center;">
            </el-switch>
        </div>

        <div slot="footer" style="text-align: center">
            <el-button type="primary" @click="confirmAction(1)" :loading="confirmDialog.loading">同意展示</el-button>
            <el-button type="warning" @click="confirmAction(0)" :loading="confirmDialog.loading">驳回展示</el-button>
        </div>
    </el-dialog>

    <!-- 批量审核展示弹窗 -->
    <el-dialog title="批量审核展示" :visible.sync="batchShowDialog.visible" width="480px">
        <div style="margin-bottom: 20px;">
            <div style="margin-bottom: 15px;">确定要对选中的 {{ selectedIds.length }} 项内容进行批量审核展示操作吗？</div>
            <el-switch v-model="batchShowDialog.permanent" active-text="永久保持此决定，不再次审核" inactive-text=""
                style="display: flex; justify-content: center;">
            </el-switch>
        </div>

        <div slot="footer" style="text-align: center">
            <el-button @click="batchShowDialog.visible = false">取消</el-button>
            <el-button type="primary" @click="confirmBatchShow" :loading="batchShowDialog.loading">确定</el-button>
        </div>
    </el-dialog>
</div>

<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                list: [],
                loading: false,
                userLoading: false,
                userList: [],
                filters: {
                    user_id: '',
                    type: '',
                    public: ''
                },
                pagination: {
                    current_page: 1,
                    pageSize: 10,
                    total_count: 0,
                },
                confirmDialog: {
                    visible: false,
                    permanent: true,
                    currentItem: null,
                    loading: false
                },
                batchShowDialog: {
                    visible: false,
                    permanent: true,
                    loading: false
                },
                selectedIds: []
            };
        },
        methods: {
            getList() {
                this.loading = true;

                request({
                    params: Object.assign({
                        r: 'netb/content/video',
                        page: this.pagination.current_page,
                        limit: this.pagination.pageSize
                    }, this.filters),
                    method: 'get',
                }).then(e => {
                    this.loading = false;
                    if (e.data.code === 0) {
                        this.list = e.data.data.list;
                        this.pagination = e.data.data.pagination;
                    } else {
                        this.$message.error(e.data.msg);
                    }
                }).catch(e => {
                    this.loading = false;
                });
            },

            // 处理表格选择变化
            handleSelectionChange(selection) {
                this.selectedIds = selection.map(item => item.id);
            },

            // 批量禁用/拉黑
            handleBatchDisable() {
                if (this.selectedIds.length === 0) {
                    this.$message.warning('请至少选择一项');
                    return;
                }

                this.$confirm(`确定要对${this.selectedIds.length}项内容进行禁用拉黑操作吗？`, '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.$refs.contentAudit.handleBatchDisable(this.selectedIds);
                }).catch(() => {
                    // 取消操作
                });
            },

            // 批量审核展示弹窗
            handleBatchShowDialog(action) {
                if (this.selectedIds.length === 0) {
                    this.$message.warning('请至少选择一项');
                    return;
                }

                this.batchShowDialog.visible = true;
                this.batchShowDialog.permanent = true;
                this.batchShowDialog.action = action;
            },

            // 批量审核展示确认
            confirmBatchShow() {
                if (this.selectedIds.length === 0) return;

                this.batchShowDialog.loading = true;

                this.$refs.contentAudit.handleBatchShow(
                    this.selectedIds,
                    this.batchShowDialog.action,
                    this.batchShowDialog.permanent ? 1 : 0
                );

                this.batchShowDialog.loading = false;
                this.batchShowDialog.visible = false;
            },

            // 批量永久删除
            handleBatchDelete() {
                if (this.selectedIds.length === 0) {
                    this.$message.warning('请至少选择一项');
                    return;
                }

                this.$confirm(`确定要永久删除这${this.selectedIds.length}项内容吗？此操作不可恢复！`, '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.$refs.contentAudit.handleBatchDelete(this.selectedIds);
                }).catch(() => {
                    // 取消操作
                });
            },
            // 搜索用户
            searchUsers(query) {
                if (!query) {
                    this.userList = [];
                    return;
                }
                this.userLoading = true;
                request({
                    params: {
                        r: 'netb/user/index',
                        keyword: query
                    },
                    method: 'get'
                }).then(e => {
                    this.userLoading = false;
                    if (e.data.code === 0) {
                        this.userList = e.data.data.list;
                    }
                }).catch(() => {
                    this.userLoading = false;
                });
            },
            // 处理筛选
            handleFilter() {
                this.pagination.current_page = 1;
                this.getList();
            },
            // 显示确认弹窗
            showConfirmDialog(item) {
                this.confirmDialog.visible = true;
                this.confirmDialog.currentItem = item;
                this.confirmDialog.permanent = true;
            },
            // 确认操作
            confirmAction(type) {
                if (!this.confirmDialog.currentItem) return;

                this.confirmDialog.loading = true;

                const params = {
                    r: 'netb/content/video-public',
                    id: this.confirmDialog.currentItem.id,
                    action: type === 1 ? 'approve' : 'reject',
                    permanent: this.confirmDialog.permanent ? 1 : 0
                };

                request({
                    params: params,
                    method: 'get'
                }).then(e => {
                    this.confirmDialog.loading = false;
                    if (e.data.code === 0) {
                        this.$message.success('成功');
                        this.confirmDialog.visible = false;
                        this.getList();
                    } else {
                        this.$message.error(e.data.msg);
                    }
                }).catch(e => {
                    this.confirmDialog.loading = false;
                    this.$message.error('操作失败，请重试');
                });
            },

            // 分页大小改变
            handleSizeChange(val) {
                this.pagination.pageSize = val;
                this.pagination.current_page = 1;
                this.getList();
            },
            // 当前页改变
            handleCurrentChange(val) {
                this.pagination.current_page = val;
                this.getList();
            }
        },
        created() {
            this.getList();
        }
    })
</script>