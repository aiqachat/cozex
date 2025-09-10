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
    .button-item {
        margin: 12px 0;
        padding: 9px 25px;
    }

    .form-container {
        padding: 20px;
        background-color: #fff;
    }
    
    .form-section {
        margin-bottom: 20px;
    }
    
    .form-section h3 {
        margin: 0 0 15px 0;
        color: #409EFF;
        font-size: 16px;
        font-weight: 500;
    }
    
    .form-row {
        display: flex;
        margin-bottom: 15px;
    }
    
    .form-group {
        flex: 1;
        margin-right: 20px;
    }
    
    .form-group:last-child {
        margin-right: 0;
    }
    
    .permission-group {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 12px;
        margin-top: 15px;
    }
    
    .permission-item {
        border: 1px solid #e4e7ed;
        padding: 12px;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s;
        background-color: #fafafa;
        position: relative;
    }
    
    .permission-item:hover {
        border-color: #409eff;
        background-color: #f0f9ff;
        box-shadow: 0 2px 8px rgba(64, 158, 255, 0.1);
    }
    
    .permission-item.selected {
        border-color: #409eff;
        background-color: #e6f7ff;
        box-shadow: 0 2px 8px rgba(64, 158, 255, 0.15);
    }
    
    .permission-item.selected::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background-color: #409eff;
        border-radius: 0 2px 2px 0;
    }
    
    .permission-name {
        font-weight: 500;
        margin-bottom: 5px;
        color: #303133;
        font-size: 14px;
    }
    
    .permission-desc {
        color: #666;
        font-size: 12px;
        line-height: 1.4;
        margin-top: 5px;
    }
    
    .permission-checkbox {
        margin-right: 8px;
        margin-bottom: 8px;
    }
    
    .permission-item .el-checkbox {
        margin-bottom: 0;
    }
    
    .permission-item .el-checkbox__input.is-checked .el-checkbox__inner {
        background-color: #409eff;
        border-color: #409eff;
    }
</style>

