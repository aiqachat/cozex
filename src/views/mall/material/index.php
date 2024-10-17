<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */
?>
<style>
    .table-body {
        padding: 20px;
        background-color: #fff;
    }

    .material-list {
        width: 1240px;
        padding: 5px;
    }

    .material-list * {
        box-sizing: border-box;
    }

    .material-list:after {
        clear: both;
        display: block;
        content: " ";
    }

    .material-item {
        display: inline-block;
        cursor: pointer;
        position: relative;
        float: left;
        width: 160px;
        height: 180px;
        margin: 7.5px;
        text-align: center;
        padding: 10px 10px 0;
    }
    .material-item.checked,
    .material-item.selected {
        box-shadow: 0 0 0 1px #1ed0ff;
        background: #daf5ff;
        border-radius: 5px;
    }

    .material-item .material-img {
        display: block;
    }

    .material-item .file-type-icon {
        width: 30px;
        height: 30px;
        border-radius: 30px;
        background: #666;
        color: #fff;
        text-align: center;
        line-height: 30px;
        font-size: 24px;
    }

    .material-upload {
        box-shadow: none;
        border: 1px dashed #b2b6bd;
        height: 140px;
        width: 140px;
        margin: 17.5px;
        padding: 0;
    }

    .material-upload i {
        font-size: 30px;
        color: #909399;
    }

    .material-upload:hover {
        box-shadow: none;
        border: 1px dashed #409EFF;
    }

    .material-upload:hover i {
        color: #409EFF;
    }

    .material-upload:active {
        border: 1px dashed #20669c;
    }

    .material-upload:active i {
        color: #20669c;
    }

    .material-dialog .group-menu {
        border-right: none;
        width: 250px;
    }

    .material-dialog .group-menu .el-menu-item {
        padding-left: 10px !important;
        padding-right: 10px;
    }

    .material-dialog .group-menu .el-menu-item .el-button {
        padding: 3px 0;
    }

    .del-material-dialog .group-menu .el-menu-item .el-button:hover {
        background: #e2e2e2;
    }

    .material-dialog .group-menu .el-menu-item .el-button i {
        margin-right: 0;
    }

    .material-simple-upload i {
        font-size: 32px;
    }

    .material-video-cover {
        background-size: 100% 100%;
        background-position: center;
    }

    .material-video-info {
        background: rgba(0, 0, 0, .35);
        color: #fff;
        position: absolute;
        left: 0;
        bottom: 0;
        padding: 1px 3px;
        font-size: 14px;
    }

    .material-dialog .material-name {
        color: #666666;
        font-size: 13px;
        margin-top: 0px;
        margin-right: auto;
        word-break: break-all;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 1;
        overflow: hidden;
    }

    .search .el-input__inner {
        border-right: 0;
    }

    .search .el-input__inner:hover {
        border: 1px solid #dcdfe6;
        border-right: 0;
        outline: 0;
    }

    .search .el-input__inner:focus {
        border: 1px solid #dcdfe6;
        border-right: 0;
        outline: 0;
    }

    .search .el-input-group__append {
        background-color: #fff;
        border-left: 0;
        width: 10%;
        padding: 0;
    }

    .search .el-input-group__append .el-button {
        padding: 0;
    }

    .search .el-input-group__append .el-button {
        margin: 0;
    }

    .box {
        border: 1px solid #e3e3e3;
    }

    .box .el-scrollbar__wrap {
        overflow-y: hidden;
    }

    /* https://github.com/ElemeFE/element/pull/15359 */
    .el-input .el-input__count .el-input__count-inner {
        background: #FFF;
        display: inline-block;
        padding: 0 5px;
        line-height: normal;
    }
