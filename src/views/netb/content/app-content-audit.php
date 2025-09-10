<?php
/**
 * 内容审核通用组件 - 广场状态、资源状态和操作按钮
 * author: AI Assistant
 * Created by RIPER-5 Protocol
 */
?>
<template id="app-content-audit">
    <div class="content-audit-container">
        <!-- 广场状态列 -->
        <el-table-column label="广场状态" min-width="120">
            <template slot-scope="scope">
                <el-tag v-if="scope.row.is_user_public == '0' || scope.row.is_permanent_public == '2'"
                    type="warning">不展示</el-tag>
                <template v-else-if="scope.row.is_user_public == '1'">
                    <el-tag v-if="scope.row.is_admin_public == '0' && scope.row.is_permanent_public == '3'"
                        type="info">后台关闭</el-tag>
                    <el-tag v-else-if="scope.row.is_admin_public == '0'" type="primary">请求展示</el-tag>
                    <el-tag v-else type="success">展示中</el-tag>
                </template>
            </template>
        </el-table-column>

        <!-- 资源状态列 -->
        <el-table-column label="资源状态" min-width="110">
            <template slot-scope="scope">
                <el-tag v-if="scope.row.is_delete == '1'" type="danger">禁用拉黑</el-tag>
                <el-tag v-else type="success">正常</el-tag>
            </template>
        </el-table-column>

        <!-- 操作按钮列 -->
        <el-table-column label="操作" min-width="120" fixed="right">
            <template slot-scope="scope">
                <div class="action">
                    <el-button size="mini" type="success" @click="handleDisable(scope.row)">{{ scope.row.is_delete ==
                        '0' ? '禁用拉黑' : '恢复正常'}}</el-button>
                    <template
                        v-if="scope.row.is_user_public == '1' && scope.row.is_admin_public == '0' && scope.row.is_permanent_public == '0'">
                        <el-button size="mini" type="primary"
                            @click="handleShowConfirmDialog(scope.row)">审核展示</el-button>
                    </template>
                    <template v-else-if="scope.row.is_user_public == '1'">
                        <el-button size="mini" type="primary" @click="handleShowSubmit(scope.row)"
                            v-if="scope.row.is_admin_public == '1'">关闭展示</el-button>
                        <el-button size="mini" type="primary" @click="handleShowSubmit(scope.row)"
                            v-else>恢复展示</el-button>
                    </template>
                    <el-button size="mini" type="danger" @click="handleDelete(scope.row)">永久删除</el-button>
                </div>
            </template>
        </el-table-column>
    </div>
</template>

<script>
    Vue.component('app-content-audit', {
        template: '#app-content-audit',
        props: {
            // 内容类型：image 或 video
            contentType: {
                type: String,
                required: true,
                validator: function (value) {
                    return ['image', 'video'].indexOf(value) !== -1
                }
            }
        },
        data() {
            return {}
        },
        methods: {
            // 禁用/恢复操作
            handleDisable(item) {
                const endpoint = this.contentType === 'image' ? 'netb/content/image-disable' : 'netb/content/video-disable';

                request({
                    params: {
                        r: endpoint,
                        id: item.id
                    },
                    method: 'get'
                }).then(e => {
                    if (e.data.code === 0) {
                        this.$emit('refresh-list');
                    }
                });
            },

            // 批量禁用/恢复操作
            handleBatchDisable(ids) {
                const endpoint = this.contentType === 'image' ? 'netb/content/image-batch-disable' : 'netb/content/video-batch-disable';

                request({
                    params: {
                        r: endpoint,
                        ids: ids
                    },
                    method: 'get'
                }).then(e => {
                    if (e.data.code === 0) {
                        this.$emit('refresh-list');
                        this.$message.success('操作成功');
                    } else {
                        this.$message.error(e.data.msg);
                    }
                });
            },

            // 显示确认弹窗
            handleShowConfirmDialog(item) {
                this.$emit('show-confirm-dialog', item);
            },

            // 批量审核展示操作
            handleBatchShow(ids, action, permanent) {
                const endpoint = this.contentType === 'image' ? 'netb/content/image-batch-public' : 'netb/content/video-batch-public';

                request({
                    params: {
                        r: endpoint,
                        ids: ids,
                        action: action,
                        permanent: permanent
                    },
                    method: 'get'
                }).then(e => {
                    if (e.data.code === 0) {
                        this.$emit('refresh-list');
                        this.$message.success('操作成功');
                    } else {
                        this.$message.error(e.data.msg);
                    }
                });
            },

            // 展示/关闭展示操作
            handleShowSubmit(item) {
                const endpoint = this.contentType === 'image' ? 'netb/content/image-show' : 'netb/content/video-show';

                request({
                    params: {
                        r: endpoint,
                        id: item.id
                    },
                    method: 'get'
                }).then(e => {
                    if (e.data.code === 0) {
                        this.$emit('refresh-list');
                    }
                });
            },

            // 永久删除操作
            handleDelete(item) {
                const endpoint = this.contentType === 'image' ? 'netb/content/image-del' : 'netb/content/video-del';

                request({
                    params: {
                        r: endpoint,
                        id: item.id
                    },
                    method: 'get'
                }).then(e => {
                    if (e.data.code === 0) {
                        this.$emit('refresh-list');
                    }
                });
            },

            // 批量永久删除操作
            handleBatchDelete(ids) {
                const endpoint = this.contentType === 'image' ? 'netb/content/image-batch-del' : 'netb/content/video-batch-del';

                request({
                    params: {
                        r: endpoint,
                        ids: ids
                    },
                    method: 'get'
                }).then(e => {
                    if (e.data.code === 0) {
                        this.$emit('refresh-list');
                        this.$message.success('删除成功');
                    } else {
                        this.$message.error(e.data.msg);
                    }
                });
            }
        }
    });
</script>