<div id="app" v-cloak>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header" class="clearfix">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><span style="color: #409EFF;cursor: pointer" @click="$navigate({r:'netb/member/level-index'})">会员等级管理</span></el-breadcrumb-item>
                <el-breadcrumb-item v-if="isUpdate">编辑会员等级</el-breadcrumb-item>
                <el-breadcrumb-item v-else>添加会员等级</el-breadcrumb-item>
            </el-breadcrumb>
            <span></span>
        </div>
        <div class="form-container">
            <el-form :model="form" :rules="rules" ref="form" label-width="150px" v-loading="listLoading">
                <el-tabs type="border-card" v-model="activeName">
                    <el-tab-pane label="基础信息" name="basic">
                        <div class="form-section">
                            <h3>会员名称</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <el-form-item label="中文名称" prop="name">
                                        <el-input size="small" v-model="form.name" placeholder="请输入中文名称"></el-input>
                                    </el-form-item>
                                </div>
                                <div class="form-group">
                                    <el-form-item label="英文名称" prop="name">
                                        <el-input size="small" v-model="form.language_data.en.name" placeholder="请输入英文名称"></el-input>
                                    </el-form-item>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <el-form-item label="中文宣传语" prop="slogan">
                                        <el-input size="small" type="textarea" v-model="form.slogan" :rows="3" placeholder="请输入中文宣传语"></el-input>
                                    </el-form-item>
                                </div>
                                <div class="form-group">
                                    <el-form-item label="英文宣传语" prop="slogan">
                                        <el-input size="small" type="textarea" v-model="form.language_data.en.slogan" :rows="3" placeholder="请输入英文宣传语"></el-input>
                                    </el-form-item>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3>价格设置</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <el-form-item label="月付价" prop="monthly_price">
                                        <el-input-number size="small" v-model="form.monthly_price" :precision="2" :step="0.01" placeholder="月付价"></el-input-number>
                                    </el-form-item>
                                </div>
                                <div class="form-group">
                                    <el-form-item label="月付优惠价" prop="monthly_discount_price">
                                        <el-input-number size="small" v-model="form.monthly_discount_price" :precision="2" :step="0.01" placeholder="月付优惠价"></el-input-number>
                                    </el-form-item>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <el-form-item label="年付价" prop="yearly_price">
                                        <el-input-number size="small" v-model="form.yearly_price" :precision="2" :step="0.01" placeholder="年付价"></el-input-number>
                                    </el-form-item>
                                </div>
                                <div class="form-group">
                                    <el-form-item label="年付优惠价" prop="yearly_discount_price">
                                        <el-input-number size="small" v-model="form.yearly_discount_price" :precision="2" :step="0.01" placeholder="年付优惠价"></el-input-number>
                                    </el-form-item>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3>资源配额</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <el-form-item label="每月积分刷新" prop="monthly_points_refresh">
                                        <el-input-number size="small" v-model="form.monthly_points_refresh" placeholder="每月积分刷新(Token)"></el-input-number>
                                    </el-form-item>
                                </div>
                                <div class="form-group">
                                    <el-form-item label="每日积分刷新" prop="daily_points_refresh">
                                        <el-input-number size="small" v-model="form.daily_points_refresh" placeholder="每日积分刷新(Token)"></el-input-number>
                                    </el-form-item>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <el-form-item label="存储空间大小" prop="storage_space_mb">
                                        <el-input-number size="small" v-model="form.storage_space_mb" placeholder="存储空间大小(MB)"></el-input-number>
                                    </el-form-item>
                                </div>
                                <div class="form-group">
                                    <el-form-item label="排序" prop="sort_order">
                                        <el-input-number size="small" v-model="form.sort_order" placeholder="排序"></el-input-number>
                                    </el-form-item>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3>状态设置</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <el-form-item label="状态" prop="status">
                                        <el-select size="small" v-model="form.status" placeholder="请选择状态">
                                            <el-option label="启用" :value="1"></el-option>
                                            <el-option label="禁用" :value="0"></el-option>
                                        </el-select>
                                    </el-form-item>
                                </div>
                                <div class="form-group">
                                    <el-form-item label="是否默认等级" prop="is_default">
                                        <el-select size="small" v-model="form.is_default" placeholder="请选择">
                                            <el-option label="否" :value="0"></el-option>
                                            <el-option label="是" :value="1"></el-option>
                                        </el-select>
                                    </el-form-item>
                                </div>
                            </div>
                        </div>
                    </el-tab-pane>

                    <el-tab-pane label="权限设置" name="permissions">
                        <div class="form-section">
                            <h3>会员权限支持</h3>
                            <div class="permission-group">
                                <div 
                                    v-for="permission in allPermissions" 
                                    :key="permission.id"
                                    class="permission-item"
                                    :class="{ selected: selectedPermissions.includes(String(permission.id)) }"
                                    @click="togglePermission(permission.id)">
                                    <el-checkbox 
                                        :value="selectedPermissions.includes(String(permission.id))"
                                        @change="(checked) => handlePermissionChange(permission.id, checked)"
                                        class="permission-checkbox">
                                    </el-checkbox>
                                    <div class="permission-name">{{ permission.name }}</div>
                                    <div class="permission-desc">{{ permission.description }}</div>
                                </div>
                            </div>
                        </div>
                    </el-tab-pane>
                </el-tabs>
            </el-form>
        </div>
        <el-button class="button-item" :loading="btnLoading" type="primary" @click="saveForm">保存</el-button>
    </el-card>
</div>