</style>
<div id="app" v-cloak class="material material-dialog">
    <el-card shadow="never" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <div>
                <el-breadcrumb separator="/" style="display: inline-block">
                    <el-breadcrumb-item>
                        <span v-if="is_recycle" style="color: #409EFF;cursor: pointer"
                              @click="$navigate({r:'mall/material/index'})">素材管理</span>
                        <span v-else>素材管理</span>
                    </el-breadcrumb-item>
                    <el-breadcrumb-item v-if="is_recycle">回收站</el-breadcrumb-item>
                </el-breadcrumb>
                <div style="float: right; margin: -5px 0" v-if="!is_recycle">
                    <el-button type="primary" @click="recoverClick" size="small">回收站</el-button>
                </div>
            </div>
        </div>
        <div style="height: 0;overflow: hidden;">
            <canvas id="material-canvas" style="border: 1px solid #ccc;visibility: hidden;"></canvas>
        </div>
        <div class="table-body">
            <el-tabs v-model="tabs" @tab-click="dialogOpened">
                <el-tab-pane v-for="(v,index) in tab_list" :key="index" :label="v.label" :name="v.name">
                    <div flex="cross:center" style="margin-bottom: 12px;">
                        <div v-if="is_recycle" style="width:80px"></div>
                        <el-button size="small" v-else type="primary" @click="showAddGroup(-1)">添加分组</el-button>
                        <div style="margin-left:200px">全部</div>
                        <div flex="cross:center" style="margin-left:auto">
                            <div class="search" style="margin-right: 12px">
                                <el-input placeholder="请输入名称搜索" v-model="keyword"
                                          clearable @clear="dialogOpened"
                                          size="small"
                                          @keyup.enter.native="dialogOpened"
                                          class="input-with-select">
                                    <el-button @click="dialogOpened" slot="append" icon="el-icon-search"></el-button>
                                </el-input>
                            </div>
                            <el-checkbox v-model="selectAll"
                                         @change="selectAllChange"
                                         label="全选"
                                         size="small"
                                         style="margin-right: 12px;margin-bottom: 0"></el-checkbox>
                            <el-button v-if="is_recycle"
                                       :loading="deleteLoading"
                                       @click="deleteItems(2)"
                                       size="small"
                                       style="margin-right: 12px">还原
                            </el-button>
                            <el-button :loading="deleteLoading"
                                       @click="deleteItems(is_recycle?3:1)"
                                       size="small"
                                       style="margin-right: 12px">删除
                            </el-button>
                            <el-dropdown v-if="!is_recycle"
                                         v-loading="moveLoading"
                                         trigger="click"
                                         :split-button="true"
                                         size="small"
                                         @command="moveItems">
                                <span>移动至</span>
                                <el-dropdown-menu slot="dropdown" style="height: 250px;overflow-y:scroll">
                                    <el-dropdown-item v-for="(item, index) in groupList"
                                                      :command="index"
                                                      :key="index">
                                        {{item.name}}
                                    </el-dropdown-item>
                                </el-dropdown-menu>
                            </el-dropdown>
                        </div>
                    </div>
                    <div flex="box:first" style="margin-bottom: 10px;min-height: 68vh">
                        <div style="border: 1px solid #e3e3e3;margin-right:15px">
                            <el-menu class="group-menu"
                                     mode="vertical"
                                     v-loading="groupListLoading">
                                <el-scrollbar style="height:635px;width:100%">
                                    <el-menu-item index="all" @click="switchGroup(-1)">
                                        <i class="el-icon-tickets"></i>
                                        <span>全部</span>
                                    </el-menu-item>
                                    <template v-for="(item, index) in groupItem">
                                        <el-menu-item :index="'' + index" @click="switchGroup(index)">
                                            <div flex="dir:left box:last">
                                                <div style="overflow: hidden;text-overflow: ellipsis">
                                                    <i class="el-icon-tickets"></i>
                                                    <span>{{item.name}}</span>
                                                </div>
                                                <div v-if="is_recycle" flex="dir:left">
                                                    <el-button @click.stop="deleteGroup(index,2)" type="text">还原</el-button>
                                                    <div style="color:#409EFF;margin:0 2px">|</div>
                                                    <el-button type="text" @click.stop="deleteGroup(index,3)">删除</el-button>
                                                </div>
                                                <div v-else flex="dir:left">
                                                    <el-button type="text" @click.stop="showAddGroup(index)">编辑
                                                    </el-button>
                                                    <div style="color:#e3e3e3;margin:0 2px">|</div>
                                                    <el-button type="text" @click.stop="deleteGroup(index,1)">删除</el-button>
                                                </div>
                                            </div>
                                        </el-menu-item>
                                    </template>
                                </el-scrollbar>
                            </el-menu>
                        </div>
                        <div v-loading="loading" flex="dir:top" class="box">
                            <el-scrollbar>
                                <div class="material-list">
                                    <div v-if="!is_recycle" class="material-item material-upload">
                                        <app-upload
                                                v-loading="uploading"
                                                :disabled="uploading"
                                                @start="handleStart"
                                                @success="handleSuccess"
                                                @complete="handleComplete"
                                                :multiple="true"
                                                :max="10"
                                                :params="uploadParams"
                                                :fields="uploadFields"
                                                :accept="accept"
                                                flex="main:center cross:center"
                                                style="width: 140px;height: 140px">
                                            <div v-if="uploading">{{uploadCompleteFilesNum}}/{{uploadFilesNum}}</div>
                                            <i v-else class="el-icon-upload"></i>
                                        </app-upload>
                                    </div>
                                    <template v-for="(item, index) in attachments">
                                        <el-tooltip class="item" effect="dark" :content="item.name" placement="top"
                                                    :open-delay="1">
                                            <div :key="index" :class="'material-item'+(item.selected ?' selected':'')"
                                                 @click="selectItem(item)">
                                                <!-- 图片 -->
                                                <img v-if="item.type == 1" class="material-img"
                                                     :src="item.thumb_url"
                                                     style="width: 140px;height: 140px;">
                                                <!-- 视频 -->
                                                <div v-if="item.type == 2" class="material-img"
                                                     style="width: 140px;height: 140px;position: relative">
                                                    <div v-if="item.cover_pic_src" class="material-video-cover"
                                                         :style="'background-image: url('+item.cover_pic_src+');'"></div>
                                                    <video style="width: 0;height: 0;visibility: hidden;"
                                                           :id="'app_material_'+ _uid + '_' + index">
                                                        <source :src="item.url">
                                                    </video>
                                                    <div class="material-video-info">
                                                        <i class="el-icon-video-play"></i>
                                                        <span>{{item.duration?item.duration:'--:--'}}</span>
                                                    </div>
                                                </div>
                                                <!-- 文件 -->
                                                <div v-if="item.type == 3" class="material-img"
                                                     style="width: 140px;height: 140px;line-height: 140px;text-align: center">
                                                    <i class="file-type-icon el-icon-document"></i>
                                                </div>
                                                <!-- 名称 -->
                                                <div flex="dir:left" style="margin-top:5px">
                                                    <div class="material-name">{{item.name}}</div>
                                                    <div style="margin:0 5px">|</div>
                                                    <div>
                                                        <el-button @click.stop="showPicModel(index)" type="text"
                                                                   style="padding:0">编辑
                                                        </el-button>
                                                    </div>
                                                </div>
                                            </div>
                                        </el-tooltip>
                                    </template>
                                </div>
                            </el-scrollbar>
                            <div style="padding: 5px;text-align: right;margin-top:auto">
                                <el-pagination
                                        v-if="pagination"
                                        background
                                        @size-change="handleLoadMore"
                                        @current-change="handleLoadMore"
                                        :current-page.sync="page"
                                        :page-size="pagination.pageSize"
                                        layout="prev, pager, next, jumper"
                                        :total="pagination.totalCount">
                                </el-pagination>
                            </div>
                        </div>
                    </div>
                </el-tab-pane>

                <!-- 分类 -->
                <el-dialog append-to-body title="分组管理" :visible.sync="addGroupVisible" :close-on-click-modal="false"
                           width="30%">
                    <el-form @submit.native.prevent label-width="90px" ref="groupForm" :model="groupForm"
                             :rules="groupFormRule">
                        <el-form-item label="分组名称" prop="name" style="margin-bottom: 22px;">
                            <el-input v-model="groupForm.name" maxlength="8" show-word-limit></el-input>
                        </el-form-item>
                        <el-form-item style="text-align: right">
                            <el-button type="primary" @click="groupFormSubmit('groupForm')" :loading="groupFormLoading"
                                       size="medium">保存
                            </el-button>
                        </el-form-item>
                    </el-form>
                </el-dialog>
                <!-- 名称修改 -->
                <el-dialog append-to-body :title="reflection[tabs] + '名称修改'"
                           :visible.sync="addPicVisible" :close-on-click-modal="false"
                           width="30%">
                    <el-form @submit.native.prevent label-width="90px" ref="picForm" :model="picForm"
                             :rules="picFormRule">
                        <el-form-item :label="reflection[tabs] + '名称'" prop="name"
                                      style="margin-bottom: 22px;">
                            <el-input v-model="picForm.name"></el-input>
                        </el-form-item>
                        <el-form-item style="text-align: right">
                            <el-button type="primary" @click="picFormSubmit('picForm')" :loading="picFormLoading"
                                       size="medium">保存
                            </el-button>
                        </el-form-item>
                    </el-form>
                </el-dialog>
            </el-tabs>
        </div>
    </el-card>
