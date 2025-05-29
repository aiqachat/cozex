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
<div id="app" v-cloak>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <span>用户端菜单管理</span>
        </div>
        <div class="menu-container">
            <el-table v-loading="loading" :data="menuList" row-key="id" border
                :tree-props="{children: 'children', hasChildren: 'hasChildren'}">
                <el-table-column label="菜单名称" min-width="300">
                    <template slot-scope="scope">
                        <div class="menu-title-container">
                            <div class="menu-title-item">
                                <div class="menu-title-label">中文：</div>
                                <el-input v-if="scope.row.isEdit && scope.row.editType === 'zh'"
                                    v-model="scope.row.meta.title" size="small" @blur="handleEdit(scope.row, 'zh')"
                                    @keyup.enter.native="handleEdit(scope.row, 'zh')">
                                </el-input>
                                <span v-else class="editable-cell"
                                    @click="handleEdit(scope.row, 'zh')">{{scope.row.meta.title}}</span>
                            </div>
                            <div class="menu-title-item">
                                <div class="menu-title-label">英文：</div>
                                <el-input v-if="scope.row.isEdit && scope.row.editType === 'en'"
                                    v-model="scope.row.meta.title_en" size="small" @blur="handleEdit(scope.row, 'en')"
                                    @keyup.enter.native="handleEdit(scope.row, 'en')">
                                </el-input>
                                <span v-else class="editable-cell"
                                    @click="handleEdit(scope.row, 'en')">{{scope.row.meta.title_en || '-'}}</span>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="排序（数值越大越靠前）" min-width="100">
                    <template slot-scope="scope">
                        <el-input-number v-model="scope.row.sort" :min="0" size="small">
                        </el-input-number>
                    </template>
                </el-table-column>
                <el-table-column label="显示状态" width="90">
                    <template slot-scope="scope">
                        <div @click.stop class="switch-container">
                            <el-switch :key="'switch-' + scope.row.id" :value="scope.row.is_show"
                                @input="(val) => toggleSwitchStatus(scope.row, val)" active-color="#409EFF"
                                inactive-color="#C0CCDA">
                            </el-switch>
                        </div>
                    </template>
                </el-table-column>
            </el-table>
            <div class="operation-buttons">
                <el-button type="primary" @click="handleSave" :loading="saving">保存所有更改</el-button>
                <el-button type="primary" @click="refreshData">刷新数据</el-button>
                <el-button @click="resetData">重置</el-button>
            </div>
        </div>
    </el-card>
