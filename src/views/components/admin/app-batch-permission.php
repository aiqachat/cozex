<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */
?>
<style>
    .app-batch-permission {
        display: inline-block;
    }

    .app-batch-permission .permissions-list {
        margin-top: 20px;
    }

    .app-batch-permission .batch-remark {
        margin-top: 5px;
        color: #999999;
        font-size: 14px;
    }

    .app-batch-permission .batch-title {
        font-size: 18px;
    }

    .app-batch-permission .batch-box-left {
        width: 120px;
        border-right: 1px solid #e2e2e2;
        padding: 0 20px;
    }

    .app-batch-permission .batch-box-left div {
        padding: 5px 0;
        margin: 5px 0;
        cursor: pointer;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
    }

    .app-batch-permission .batch-div-active {
        background-color: #e2e2e2;
    }

    .app-batch-permission .el-dialog__body {
        padding: 15px 20px;
    }

    .app-batch-permission .batch-box-right {
        padding: 5px 20px;
    }

    .app-batch-permission .permissions-item {
        height: 24px;
        line-height: 24px;
        border-radius: 12px;
        padding: 0 12px;
        margin-right: 10px;
        margin-bottom: 10px;
        cursor: pointer;
        color: #999999;
        background-color: #F7F7F7;
        display: inline-block;
        font-size: 12px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .app-batch-permission .permissions-item.active {
        background-color: #F5FAFF;
        color: #57ADFF;
    }

</style>

<template id="app-batch-permission">
    <div class="app-batch-permission">
        <el-button size="small" type="primary" @click="batchSetting">批量权限设置</el-button>
        <el-dialog
                :visible.sync="dialogVisible"
                width="50%">
            <div slot="title">
                <div flex="dir:left">
                    <div class="batch-title">批量权限设置</div>
                </div>
                <!--                <div class="batch-remark">注：每次只能修改一项，修改后点击确定即可生效。如需修改多项，需多次操作。</div>-->
            </div>
            <div flex="dir:left box:first">
                <div class="batch-box-left" flex="dir:top">
                    <div v-for="(item, index) in baseBatchList"
                         :key='item.key'
                         :class="{'batch-div-active': currentBatch === item.key ? true : false}"
                         @click="currentBatch = item.key"
                         flex="main:center">
                        {{item.name}}
                    </div>
                </div>
                <div class="batch-box-right" v-loading="baseLoading">
                    <div v-if="currentBatch === 'base-permission'">
                        <el-checkbox :indeterminate="isBaseIndeterminate" v-model="baseCheckAll"
                                     @change="handleBaseCheckAllChange">全选
                        </el-checkbox>
                        <div class="permissions-list">
                            <div v-for="item in permissions" @click="handleBaseCheckedCitiesChange(item)"
                                 :key="item.name"
                                 class="permissions-item" :class="{active:item.isCheck}">
                                {{item.display_name}}
                            </div>
                        </div>
                    </div>
                    <div v-if="currentBatch === 'upload-permission'">
                        <el-checkbox :indeterminate="isUploadIndeterminate" v-model="uploadCheckAll"
                                     @change="handleUploadCheckAllChange">全选
                        </el-checkbox>
                        <div class="permissions-list">
                            <div v-for="item in storage" @click="handleUploadCheckedCitiesChange(item)" :key="item.name"
                                 class="permissions-item" :class="{active:item.isCheck}">
                                {{item.display_name}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div slot="footer">
                <el-button size="small" @click="dialogVisible = false">取消</el-button>
                <el-button size="small" :loading="btnLoading" type="primary" @click="dialogSubmit">确定</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    Vue.component('app-batch-permission', {
        template: '#app-batch-permission',
        props: {
            // 列表选中的数据
            chooseList: {
                type: Array,
                default: function () {
                    return [];
                }
            },
        },
        data() {
            return {
                btnLoading: false,
                baseBatchList: [
                    {
                        name: "基础权限",
                        key: 'base-permission',// 唯一
                    },
                    {
                        name: "上传权限",
                        key: 'upload-permission',
                    },
                ],
                dialogVisible: false,
                currentBatch: 'base-permission',
                // 基础权限
                baseLoading: false,
                permissions: [],
                isBaseIndeterminate: false,
                baseCheckAll: false,
                // 上传权限
                storage: [],
                isUploadIndeterminate: false,
                uploadCheckAll: false,
            }
        },
        methods: {
            checkChooseList() {
                if (this.chooseList.length > 0) {
                    return true;
                }
                this.$message.warning("请先勾选要设置的账户");
                return false;
            },
            // 打开批量设置框
            batchSetting() {
                let self = this;
                if (!self.checkChooseList()) {
                    return false;
                }
                self.dialogVisible = true;
                self.getPermissions();
            },
            dialogSubmit() {
                let self = this;
                self.btnLoading = true;
                request({
                    params: {
                        r: 'admin/user/batch-permission',
                    },
                    method: 'post',
                    data: {
                        form: self.getSubmitData()
                    },
                }).then(e => {
                    self.btnLoading = false;
                    if (e.data.code === 0) {
                        self.dialogVisible = false;
                        self.$message.success(e.data.msg);
                    } else {
                        self.$message.error(e.data.msg);
                    }
                }).catch(e => {
                    self.btnLoading = false;
                });
            },
            getSubmitData() {
                let self = this;
                let secondaryPermissions = {
                    attachment: [],
                };
                self.storage.forEach(function (item, index) {
                    if (item.isCheck) {
                        secondaryPermissions.attachment.push(index + 1);
                    }
                });

                let basePermission = [];
                self.permissions.forEach(function (item) {
                    if (item.isCheck) {
                        basePermission.push(item.name)
                    }
                });

                let chooseIdList = [];
                self.chooseList.forEach(function (item) {
                    chooseIdList.push(item.id)
                });

                return JSON.stringify({
                    chooseList: chooseIdList,
                    basePermission: basePermission,
                    secondaryPermissions: secondaryPermissions
                })
            },
            // 基础权限全选
            handleBaseCheckAllChange(val) {
                let self = this;
                self.permissions.forEach(function (item) {
                    item.isCheck = self.baseCheckAll;
                });
                self.isBaseIndeterminate = false;
            },
            handleBaseCheckedCitiesChange(permissionItem) {
                let checkedCount = 0;
                this.permissions.forEach(function (item) {
                    if (item.name === permissionItem.name) {
                        item.isCheck = !item.isCheck;
                    }
                    if (item.isCheck) {
                        checkedCount++
                    }
                });
                this.baseCheckAll = checkedCount === this.permissions.length;
                this.isBaseIndeterminate = checkedCount > 0 && checkedCount < this.permissions.length;
            },
            // 上传权限全选
            handleUploadCheckAllChange(val) {
                let self = this;
                self.storage.forEach(function (item) {
                    item.isCheck = self.uploadCheckAll;
                });
                self.isUploadIndeterminate = false;
            },
            handleUploadCheckedCitiesChange(storageItem) {
                let checkedCount = 0;
                this.storage.forEach(function (item) {
                    if (item.name === storageItem.name) {
                        item.isCheck = !item.isCheck;
                    }
                    if (item.isCheck) {
                        checkedCount++
                    }
                });
                this.uploadCheckAll = checkedCount === this.storage.length;
                this.isUploadIndeterminate = checkedCount > 0 && checkedCount < this.storage.length;
            },
            // 获取基础权限|上传权限
            getPermissions() {
                let self = this;
                self.baseLoading = true;
                request({
                    params: {
                        r: 'admin/user/permissions',
                        id: getQuery('id'),
                    },
                    method: 'get',
                }).then(e => {
                    if (e.data.code === 0) {
                        let mall = e.data.data.permissions.mall;
                        self.permissions = [];
                        mall.forEach(function (item) {
                            self.permissions.push({
                                display_name: item.display_name,
                                name: item.name,
                                isCheck: false
                            })
                        });
                        // 上传权限
                        self.storage = [];
                        let storageList = e.data.data.storage;
                        for (let i in storageList) {
                            self.storage.push({
                                display_name: storageList[i],
                                name: storageList[i],
                                isCheck: false,
                            })
                        }
                        self.baseLoading = false;
                    }
                }).catch(e => {
                    self.baseLoading = false;
                });
            },
        },
        created() {
        }
    })
</script>