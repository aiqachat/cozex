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

    .bi {
        font-size: 20px;
    }
</style>
<div id="app" v-cloak>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <span>语音技术授权</span>
            <span style="color: #a4a4a4;">
                （ 注：授权密钥和应用使用量前往火山引擎声音技术控制台查看，<a href="https://console.volcengine.com/speech/app"
                    target="_blank">语音技术</a> ）
            </span>
            <div style="float: right;margin-top: -5px">
                <el-button type="primary" @click="edit" size="small">添加语音技术授权</el-button>
            </div>
        </div>
        <div class="table-body">
            <el-table :data="form" border style="width: 100%" v-loading="listLoading">
                <el-table-column label="应用名称" prop="name" width="250">
                    <template slot-scope="scope">
                        {{scope.row.name}}
                        <el-tag v-if="scope.row.is_default == '1'" effect="dark" size="mini">默认</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="归属" width="100">
                    <template slot-scope="scope">
                        <el-tag type="primary" v-if="scope.row.type === '2'" size="small">Byteplus</el-tag>
                        <el-tag type="primary" v-else size="small">火山引擎</el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="app_id" label="APP ID"></el-table-column>
                <el-table-column label="Access Token">
                    <template slot-scope="scope">
                        <div style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">
                            {{scope.row.access_token}}</div>
                    </template>
                </el-table-column>
                <el-table-column prop="created_at" label="添加时间" width="180"></el-table-column>
                <el-table-column label="操作" width="200" fixed="right">
                    <template slot-scope="scope">
                        <div flex="">
                            <el-button v-if="scope.row.is_default != '1'" type="primary" size="mini"
                                @click="toSetDefault(scope.row)">设为默认</el-button>
                            <el-tooltip class="item" effect="dark" content="编辑" placement="top">
                                <el-button circle type="text" size="mini" @click="edit(scope.row)">
                                    <i class="bi bi-pencil-square"></i>
                                </el-button>
                            </el-tooltip>
                            <el-tooltip class="item" effect="dark" content="删除" placement="top">
                                <el-button circle type="text" size="mini" @click="destroy(scope.row)">
                                    <i class="bi bi-trash"></i>
                                </el-button>
                            </el-tooltip>
                        </div>
                    </template>
                </el-table-column>
            </el-table>
            <div flex="dir:right" style="margin-top: 20px;">
                <el-pagination @current-change="pagination" hide-on-single-page background
                    layout="total, prev, pager, next, jumper" :total="pageCount" :page-size="pageSize"
                    :current-page="currentPage"></el-pagination>
            </div>
        </div>
        <!-- 编辑 -->
        <el-dialog :title="data.id ? '编辑语音技术授权' : '添加语音技术授权'" :visible.sync="dialog" width="40%">
            <div style="color: #a4a4a4;padding: 0 0 10px 35px;">说明：在产品服务列表里面-查看服务接口认证信息（所有语音技术同一个项目相同，无需重复添加）</div>
            <el-form :model="data" label-width="120px" :rules="dataRules" ref="data">
                <el-form-item label="火山版本" prop="type">
                    <el-radio-group v-model="data.type" size="small" @change="onTypeChange">
                        <el-radio label="1" border>火山引擎</el-radio>
                        <el-radio label="2" border>Byteplus</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="对应账号" prop="key_id">
                    <div style="display: flex; align-items: center;">
                        <el-select v-model="data.key_id" placeholder="请选择密钥" size="small" style="flex: 1;">
                            <el-option v-for="item in filteredKeys" :key="item.id"
                                       :label="item.name + ' (' + item.account_id + ')'" :value="item.id">
                            </el-option>
                        </el-select>
                        <el-button type="text" @click="goToAddKey" style="padding-left: 10px;">添加密钥</el-button>
                        <el-tooltip effect="dark" content="刷新密钥列表" placement="top">
                            <el-button class="el-icon-refresh" type="text" @click="refreshKeys"
                                       style="padding-left: 5px;"></el-button>
                        </el-tooltip>
                    </div>
                </el-form-item>
                <el-form-item label="应用名称" prop="name">
                    <el-input v-model.trim="data.name" size="small"></el-input>
                </el-form-item>
                <el-form-item label="APP ID" prop="app_id">
                    <el-input v-model.trim="data.app_id" maxlength="20" show-word-limit size="small"></el-input>
                </el-form-item>
                <el-form-item label="Access Token" prop="access_token">
                    <el-input v-model.trim="data.access_token" size="small"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialog = false">取消</el-button>
                <el-button :loading="btnLoading" type="primary" @click="submit">确认</el-button>
            </div>
        </el-dialog>
    </el-card>
