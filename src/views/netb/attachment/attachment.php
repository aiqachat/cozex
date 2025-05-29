<?php
/**
 * Created by PhpStorm.
 * User: wstianxia
 * Date: 2019/7/22
 * Time: 15:58
 * @copyright: ©2021 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */
Yii::$app->loadViewComponent('app-attachment-edit')
?>
<div id="app" v-cloak>
    <el-card shadow="never" v-loading="loading">
        <div flex style="margin-bottom: 20px">
            <app-attachment-edit @save="loadData()" submit-url="netb/attachment/attachment-create-storage"
                                 :storage-types="storageTypes">
                <el-button size="small">添加存储位置</el-button>
            </app-attachment-edit>
            <el-button size="small" style="margin-left: 20px" @click="storageShow" :loading="dialog.loading">选取账户存储</el-button>
        </div>
        <div style="margin-bottom: 10px;">
            <div>cozex创建者：{{nickname}}</div>
            <div>cozex当前上传设置：{{storage}}</div>
        </div>
        <el-table border :data="list" style="width: 100%">
            <el-table-column label="存储位置">
                <template slot-scope="scope">
                    {{storageTypes[scope.row.type]}}
                </template>
            </el-table-column>
            <el-table-column label="使用中">
                <template slot-scope="scope">
                    <el-switch :disabled="scope.row.status == 1" @change="handleEnable(scope.row)" active-value="1"
                               inactive-value="0"
                               v-model="scope.row.status"></el-switch>
                </template>
            </el-table-column>
            <el-table-column label="操作">
                <template slot-scope="scope">
                    <app-attachment-edit @save="loadData()" :item="scope.row" :storage-types="storageTypes"
                                         submit-url="netb/attachment/attachment-create-storage">
                        <el-button size="mini" type="text" circle>
                            <el-tooltip class="item" effect="dark" content="编辑" placement="top">
                                <i class="bi bi-pencil-square" style="font-size: 20px;"></i>
                            </el-tooltip>
                        </el-button>
                    </app-attachment-edit>
                </template>
            </el-table-column>
        </el-table>
        <el-dialog :visible.sync="dialog.dialogVisible" title="存储方式选择" :close-on-click-modal="false">
            <el-form ref="form" :model="dialog.form" :rules="dialog.rules" label-position="left"
                     v-if="dialog.list.length > 0">
                <el-form-item label="存储位置" prop="type">
                    <template v-for="(item, key) in dialog.list">
                        <el-radio v-model="dialog.form.id" :label="item.id" border size="medium">
                            {{storageTypes[item.type]}}
                        </el-radio>
                    </template>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" size="small" @click="handleSaveStorage('form')">保存</el-button>
                </el-form-item>
            </el-form>
            <div v-else>账户尚未设置存储方式</div>
        </el-dialog>
    </el-card>
</div>
<script>
    new Vue({
        el: '#app',
        data() {
            return {
                storageTypes: {},
                list: [],
                loading: false,
                dialog: {
                    dialogVisible: false,
                    form: {
                        id: null
                    },
                    loading: false,
                    list: [],
                    rules: {
                        id: [
                            {required: true, message: '请选择存储位置',},
                        ]
                    }
                },
                nickname: '',
                storage: ''
            };
        },
        created() {
            this.loadData();
        },
        methods: {
            loadData() {
                this.loading = true;
                this.$request({
                    params: {
                        r: 'netb/attachment/attachment',
                    }
                }).then(e => {
                    this.loading = false;
                    if (e.data.code === 0) {
                        this.storageTypes = e.data.data.storageTypes;
                        this.list = e.data.data.list;
                        this.storage = e.data.data.storage;
                        this.nickname = e.data.data.nickname;
                    } else {
                    }
                }).catch(e => {
                    this.loading = false;
                });
            },
            handleEnable(item) {
                this.$confirm('确认切换存储位置', '提示').then(e => {
                    this.$request({
                        params: {
                            r: 'netb/attachment/attachment-enable-storage',
                            id: item.id,
                        },
                    }).then(e => {
                        if (e.data.code !== 0) {
                            item.status = 0;
                        } else {
                            this.loadData();
                        }
                    }).catch(e => {
                        item.status = 0;
                    });
                }).catch(e => {
                    item.status = 0;
                });
            },
            storageShow() {
                this.dialog.loading = true;
                this.$request({
                    params: {
                        r: 'netb/attachment/account-attachment',
                    }
                }).then(e => {
                    this.dialog.loading = false;
                    if (e.data.code === 0) {
                        this.dialog.list = e.data.data.list;
                        this.dialog.dialogVisible = true;
                    } else {
                    }
                }).catch(e => {
                    this.dialog.loading = false;
                });
            },
            handleSaveStorage(formName) {
                this.$refs[formName].validate(valid => {
                    if (valid) {
                        this.$request({
                            params: {
                                r: 'netb/attachment/create-storage-from-account'
                            },
                            data: this.dialog.form,
                            method: 'post',
                        }).then(e => {
                            if (e.data.code === 0) {
                                this.$message.success('保存成功');
                                this.dialog.dialogVisible = false;
                                this.loadData();
                            } else {
                                this.$message.error(e.data.msg);
                            }
                        }).catch(e => {
                        });
                    } else {
                        this.$message.error('提交内容有误');
                    }
                });
            }
        },
    });
</script>