</div>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                menuList: [],
                originalMenuList: [],
                loading: false,
                saving: false
            };
        },
        methods: {
            // 获取菜单列表
            getMenuList() {
                this.loading = true;
                this.$request({
                    params: {
                        r: 'netb/user/menu',
                    },
                }).then(res => {
                    if (res.data.code === 0) {
                        const processData = (data) => {
                            return data.map(item => ({
                                ...item,
                                isEdit: false,
                                sort: item.sort || 0,
                                children: item.children ? processData(item.children) : null
                            }));
                        };

                        this.menuList = processData(res.data.data.list);
                        this.originalMenuList = JSON.parse(JSON.stringify(processData(res.data.data.original)));
                    } else {
                        this.$message.error(res.data.msg || '获取菜单数据失败');
                    }
                }).catch(err => {
                    this.$message.error('网络请求失败，请稍后重试');
                }).finally(() => {
                    this.loading = false;
                });
            },

            // 刷新数据
            refreshData() {
                this.getMenuList();
            },

            // 重置数据
            resetData() {
                this.$confirm('确认重置所有更改吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.menuList = JSON.parse(JSON.stringify(this.originalMenuList));
                    this.$message.success('已重置所有更改');
                }).catch(() => { });
            },

            // 编辑菜单名称
            handleEdit(row, type) {
                if (row.isEdit) {
                    if (type === 'zh' && !row.meta.title.trim()) {
                        this.$message.warning('中文菜单名称不能为空');
                        return;
                    }
                    row.isEdit = false;
                    row.editType = null;
                } else {
                    this.closeOtherEditing(row);
                    row.isEdit = true;
                    row.editType = type;
                    this.$nextTick(() => {
                        const input = document.querySelector('.el-table__row .el-input__inner');
                        if (input) {
                            input.focus();
                        }
                    });
                }
            },

            // 关闭其他正在编辑的行
            closeOtherEditing(currentRow) {
                const closeEditing = (data) => {
                    data.forEach(item => {
                        if (item.id !== currentRow.id && item.isEdit) {
                            item.isEdit = false;
                            item.editType = null;
                        }
                        if (item.children) {
                            closeEditing(item.children);
                        }
                    });
                };
                closeEditing(this.menuList);
            },

            // 切换开关状态
            toggleSwitchStatus(row, newStatus) {
                // 设置新状态
                row.is_show = newStatus;

                // 父菜单 -> 子菜单同步
                if (row.children && row.children.length > 0) {
                    const updateChildrenStatus = (children) => {
                        children.forEach(child => {
                            child.is_show = row.is_show;
                            if (child.children) {
                                updateChildrenStatus(child.children);
                            }
                        });
                    };
                    updateChildrenStatus(row.children);
                }

                // 子菜单 -> 父菜单同步
                this.updateParentStatus(this.menuList, row);
            },

            // 查找菜单项
            findMenuById(menus, id) {
                if (!menus || !id) return null;

                for (let i = 0; i < menus.length; i++) {
                    if (menus[i].id === id) {
                        return menus[i];
                    }

                    if (menus[i].children) {
                        const found = this.findMenuById(menus[i].children, id);
                        if (found) return found;
                    }
                }

                return null;
            },

            // 更新父菜单状态
            updateParentStatus(menus, childMenu) {
                if (!childMenu || !childMenu.pid) return;

                // 查找父菜单
                const parentMenu = this.findMenuById(menus, childMenu.pid);
                if (!parentMenu || !parentMenu.children || parentMenu.children.length === 0) return;

                // 检查子菜单状态
                const anyChildShown = parentMenu.children.some(child => child.is_show);

                // 更新父菜单状态
                if (parentMenu.is_show !== anyChildShown) {
                    parentMenu.is_show = anyChildShown;
                    this.updateParentStatus(menus, parentMenu);
                }
            },

            // 处理菜单数据用于保存
            processMenuData(data) {
                return data.map(item => {
                    const menuItem = {
                        name: item.name,
                        meta: {
                            title: item.meta.title,
                            title_en: item.meta.title_en
                        },
                        sort: item.sort,
                        is_show: item.is_show
                    };
                    if (item.children && item.children.length > 0) {
                        menuItem.children = this.processMenuData(item.children);
                    }
                    return menuItem;
                });
            },

            // 保存所有更改
            handleSave() {
                this.$confirm('确认保存所有更改吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.saving = true;
                    const changedData = this.processMenuData(this.menuList);

                    this.$request({
                        params: {
                            r: 'netb/user/menu',
                        },
                        method: 'post',
                        data: {
                            menu_list: changedData
                        },
                    }).then(res => {
                        if (res.data.code === 0) {
                            this.$message.success('保存成功');
                            this.getMenuList();
                        } else {
                            this.$message.error(res.data.msg || '保存失败');
                        }
                    }).catch(err => {
                        this.$message.error('保存失败，请稍后重试');
                    }).finally(() => {
                        this.saving = false;
                    });
                }).catch(() => { });
            }
        },
        mounted: function () {
            this.getMenuList();
        }
    });
</script>
<style>
    .operation-buttons {
        margin-top: 20px;
        text-align: center;
    }

    .el-table .cell {
        display: flex !important;
        align-items: center !important;
    }

    .editable-cell {
        cursor: pointer;
        padding: 5px;
        min-height: 20px;
        width: 100%;
        transition: all 0.3s;
    }

    .editable-cell:hover {
        background-color: #f5f7fa;
        border-radius: 4px;
    }

    .menu-title-container {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .menu-title-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .menu-title-label {
        min-width: 45px;
        color: #606266;
        font-size: 13px;
    }
</style>