</div>

<script>
    const app = new Vue({
        el: '#app',
        computed: {
            accept: {
                get() {
                    if (this.tabs === 'image') {
                        return 'image/*';
                    }
                    if (this.tabs === 'video') {
                        return 'video/*';
                    }
                    return '*/*';
                },
            },
        },
        data() {
            return {
                tab_list: [
                    {label: '图片', name: 'image'},
                    {label: '音视频', name: 'video'},
                    {label: '文件', name: 'file'},
                ],
                reflection: {
                    image: '图片',
                    video: '音视频',
                    file: '文件',
                },
                tabs: 'image',
                is_recycle: 0,
                keyword: '',

                canvas: null,
                uploading: false,
                dialogVisible: false,
                loading: true,
                loadingMore: false,
                noMore: false,
                attachments: [],
                checkedAttachments: [],
                uploadParams: {},
                uploadFields: {},
                uploadCompleteFilesNum: 0,
                uploadFilesNum: 0,
                page: 0,
                addGroupVisible: false,
                noMall: true,
                groupList: [],
                groupItem: [],
                groupListLoading: false,
                groupForm: {
                    id: null,
                    name: '',
                },
                groupFormRule: {
                    name: [
                        {required: true, message: '请填写分组名称',}
                    ],
                },
                groupFormLoading: false,
                selectAll: false,
                deleteLoading: false,
                moveLoading: false,
                currentAttachmentGroupId: null,
                video: null,
                pagination: null,

                addPicVisible: false,
                picForm: {
                    id: null,
                    name: '',
                },
                picFormRule: {
                    name: [
                        {required: true, message: '请填写名称',}
                    ],
                },
                picFormLoading: false,
            };
        },
        mounted() {
            this.dialogOpened();
        },
        methods: {
            recoverClick() {
                this.is_recycle = 1;
                this.dialogOpened();
            },
            dialogOpened() {
                this.page = 1;
                this.loading = true;
                this.loadGroups();
                this.loadList({})
            },
            deleteItems(type) {
                const itemIds = [];
                for (let i in this.attachments) {
                    if (this.attachments[i].selected) {
                        itemIds.push(this.attachments[i].id);
                    }
                }
                if (!itemIds.length) {
                    this.$message.warning('请先选择需要处理的' + this.reflection[this.tabs]);
                    return;
                }

                let title;
                switch (type) {
                    case 1:
                        title = '是否确认将选中素材放入回收站中？删除的素材可通过回收站还原';
                        break;
                    case 2:
                        title = '确认还原选择素材？';
                        break;
                    case 3:
                        title = '素材删除后将无法恢复，您确认要彻底删除所选素材吗？';
                        break;
                    default:
                        title = '';
                        break;
                }
                this.$confirm(title, '提示', {
                    type: 'warning'
                }).then(() => {
                    this.deleteLoading = true;
                    this.$request({
                        params: {
                            r: 'common/attachment/delete'
                        },
                        method: 'post',
                        data: {
                            ids: itemIds,
                            //type 1回收 2还原 3删除
                            type,
                        },
                    }).then(e => {
                        this.deleteLoading = false;
                        if (e.data.code === 0) {
                            this.$message.success(e.data.msg);
                            for (let i in itemIds) {
                                for (let j in this.attachments) {
                                    if (this.attachments[j].id == itemIds[i]) {
                                        this.attachments.splice(j, 1);
                                        break;
                                    }
                                }
                            }
                        } else {
                            this.$message.error(e.data.msg);
                        }
                    }).catch(e => {
                        this.deleteLoading = false;
                    });
                }).catch(() => {
                });
            },
            selectAllChange(value) {
                for (let i in this.attachments) {
                    this.attachments[i].selected = value;
                }
            },
            selectItem(item) {
                item.selected = item.selected ? false : true;
            },
            moveItems(index) {
                const itemIds = [];
                for (let i in this.attachments) {
                    if (this.attachments[i].selected) {
                        itemIds.push(this.attachments[i].id);
                    }
                }
                if (!itemIds.length) {
                    this.$message.warning('请先选择需要移动的' + this.reflection[this.tabs]);
                    return;
                }
                this.$confirm('确认移动所选的' + itemIds.length + '张' + this.reflection[this.tabs] + '？', '提示', {
                    type: 'warning'
                }).then(() => {
                    this.moveLoading = true;
                    this.$request({
                        params: {
                            r: 'common/attachment/move'
                        },
                        method: 'post',
                        data: {
                            ids: itemIds,
                            attachment_group_id: this.groupItem[index].id,
                        },
                    }).then(e => {
                        this.moveLoading = false;
                        if (e.data.code === 0) {
                            this.$message.success(e.data.msg);
                            this.switchGroup(index);
                        } else {
                            this.$message.error(e.data.msg);
                        }
                    }).catch(e => {
                        this.moveLoading = false;
                    });
                }).catch(() => {
                });
            },
            loadGroups() {
                this.groupListLoading = true;
                this.$request({
                    params: {
                        r: 'common/attachment/group-list',
                        is_recycle: this.is_recycle,
                        type: this.tabs,
                    },
                }).then(e => {
                    this.groupListLoading = false;
                    if (e.data.code === 0) {
                        this.noMall = e.data.data.no_mall ? e.data.data.no_mall : false;
                        this.groupItem = e.data.data.list;
                        this.groupList = e.data.data.list;
                    } else {
                        this.$message.error(e.data.msg);
                    }
                }).catch(e => {
                    this.groupListLoading = false;
                });
            },
            showAddGroup(index) {
                if (index > -1) {
                    this.groupForm.id = this.groupItem[index].id;
                    this.groupForm.name = this.groupItem[index].name;
                } else {
                    this.groupForm.id = null;
                    this.groupForm.name = '';
                }
                this.groupForm.edit_index = index;
                this.addGroupVisible = true;
            },
            deleteGroup(index, type) {
                let title;
                switch (type) {
                    case 1:
                        title = '是否确认将分组放入回收站中？删除的分组可通过回收站还原';
                        break;
                    case 2:
                        title = '确认还原选择分组？';
                        break;
                    case 3:
                        title = '分组删除后将无法恢复，您确认要彻底删除所选分组吗？';
                        break;
                    default:
                        title = '';
                        break;
                }
                this.$confirm(title, '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.$request({
                        params: {r: 'common/attachment/group-delete'},
                        method: 'POST',
                        data: {
                            id: this.groupItem[index].id,
                            type,
                        }
                    }).then(e => {
                        if (e.data.code === 0) {
                            this.$message.success(e.data.msg);
                            this.groupItem.splice(index, 1);
                        } else {
                            this.$message.error(e.data.msg);
                        }
                    }).catch(e => {
                    });
                }).catch(() => {
                });
            },
            showPicModel(index) {
                this.picForm = {
                    id: this.attachments[index].id,
                    name: this.attachments[index].name,
                    edit_index: index,
                };
                this.addPicVisible = true;
            },

            picFormSubmit(formName) {
                this.$refs[formName].validate(valid => {
                    if (valid) {
                        this.picFormLoading = true;
                        this.$request({
                            params: {
                                r: 'common/attachment/rename',
                            },
                            method: 'post',
                            data: Object.assign({}, this.picForm),
                        }).then(e => {
                            this.picFormLoading = false;
                            if (e.data.code === 0) {
                                this.$message.success(e.data.msg);
                                this.addPicVisible = false;
                                this.attachments[this.picForm.edit_index].name = this.picForm.name;
                            } else {
                                this.$message.error(e.data.msg);
                            }
                        }).catch(e => {
                            this.picFormLoading = false;
                        });
                    }
                })
            },

            groupFormSubmit(formName) {
                this.$refs[formName].validate(valid => {
                    if (valid) {
                        this.groupFormLoading = true;
                        this.$request({
                            params: {
                                r: 'common/attachment/group-update',
                            },
                            method: 'post',
                            data: Object.assign({}, this.groupForm, {'type': this.tabs}),
                        }).then(e => {
                            this.groupFormLoading = false;
                            if (e.data.code === 0) {
                                this.$message.success(e.data.msg);
                                this.addGroupVisible = false;
                                if (this.groupForm.edit_index > -1) {
                                    this.groupItem[this.groupForm.edit_index] = e.data.data;
                                } else {
                                    this.groupList.push(e.data.data);
                                }
                            } else {
                                this.$message.error(e.data.msg);
                            }
                        }).catch(e => {
                            this.groupFormLoading = false;
                        });
                    }
                })
            },
            switchGroup(index) {
                this.attachments = [];
                this.page = 0;
                this.noMore = false;
                this.loading = true;
                this.uploadParams = {
                    attachment_group_id: index > -1 ? this.groupItem[index].id : null,
                };
                this.currentAttachmentGroupId = index > -1 ? this.groupItem[index].id : null;
                this.loadList({});
            },
            loadList(params) {
                this.noMore = false;
                this.selectAll = false;
                params['r'] = 'common/attachment/list';
                params['page'] = this.page;
                params['attachment_group_id'] = this.currentAttachmentGroupId;
                params['type'] = this.tabs;
                params['is_recycle'] = this.is_recycle;
                params['keyword'] = this.keyword;
                this.$request({
                    params: params,
                }).then(e => {
                    if (e.data.code === 0) {
                        if (!e.data.data.list.length) {
                            this.noMore = true;
                        }
                        for (let i in e.data.data.list) {
                            e.data.data.list[i].checked = false;
                            e.data.data.list[i].selected = false;
                            e.data.data.list[i].duration = null;
                        }
                        //this.attachments = this.attachments.concat(e.data.data.list);
                        this.attachments = e.data.data.list;
                        this.pagination = e.data.data.pagination;
                        this.checkedAttachments = [];
                        this.loading = false;
                        this.loadingMore = false;
                        this.updateVideo();
                    } else {
                        this.$message.error(e.data.msg);
                        this.dialogVisible = false;
                    }
                }).catch(e => {
                });
            },
            confirm() {
                this.$emit('selected', this.checkedAttachments, this.params);
                this.dialogVisible = false;
                const urls = [];
                for (let i in this.checkedAttachments) {
                    urls.push(this.checkedAttachments[i].url);
                }
                for (let i in this.attachments) {
                    this.attachments[i].checked = false;
                }
                this.checkedAttachments = [];
                if (!urls.length) {
                    return;
                }
                if (this.multiple) {
                    this.$emit('input', urls);
                } else {
                    this.$emit('input', urls[0]);
                }
            },
            handleStart(files) {
                this.uploading = true;
                this.uploadFilesNum = files.length;
                this.uploadCompleteFilesNum = 0;
            },
            handleSuccess(file) {
                if (file.response && file.response.data && file.response.data.code === 0) {
                    const newItem = {
                        checked: false,
                        selected: false,
                        created_at: file.response.data.data.created_at,
                        deleted_at: file.response.data.data.deleted_at,
                        id: `${file.response.data.data.id}`,
                        is_delete: file.response.data.data.is_delete,
                        mall_id: file.response.data.data.mall_id,
                        name: file.response.data.data.name,
                        size: file.response.data.data.size,
                        storage_id: file.response.data.data.storage_id,
                        thumb_url: file.response.data.data.thumb_url,
                        type: file.response.data.data.type,
                        updated_at: file.response.data.data.updated_at,
                        url: file.response.data.data.url,
                        user_id: file.response.data.data.user_id,
                        duration: null,
                        cover_pic_src: null,
                    };
                    this.attachments.unshift(newItem);
                    this.uploadCompleteFilesNum++;
                    this.updateVideo();
                }
            },
            handleComplete(files) {
                this.uploading = false;
                if (this.simple) {
                    let urls = [];
                    let attachments = [];
                    for (let i in files) {
                        if (files[i].response.data && files[i].response.data.code === 0) {
                            urls.push(files[i].response.data.data.url);
                            attachments.push(files[i].response.data.data);
                        }
                    }
                    if (!urls.length) {
                        return;
                    }
                    this.dialogVisible = false;
                    this.$emit('selected', attachments, this.params);
                    if (this.multiple) {
                        this.$emit('input', urls);
                    } else {
                        this.$emit('input', urls[0]);
                    }
                }
            },
            handleLoadMore(currentPage) {
                if (this.noMore) {
                    return;
                }
                this.page = currentPage;
                this.loading = true;
                this.loadingMore = true;
                this.loadList({});
            },
            updateVideo() {
                if (!this.canvas) {
                    this.canvas = document.getElementById('material-canvas');
                }
                for (let i in this.attachments) {
                    if (this.attachments[i].type == 2) {
                        if (this.attachments[i].duration) {
                            continue;
                        }
                        let times = 0;
                        let video = null;
                        const maxRetry = 10;
                        const id = 'app_material_' + this._uid + '_' + i;
                        const timer = setInterval(() => {
                            times++;
                            if (times >= maxRetry) {
                                clearInterval(timer);
                            }
                            if (!video) {
                                video = document.getElementById(id);
                            }
                            if (!video) {
                                return;
                            }
                            try {
                                const zoom = 0.15;
                                this.canvas.width = video.videoWidth * zoom;
                                this.canvas.height = video.videoHeight * zoom;
                                this.canvas.getContext('2d').drawImage(video, 0, 0, this.canvas.width, this.canvas.height);
                                this.attachments[i].cover_pic_src = this.canvas.toDataURL('image/jpg');
                            } catch (e) {
                                console.warn('获取视频封面异常: ', e);
                            }

                            if (video.duration && !isNaN(video.duration)) {
                                let m = Math.trunc(video.duration / 60);
                                let s = Math.trunc(video.duration) % 60;
                                m = m < 10 ? `0${m}` : `${m}`;
                                s = s < 10 ? `0${s}` : `${s}`;
                                this.attachments[i].duration = `${m}:${s}`;
                                clearInterval(timer);
                            }
                        }, 500);
                    }
                }
            }
        }
    });
</script>