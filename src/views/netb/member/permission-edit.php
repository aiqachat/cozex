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
    
    .help-text {
        font-size: 12px;
        color: #909399;
        margin-top: 5px;
        line-height: 1.4;
    }

    .code-preview {
        font-family: 'Courier New', monospace;
        background-color: #f5f7fa;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 12px;
        color: #606266;
        border: 1px solid #e4e7ed;
        margin-top: 5px;
    }

    .permission-type-info {
        background-color: #f0f9ff;
        border: 1px solid #b3d8ff;
        border-radius: 4px;
        padding: 12px;
        margin-bottom: 15px;
    }

    .permission-type-info .el-icon-info {
        color: #409EFF;
        margin-right: 8px;
    }

    .permission-type-info .info-text {
        color: #606266;
        font-size: 13px;
        line-height: 1.5;
    }
</style>

<div id="app" v-cloak>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header" class="clearfix">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><span style="color: #409EFF;cursor: pointer" @click="$navigate({r:'netb/member/permission-index'})">会员权限管理</span></el-breadcrumb-item>
                <el-breadcrumb-item v-if="isUpdate">编辑权限</el-breadcrumb-item>
                <el-breadcrumb-item v-else>添加权限</el-breadcrumb-item>
            </el-breadcrumb>
            <span></span>
        </div>
        <div class="form-container">
            <el-form :model="form" :rules="rules" ref="form" label-width="150px" v-loading="listLoading">
                <el-tabs type="border-card" v-model="activeName">
                    <el-tab-pane label="基本信息" name="basic">
                        <div class="form-section">
                            <h3>权限信息</h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <el-form-item label="权限中文名称" prop="name">
                                        <el-input size="small" v-model="form.name" placeholder="请输入权限中文名称"></el-input>
                                    </el-form-item>
                                </div>
                                <div class="form-group">
                                    <el-form-item label="权限英文名称" prop="name">
                                        <el-input size="small" v-model="form.language_data.en.name" placeholder="请输入权限英文名称"></el-input>
                                    </el-form-item>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <el-form-item label="权限类型" prop="permission_type">
                                        <el-select size="small" v-model="form.permission_type" placeholder="请选择权限类型">
                                            <el-option label="系统权限" value="system"></el-option>
                                            <el-option label="自定义权限" value="custom"></el-option>
                                        </el-select>
                                    </el-form-item>
                                </div>
                                <div class="form-group">
                                    <el-form-item label="排序" prop="sort_order">
                                        <el-input-number size="small" v-model="form.sort_order" placeholder="排序（数字越小越靠前）"></el-input-number>
                                    </el-form-item>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <el-form-item label="权限代码" prop="code">
                                        <el-input size="small" v-model="form.code" placeholder="请输入权限代码（英文，如：custom_feature）"></el-input>
                                        <div class="help-text">权限代码必须唯一，建议使用英文和下划线</div>
                                        <div v-if="form.code" class="code-preview">
                                            代码预览: {{ form.code }}
                                        </div>
                                    </el-form-item>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <el-form-item label="权限中文描述" prop="description">
                                        <el-input size="small" type="textarea" v-model="form.description" :rows="3" placeholder="请输入权限中文描述"></el-input>
                                    </el-form-item>
                                </div>
                                <div class="form-group">
                                    <el-form-item label="权限英文描述" prop="description">
                                        <el-input size="small" type="textarea" v-model="form.language_data.en.description" :rows="3" placeholder="请输入权限英文描述"></el-input>
                                    </el-form-item>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3>状态配置</h3>

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
                                    <el-form-item label="创建时间" v-if="isUpdate">
                                        <div style="line-height: 32px; color: #909399;">{{ form.created_at || '未知' }}</div>
                                    </el-form-item>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3>权限类型说明</h3>
                            <div class="permission-type-info">
                                <i class="el-icon-info"></i>
                                <span class="info-text">
                                    <strong>系统权限：</strong>系统内置权限，不可编辑和删除，用于核心功能控制<br>
                                    <strong>自定义权限：</strong>用户自定义权限，可以编辑和删除，用于扩展功能控制
                                </span>
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
                permission_type: 'custom',
                description: '',
                code: '',
                status: 1,
                sort_order: 0,
                created_at: '',
                language_data: {
                    en: {}
                }
            },
            rules: {
                name: [
                    { required: true, message: '请输入权限名称', trigger: 'blur' }
                ],
                permission_type: [
                    { required: true, message: '请选择权限类型', trigger: 'change' }
                ],
                code: [
                    { required: true, message: '请输入权限代码', trigger: 'blur' },
                    { pattern: /^[a-z][a-z0-9_]*$/, message: '权限代码只能包含小写字母、数字和下划线，且必须以字母开头', trigger: 'blur' }
                ],
                description: [
                    { required: true, message: '请输入权限描述', trigger: 'blur' }
                ]
            },
            id: getQuery('id')
        }
    },
    created() {
        this.loadData();
    },
    methods: {
        loadData() {
            const id = this.id;
            if (id) {
                this.isUpdate = true;
                this.form.id = id;
                this.listLoading = true;
                request({
                    params: {
                        r: 'netb/member/permission-edit',
                        id: id
                    },
                }).then(response => {
                    if (response.data.code === 0) {
                        this.form = response.data.data.detail;
                        this.form.id = id;
                        if (!this.form.language_data || !this.form.language_data.en) {
                            this.form.language_data = {en : {}};
                        }
                    } else {
                        this.$message.error(response.data.msg || '加载数据失败');
                    }
                }).catch(error => {
                    this.$message.error('加载数据失败');
                }).finally(() => {
                    this.listLoading = false;
                });
            }
        },
        
        saveForm() {
            this.$refs.form.validate((valid) => {
                if (valid) {
                    this.btnLoading = true;
                    request({
                        method: 'POST',
                        params: {
                            r: 'netb/member/permission-edit',
                        },
                        data: this.form
                    }).then(response => {
                        if (response.data.code === 0) {
                            this.$message.success(response.data.msg || '保存成功');
                            this.$navigate({r: 'netb/member/permission-index'});
                        } else {
                            this.$message.error(response.data.msg || '保存失败');
                        }
                    }).catch(error => {
                        this.$message.error('保存失败');
                    }).finally(() => {
                        this.btnLoading = false;
                    });
                }
            });
        }
    },
    watch: {
        'form.name': function(newVal) {
            if (!this.form.code && newVal && this.form.permission_type === 'custom') {
                // 权限代码自动生成（当输入权限名称时）
                const code = newVal
                    .replace(/[^\u4e00-\u9fa5a-zA-Z0-9]/g, '_') // 替换特殊字符为下划线
                    .replace(/^_+|_+$/g, '') // 去除首尾下划线
                    .toLowerCase();
                this.form.code = code;
            }
        },
        'form.permission_type': function(newVal) {
            if (newVal === 'system') {
                // 系统权限时，代码不可编辑
                this.form.code = 'system_' + this.form.name.toLowerCase().replace(/[^\u4e00-\u9fa5a-zA-Z0-9]/g, '_');
            }
        }
    }
});
</script> 