</div>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                form: [],
                pageCount: 0,
                pageSize: 10,
                page: 1,
                currentPage: null,
                listLoading: false,
                btnLoading: false,

                dialog: false,
                data: {},
                dataRules: {
                    name: [
                        { required: true, message: '应用名称不能为空', trigger: 'blur' },
                    ],
                    type: [
                        { required: true, message: '请选择归属版本', trigger: 'blur' },
                    ],
                    key_id: [
                        { required: true, message: '请选择密钥', trigger: 'blur' },
                    ],
                    app_id: [
                        { required: true, message: 'APPID不能为空', trigger: 'blur' },
                    ],
                    access_token: [
                        { required: true, message: 'token不能为空', trigger: 'blur' },
                    ],
                },

                keys: [],
            };
        },
        computed: {
            filteredKeys() {
                return this.keys.filter(key => key.type === this.data.type);
            }
        },
        methods: {
            toSetDefault(row) {
                this.$confirm('此操作将该应用设置为默认, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.listLoading = true;
                    request({
                        params: { r: 'netb/setting/volcengine-default' },
                        data: { id: row.id },
                        method: 'post'
                    }).then(e => {
                        this.getList()
                    }).catch(e => {
                        this.listLoading = false;
                    });
                })
            },
            edit(row) {
                this.data = {
                    type: '1',
                    key_id: null
                };
                if (row) {
                    this.data = Object.assign({}, this.data, row);
                }
                this.dialog = true;
            },
            onTypeChange() {
                // 当版本改变时，清空已选择的密钥
                this.data.key_id = null;
            },
            goToAddKey() {
                // 跳转到密钥管理页面
                window.open('?r=netb/index/volcengine', '_blank');
            },
            refreshKeys() {
                // 刷新密钥列表
                this.getKey();
            },
            destroy: function (column) {
                this.$confirm('确认删除该记录吗?', '提示', {
                    type: 'warning'
                }).then(() => {
                    this.listLoading = true;
                    request({
                        params: { r: 'netb/setting/volcengine-destroy' },
                        data: { id: column.id },
                        method: 'post'
                    }).then(e => {
                        this.getList()
                    }).catch(e => {
                        this.listLoading = false;
                    });
                }).catch(action => {
                    this.listLoading = false;
                });
            },
            submit() {
                this.$refs.data.validate((valid) => {
                    if (valid) {
                        this.btnLoading = true;
                        request({
                            params: { r: 'netb/setting/volcengine' },
                            method: 'post',
                            data: this.data,
                        }).then(e => {
                            if (e.data.code === 0) {
                                this.$message.success(e.data.msg);
                                this.dialog = false;
                                this.getList()
                            } else {
                                this.$message.error(e.data.msg);
                            }
                            this.btnLoading = false;
                        }).catch(e => {
                            this.btnLoading = false;
                        });
                    }
                });
            },
            pagination(currentPage) {
                this.page = currentPage;
                this.getList();
            },
            getList() {
                this.listLoading = true;
                request({
                    params: { r: 'netb/setting/volcengine', page: this.page },
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
            getKey() {
                request({
                    params: { r: 'netb/index/volcengine-all' },
                }).then(e => {
                    if (e.data.code === 0) {
                        this.keys = e.data.data;
                    } else {
                        this.$message.error(e.data.msg);
                    }
                });
            },
        },
        mounted: function () {
            this.page = getQuery('page') ? getQuery('page') : 1;
            this.getList();
            this.getKey(); // 获取密钥列表
        }
    });
</script>