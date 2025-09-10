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

    .permission-tag {
        margin: 2px;
    }

    .price-info {
        font-size: 12px;
        color: #666;
    }

    .resource-info {
        font-size: 12px;
        color: #999;
    }

    .status-tag {
        cursor: pointer;
        font-weight: 500;
        border-radius: 16px;
        padding: 8px 16px;
        font-size: 13px;
        line-height: 1.4;
        min-height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .status-tag:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .status-tag:active {
        transform: translateY(0);
    }
    
    .status-enabled {
        background: linear-gradient(135deg, #67C23A 0%, #85CE61 100%);
        border: 1px solid #67C23A;
        color: #fff;
    }
    
    .status-enabled:hover {
        background: linear-gradient(135deg, #5DAF34 0%, #67C23A 100%);
        border-color: #5DAF34;
        box-shadow: 0 4px 12px rgba(103, 194, 58, 0.4);
    }
    
    .status-disabled {
        background: linear-gradient(135deg, #909399 0%, #A6A9AD 100%);
        border: 1px solid #909399;
        color: #fff;
    }
    
    .status-disabled:hover {
        background: linear-gradient(135deg, #82848A 0%, #909399 100%);
        border-color: #82848A;
        box-shadow: 0 4px 12px rgba(144, 147, 153, 0.4);
    }
    
    .status-tag i {
        margin-right: 6px;
        font-size: 14px;
    }
    
    .status-container {
        text-align: center;
        padding: 8px 0;
        min-height: 52px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .status-hint {
        font-size: 11px;
        color: #909399;
        margin-top: 6px;
        font-weight: 400;
        line-height: 1.2;
        opacity: 0.8;
    }
    
    /* 状态变化动画 */
    @keyframes statusChange {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.1);
            opacity: 0.8;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
    
    /* 加载状态样式 */
    .status-tag.toggling {
        pointer-events: none;
        opacity: 0.7;
        position: relative;
    }
    
    .status-tag.toggling::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 16px;
        height: 16px;
        margin: -8px 0 0 -8px;
        border: 2px solid transparent;
        border-top: 2px solid currentColor;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .default-tag {
        cursor: pointer;
        font-weight: bold;
        border-radius: 16px;
        padding: 8px 16px;
        animation: pulse 2s infinite;
        font-size: 13px;
        line-height: 1.4;
        min-height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(230, 162, 60, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(230, 162, 60, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(230, 162, 60, 0);
        }
    }
    
    .default-tag i {
        margin-right: 6px;
        font-size: 15px;
        color: #FFF;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    
    /* 优化默认等级标签的渐变背景 */
    .default-tag {
        background: linear-gradient(135deg, #E6A23C 0%, #F0C78A 100%);
        border: 1px solid #E6A23C;
        box-shadow: 0 2px 8px rgba(230, 162, 60, 0.3);
    }
    
    .el-button--success.el-button--mini {
        border-radius: 16px;
        font-weight: 500;
        height: 36px;
        padding: 8px 16px;
        font-size: 13px;
        line-height: 1.4;
        min-width: 90px;
    }
    
    .el-button--success.el-button--mini:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(103, 194, 58, 0.3);
        transition: all 0.3s ease;
    }
    
    .default-level-container {
        text-align: center;
        padding: 8px 0;
        min-height: 52px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .default-hint {
        font-size: 11px;
        color: #E6A23C;
        margin-top: 6px;
        font-weight: 500;
        line-height: 1.2;
    }
    
    .set-default-btn {
        transition: all 0.3s ease;
        border: 1px solid #67C23A;
        background: linear-gradient(135deg, #67C23A 0%, #85CE61 100%);
    }
    
    .set-default-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(103, 194, 58, 0.4);
        background: linear-gradient(135deg, #5DAF34 0%, #67C23A 100%);
        border-color: #5DAF34;
    }
    
    .set-default-btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 6px rgba(103, 194, 58, 0.3);
    }
    
    .el-table .el-table__cell {
        vertical-align: middle;
    }
    
    /* 确保默认等级列的内容垂直居中 */
    .el-table .el-table__cell .default-level-container,
    .el-table .el-table__cell .el-button--mini {
        margin: 0 auto;
    }
    
    /* 优化表格行高度 */
    .el-table .el-table__row {
        height: auto;
        min-height: 60px;
    }
    
    /* 添加表格行的悬停效果 */
    .el-table .el-table__row:hover {
        background-color: #F5F7FA;
        transition: background-color 0.3s ease;
    }
    
    /* 确保按钮在表格中的显示效果 */
    .el-table .el-table__cell .el-button--mini {
        display: inline-block;
        vertical-align: middle;
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

    /* 权限管理对话框样式 */
    .permission-dialog-content {
        padding: 20px;
        background: #fff;
    }

    .permission-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #EBEEF5;
    }

    .level-info {
        display: flex;
        flex-direction: column;
    }

    .level-name {
        font-size: 18px;
        font-weight: 600;
        color: #303133;
    }

    .level-desc {
        font-size: 14px;
        color: #909399;
        margin-top: 5px;
    }

    .permission-stats {
        display: flex;
        gap: 20px;
    }

    .stats-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 10px 15px;
        background: #F5F7FA;
        border-radius: 6px;
        min-width: 80px;
    }

    .stats-number {
        font-size: 20px;
        font-weight: 600;
        color: #409EFF;
        line-height: 1;
    }

    .stats-label {
        font-size: 12px;
        color: #909399;
        margin-top: 5px;
    }

    .permission-search {
        margin-bottom: 20px;
    }

    .permission-search .el-input {
        max-width: 300px;
    }

    .permission-list {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #EBEEF5;
        border-radius: 6px;
        padding: 15px;
        background: #FAFAFA;
    }

    .permission-list::-webkit-scrollbar {
        width: 6px;
    }

    .permission-list::-webkit-scrollbar-track {
        background: #F1F1F1;
        border-radius: 3px;
    }

    .permission-list::-webkit-scrollbar-thumb {
        background: #C1C1C1;
        border-radius: 3px;
    }

    .permission-list::-webkit-scrollbar-thumb:hover {
        background: #A8A8A8;
    }

    .permission-checkbox-group .el-checkbox {
        margin-bottom: 10px;
        width: 100%;
    }

    .permission-item {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        border-radius: 4px;
        transition: background-color 0.2s ease;
        border: 1px solid transparent;
        margin-bottom: 8px;
    }

    .permission-item:hover {
        background-color: #F0F9FF;
        border-color: #B3D8FF;
    }

    .permission-item.selected {
        background-color: #E1F3D8;
        border: 1px solid #67C23A;
    }

    .permission-checkbox {
        flex-grow: 1;
    }

    .permission-content {
        display: flex;
        flex-direction: column;
        margin-left: 10px;
        flex-grow: 1;
    }

    .permission-name {
        font-size: 14px;
        font-weight: 500;
        color: #303133;
        display: flex;
        align-items: center;
        margin-bottom: 4px;
    }

    .permission-name i {
        margin-right: 8px;
        font-size: 14px;
        color: #409EFF;
    }

    .permission-desc {
        font-size: 12px;
        color: #606266;
        margin-bottom: 6px;
        line-height: 1.4;
    }

    .permission-meta {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .permission-meta .el-tag {
        border-radius: 4px;
        font-size: 11px;
        padding: 2px 6px;
        height: 20px;
        line-height: 16px;
    }

    .permission-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #EBEEF5;
    }

    .permission-actions .el-button {
        margin-left: 10px;
        border-radius: 4px;
    }

    .permission-actions .el-button:first-child {
        margin-left: 0;
    }

    /* 对话框标题样式 */
    .el-dialog__header {
        background: #F5F7FA;
        border-bottom: 1px solid #EBEEF5;
        padding: 15px 20px;
    }

    .el-dialog__title {
        font-weight: 600;
        color: #303133;
        font-size: 16px;
    }

    /* 底部按钮样式 */
    .dialog-footer {
        text-align: right;
        padding: 15px 20px;
        background: #FAFAFA;
        border-top: 1px solid #EBEEF5;
    }

    .dialog-footer .el-button {
        border-radius: 4px;
        font-weight: 500;
        padding: 8px 16px;
    }

    /* 响应式设计 */
    @media (max-width: 768px) {
        .permission-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .permission-stats {
            align-self: stretch;
            justify-content: space-around;
        }
        
        .permission-actions {
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .permission-actions .el-button {
            margin-left: 0;
        }
        
        .permission-search .el-input {
            max-width: 100%;
        }
    }
</style>

<div id="app" v-cloak>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <div flex="main:justify">
                <span style="line-height: 32px;">会员等级管理</span>
                <el-button type="primary" size="small" icon="el-icon-plus" @click="$navigate({r: 'netb/member/level-edit'})">添加会员等级</el-button>
            </div>
        </div>
        <div class="table-body">
            <div class="search-container">
                <div class="search-row">
                    <div class="input-item">
                        <el-input @keyup.enter.native="search" size="small" placeholder="请输入会员名称" v-model="searchData.keyword" clearable @clear="search">
                            <el-button slot="append" icon="el-icon-search" @click="search"></el-button>
                        </el-input>
                    </div>
                    <div class="filter-group">
                        <el-select v-model="searchData.status" size="small" placeholder="状态" @change="search" clearable style="width: 120px;">
                            <el-option label="全部状态" value=""></el-option>
                            <el-option label="正常" value="0"></el-option>
                            <el-option label="禁用" value="1"></el-option>
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
                    width="80"
                    prop="id"
                    label="ID">
                </el-table-column>
                <el-table-column
                    width="150"
                    prop="name"
                    label="会员名称">
                    <template slot-scope="scope">
                        <div>
                            <div><strong>{{ scope.row.name }}</strong></div>
                            <div style="color: #999; font-size: 12px;">{{ getLanguageData(scope.row.language_data, 'en', 'name') }}</div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                    width="200"
                    prop="slogan"
                    label="宣传语">
                    <template slot-scope="scope">
                        <div>
                            <div>{{ scope.row.slogan }}</div>
                            <div style="color: #999; font-size: 12px;">{{ getLanguageData(scope.row.language_data, 'en', 'slogan') }}</div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                    width="150"
                    label="价格信息">
                    <template slot-scope="scope">
                        <div class="price-info">
                            <div>月付: ¥{{ scope.row.monthly_discount_price }}</div>
                            <div>年付: ¥{{ scope.row.yearly_discount_price }}</div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                    width="150"
                    label="资源配额">
                    <template slot-scope="scope">
                        <div class="resource-info">
                            <div>月积分: {{ scope.row.monthly_points_refresh }}</div>
                            <div>日积分: {{ scope.row.daily_points_refresh }}</div>
                            <div>存储: {{ scope.row.storage_space_mb }}MB</div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                    min-width="200"
                    label="权限">
                    <template slot-scope="scope">
                        <div>
                            <el-tag 
                                v-for="permission in scope.row.permissions" 
                                :key="permission.id"
                                size="mini"
                                class="permission-tag">
                                {{ permission.name }}
                            </el-tag>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                    width="120"
                    prop="status"
                    label="状态"
                    align="center">
                    <template slot-scope="scope">
                        <div class="status-container">
                                                    <el-tag 
                            v-if="scope.row.status == 1"
                            type="success"
                            :class="['status-tag', 'status-enabled', {'toggling': scope.row.togglingStatus}]"
                            effect="dark"
                            @click="toggleStatus(scope.row)"
                            :data-row-id="scope.row.id">
                            <i class="el-icon-check" v-if="!scope.row.togglingStatus"></i>
                            <span v-if="!scope.row.togglingStatus">启用中</span>
                        </el-tag>
                            <el-tag 
                                v-else
                                type="info"
                                :class="['status-tag', 'status-disabled', {'toggling': scope.row.togglingStatus}]"
                                effect="dark"
                                @click="toggleStatus(scope.row)"
                                :data-row-id="scope.row.id">
                                <i class="el-icon-close" v-if="!scope.row.togglingStatus"></i>
                                <span v-if="!scope.row.togglingStatus">已禁用</span>
                            </el-tag>
                            <div class="status-hint">
                                <span v-if="!scope.row.togglingStatus">
                                    {{ scope.row.status == 1 ? '点击禁用' : '点击启用' }}
                                </span>
                                <span v-else style="color: #409EFF;">
                                    处理中...
                                </span>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                    width="80"
                    prop="sort_order"
                    label="排序">
                </el-table-column>
                <el-table-column
                    width="130"
                    prop="is_default"
                    label="默认等级"
                    align="center">
                    <template slot-scope="scope">
                        <div v-if="scope.row.is_default == 1" class="default-level-container">
                            <el-tag 
                                type="warning"
                                class="default-tag"
                                effect="dark">
                                <i class="el-icon-star-on"></i>
                                默认等级
                            </el-tag>
                            <div class="default-hint">当前默认</div>
                        </div>
                        <el-button 
                            v-else
                            size="mini"
                            type="success"
                            icon="el-icon-star-off"
                            @click="setDefault(scope.row.id)"
                            :loading="scope.row.settingDefault"
                            class="set-default-btn">
                            设为默认
                        </el-button>
                    </template>
                </el-table-column>
                <el-table-column
                    width="200"
                    label="操作">
                    <template slot-scope="scope">
                        <div class="table-info">
                            <el-button 
                                type="text" 
                                @click="$navigate({r: 'netb/member/level-edit', id: scope.row.id})">
                                编辑
                            </el-button>
                            <el-button 
                                type="text" 
                                @click="managePermissions(scope.row)">
                                权限管理
                            </el-button>
                            <el-button 
                                type="text" 
                                @click="deleteLevel(scope.row.id)"
                                style="color: #F56C6C;">
                                删除
                            </el-button>
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

    <!-- 权限管理对话框 -->
    <el-dialog
        title="权限管理"
        :visible.sync="permissionDialogVisible"
        width="700px"
        :close-on-click-modal="false"
        :close-on-press-escape="false">
        <div class="permission-dialog-content">
            <div class="permission-header">
                <div class="level-info">
                    <span class="level-name">{{ currentLevel ? currentLevel.name : '' }}</span>
                    <span class="level-desc">会员等级权限配置</span>
                </div>
                <div class="permission-stats">
                    <span class="stats-item">
                        <span class="stats-number">{{ selectedPermissions.length }}</span>
                        <span class="stats-label">已选择</span>
                    </span>
                    <span class="stats-item">
                        <span class="stats-number">{{ allPermissions.length }}</span>
                        <span class="stats-label">总权限</span>
                    </span>
                </div>
            </div>
            
            <div class="permission-search">
                <el-input
                    v-model="permissionSearchKeyword"
                    placeholder="搜索权限名称"
                    prefix-icon="el-icon-search"
                    clearable
                    size="small"
                    @input="onPermissionSearch">
                </el-input>
            </div>
            
            <div class="permission-list">
                <el-checkbox-group v-model="selectedPermissions" class="permission-checkbox-group">
                    <div 
                        v-for="permission in filteredPermissions" 
                        :key="permission.id"
                        class="permission-item"
                        :class="{ 'selected': selectedPermissions.includes(permission.id) }">
                        <el-checkbox 
                            :label="permission.id"
                            class="permission-checkbox">
                            <div class="permission-content">
                                <div class="permission-name">
                                    <i class="el-icon-key"></i>
                                    {{ permission.name }}
                                </div>
                                <div class="permission-desc">{{ permission.description }}</div>
                                <div class="permission-meta">
                                    <el-tag size="mini" type="info">{{ permission.code }}</el-tag>
                                </div>
                            </div>
                        </el-checkbox>
                    </div>
                </el-checkbox-group>
            </div>
            
            <div class="permission-actions">
                <el-button @click="selectAllPermissions" size="small">全选</el-button>
                <el-button @click="clearAllPermissions" size="small">清空</el-button>
            </div>
        </div>
        
        <div slot="footer" class="dialog-footer">
            <el-button @click="permissionDialogVisible = false">取消</el-button>
            <el-button type="primary" @click="savePermissions" :loading="savingPermissions">
                保存权限设置
            </el-button>
        </div>
    </el-dialog>
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
                status: ''
            },
            permissionDialogVisible: false,
            selectedPermissions: [],
            allPermissions: [],
            currentLevel: null,
            permissionSearchKeyword: '',
            savingPermissions: false
        }
    },
    computed: {
        filteredPermissions() {
            if (!this.permissionSearchKeyword) {
                return this.allPermissions;
            }
            const keyword = this.permissionSearchKeyword.toLowerCase();
            return this.allPermissions.filter(permission => 
                permission.name.toLowerCase().includes(keyword) || 
                permission.description.toLowerCase().includes(keyword)
            );
        }
    },
    created() {
        this.getList();
        this.getAllPermissions();
    },
    methods: {
        // 安全获取多语言数据的方法
        getLanguageData(languageData, lang, field) {
            if (!languageData || typeof languageData !== 'object') {
                return '';
            }
            if (languageData[lang] && languageData[lang][field]) {
                return languageData[lang][field];
            }
            // 如果指定语言没有数据，尝试获取默认语言
            if (lang !== 'zh' && languageData['zh'] && languageData['zh'][field]) {
                return languageData['zh'][field];
            }
            return '';
        },
        
        // 获取当前语言
        getCurrentLanguage() {
            // 这里可以根据实际需求设置默认语言
            return 'zh'; // 默认中文
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
            this.listLoading = true;
            request({
                params: {
                    r: 'netb/member/level-index',
                    page: this.page,
                    keyword: this.searchData.keyword,
                    status: this.searchData.status,
                },
            }).then(response => {
                if (response.data.code === 0) {
                    this.list = response.data.data.list;
                    this.pagination = response.data.data.pagination;
                    
                    // 调试信息：检查数据结构
                    if (this.list && this.list.length > 0) {
                        console.log('会员等级列表数据结构:', this.list[0]);
                        if (this.list[0].language_data) {
                            console.log('多语言数据结构:', this.list[0].language_data);
                        }
                    }
                }
            }).catch(error => {
                console.error('获取会员等级列表失败:', error);
                this.$message.error('获取数据失败，请重试');
            }).finally(() => {
                this.listLoading = false;
            });
        },
        
        getAllPermissions() {
            request({
                params: {
                    r: 'netb/member/permission-all'
                }
            }).then(response => {
                if (response.data.code === 0) {
                    this.allPermissions = response.data.data.list;
                }
            });
        },
        
        deleteLevel(id) {
            this.$confirm('确定要删除这个会员等级吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                request({
                    method: 'POST',
                    params: {
                        r: 'netb/member/level-delete'
                    },
                    data: {
                        id: id
                    }
                }).then(response => {
                    if (response.data.code === 0) {
                        this.$message.success('删除成功');
                        this.getList();
                    } else {
                        this.$message.error(response.data.msg || '删除失败');
                    }
                });
            });
        },
        
        toggleStatus(row) {
            const action = row.status == 1 ? '禁用' : '启用';
            const confirmMessage = row.status == 1 
                ? '禁用后，该等级的用户将无法享受相关权益，确定要禁用吗？'
                : '启用后，该等级将恢复正常使用，确定要启用吗？';
            
            this.$confirm(confirmMessage, `${action}会员等级`, {
                confirmButtonText: `确定${action}`,
                cancelButtonText: '取消',
                type: 'warning',
                message: row.status == 1 
                    ? '禁用等级可能会影响现有用户的体验，请谨慎操作。'+row.id
                    : '启用等级将恢复该等级的所有功能。'
            }).then(() => {
                // 设置加载状态
                this.$set(row, 'togglingStatus', true);
                
                request({
                    method: 'POST',
                    params: {
                        r: 'netb/member/level-toggle-status'
                    },
                    data: {
                        id: row.id
                    }
                }).then(response => {
                    if (response.data.code === 0) {
                        row.status = response.data.data.status;
                        this.$message.success(response.data.msg);
                        
                        // 添加状态变化动画效果
                        this.$nextTick(() => {
                            const statusTag = document.querySelector(`[data-row-id="${row.id}"] .status-tag`);
                            if (statusTag) {
                                statusTag.style.animation = 'statusChange 0.6s ease-in-out';
                            }
                        });
                    } else {
                        this.$message.error(response.data.msg || '操作失败');
                    }
                }).catch(() => {
                    this.$message.error('操作失败，请重试');
                }).finally(() => {
                    // 清除加载状态
                    this.$set(row, 'togglingStatus', false);
                });
            });
        },
        
        setDefault(id) {
            this.$confirm('确定要设置这个会员等级为默认等级吗？', '设置默认等级', {
                confirmButtonText: '确定设置',
                cancelButtonText: '取消',
                type: 'warning',
                message: '设置后，其他等级将不再是默认等级。新用户注册时将自动分配到此等级。'
            }).then(() => {
                // 设置加载状态
                const level = this.list.find(item => item.id === id);
                if (level) {
                    this.$set(level, 'settingDefault', true);
                }
                
                request({
                    method: 'POST',
                    params: {
                        r: 'netb/member/level-set-default'
                    },
                    data: {
                        id: id
                    }
                }).then(response => {
                    if (response.data.code === 0) {
                        this.$message.success(response.data.msg);
                        this.getList();
                    } else {
                        this.$message.error(response.data.msg || '操作失败');
                    }
                }).catch(() => {
                    this.$message.error('操作失败，请重试');
                }).finally(() => {
                    // 清除加载状态
                    if (level) {
                        this.$set(level, 'settingDefault', false);
                    }
                });
            });
        },
        
        managePermissions(level) {
            this.currentLevel = level;
            this.selectedPermissions = level.permissions.map(p => p.id);
            this.permissionSearchKeyword = '';
            this.permissionDialogVisible = true;
        },
        
        savePermissions() {
            this.savingPermissions = true;
            request({
                method: 'POST',
                params: {
                    r: 'netb/member/level-set-permissions'
                },
                data: {
                    id: this.currentLevel.id,
                    permissions: this.selectedPermissions
                }
            }).then(response => {
                if (response.data.code === 0) {
                    this.$message.success(response.data.msg);
                    this.permissionDialogVisible = false;
                    this.getList();
                } else {
                    this.$message.error(response.data.msg || '权限设置失败');
                }
            }).catch(() => {
                this.$message.error('操作失败，请重试');
            }).finally(() => {
                this.savingPermissions = false;
            });
        },

        selectAllPermissions() {
            this.selectedPermissions = this.filteredPermissions.map(p => p.id);
            this.$message.success(`已选择全部 ${this.filteredPermissions.length} 个权限`);
        },

        clearAllPermissions() {
            this.selectedPermissions = [];
            this.$message.info('已清空所有权限选择');
        },
        
        // 权限搜索防抖
        onPermissionSearch() {
            // 搜索逻辑已通过计算属性实现，无需额外处理
        },
        
        // 处理表格排序变化
        changeSort(column) {
            // 这里可以添加排序逻辑
            // column 包含排序信息：column.prop (排序字段), column.order (排序方向: ascending/descending/null)
            console.log('排序变化:', column);
            
            // 如果需要根据排序重新获取数据，可以在这里调用 this.getList()
            // this.getList();
        }
    }
});
</script> 