<script>
new Vue({
    el: '#app',
    data() {
        return {
            activeName: 'basic',
            isUpdate: false,
            listLoading: false,
            btnLoading: false,
            form: {
                id: '',
                name: '',
                slogan: '',
                monthly_price: 0,
                monthly_discount_price: 0,
                yearly_price: 0,
                yearly_discount_price: 0,
                monthly_points_refresh: 0,
                daily_points_refresh: 0,
                storage_space_mb: 0,
                status: 1,
                sort_order: 0,
                is_default: 0,
                language_data: {}
            },
            rules: {
                name: [
                    { required: true, message: '请输入会员等级名称', trigger: 'blur' }
                ]
            },
            id: getQuery('id'),
            allPermissions: [],
            selectedPermissions: []
        }
    },
    created() {
        this.loadData();
    },
    methods: {
        ensureLanguageDataStructure() {
            // 确保 language_data 结构正确
            if (!this.form.language_data) {
                this.$set(this.form, 'language_data', {en: {}});
            }
            if (!this.form.language_data.en) {
                this.$set(this.form.language_data, 'en', {});
            }
            if (!this.form.language_data.en.name) {
                this.$set(this.form.language_data.en, 'name', '');
            }
            if (!this.form.language_data.en.slogan) {
                this.$set(this.form.language_data.en, 'slogan', '');
            }
        },
        
        loadData() {
            const id = this.id;
            if (id) {
                this.isUpdate = true;
                this.form.id = id;
                this.listLoading = true;
                // 先加载权限列表，再加载会员等级数据
                Promise.all([
                    this.getAllPermissions(),
                    this.getLevelDetail(id)
                ]).then(() => {
                    this.listLoading = false;
                }).catch(error => {
                    console.error('加载数据失败:', error);
                    this.$message.error('加载数据失败');
                    this.listLoading = false;
                });
            } else {
                // 新增时只需要加载权限列表，并确保数据结构正确
                this.ensureLanguageDataStructure();
                this.getAllPermissions().catch(error => {
                    console.error('加载权限列表失败:', error);
                    // 即使权限列表加载失败，页面也应该能正常显示
                    this.allPermissions = [];
                });
            }
        },
        
        getLevelDetail(id) {
            return request({
                params: {
                    r: 'netb/member/level-edit',
                    id: id
                },
            }).then(response => {
                if (response.data.code === 0) {
                    this.form = response.data.data.detail;
                    // 确保 id 字段被正确设置
                    this.form.id = id;

                    // 确保 language_data 结构正确
                    this.ensureLanguageDataStructure();
                    
                    // 确保权限数据正确加载，转换为字符串类型进行比较
                    if (response.data.data.detail.permissions && Array.isArray(response.data.data.detail.permissions)) {
                        this.selectedPermissions = response.data.data.detail.permissions.map(p => String(p.id));
                    } else {
                        this.selectedPermissions = [];
                    }
                } else {
                    this.$message.error(response.data.msg || '加载数据失败');
                }
            });
        },
        
        getAllPermissions() {
            return request({
                params: {
                    r: 'netb/member/permission-all',
                },
            }).then(response => {
                if (response.data.code === 0) {
                    this.allPermissions = response.data.data.list;
                }
            }).catch(error => {
                this.$message.error('获取权限列表失败');
            });
        },
        
        handlePermissionChange(permissionId, checked) {
            const permissionIdStr = String(permissionId);
            if (checked) {
                if (!this.selectedPermissions.includes(permissionIdStr)) {
                    this.selectedPermissions.push(permissionIdStr);
                }
            } else {
                const index = this.selectedPermissions.indexOf(permissionIdStr);
                if (index > -1) {
                    this.selectedPermissions.splice(index, 1);
                }
            }
        },
        
        togglePermission(permissionId) {
            const permissionIdStr = String(permissionId);
            const index = this.selectedPermissions.indexOf(permissionIdStr);
            if (index > -1) {
                this.selectedPermissions.splice(index, 1);
            } else {
                this.selectedPermissions.push(permissionIdStr);
            }
        },
        
        saveForm() {
            this.$refs.form.validate((valid) => {
                if (valid) {
                    // 确保 language_data 结构完整
                    this.ensureLanguageDataStructure();
                    
                    this.btnLoading = true;
                    const data = {...this.form, permissions: this.selectedPermissions};
                    request({
                        method: 'POST',
                        params: {
                            r: 'netb/member/level-edit',
                        },
                        data: data
                    }).then(response => {
                        if (response.data.code === 0) {
                            this.$message.success(response.data.msg || '保存成功');
                            this.$navigate({r: 'netb/member/level-index'});
                        } else {
                            this.$message.error(response.data.msg || '保存失败');
                        }
                    }).catch(error => {
                        console.error('保存失败:', error);
                        this.$message.error('保存失败');
                    }).finally(() => {
                        this.btnLoading = false;
                    });
                }
            });
        }
    }
});
</script> 