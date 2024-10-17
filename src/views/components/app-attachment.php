<?php
/**
 * author: chenzs
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/11/14 13:49
 */
?>
<style scoped>
    .app-attachment-list {
        padding: 5px;
        width: 960px;
    }

    .app-attachment-list * {
        box-sizing: border-box;
    }

    .app-attachment-list:after {
        clear: both;
        display: block;
        content: " ";
    }

    .app-attachment-item {
        display: inline-block;
        /*box-shadow: 0 0 0 1px rgba(0, 0, 0, .15);*/
        cursor: pointer;
        position: relative;
        float: left;
        width: 120px;
        height: 140px;
        margin: 7.5px;
        text-align: center;
        padding: 10px 10px 0;
    }

    .app-attachment-active-icon {
        position: absolute;
        right: 5px;
        top: 5px;
        font-size: 28px;
        color: #409EFF;
        text-shadow: 0 0 1px rgba(255, 255, 255, 0.75);
        opacity: 0;
    }

    .app-attachment-item:hover {
        /*del-box-shadow: 0 0 0 1px rgba(84, 200, 255, 0.41);*/
    }

    .app-attachment-item.checked,
    .app-attachment-item.selected {
        box-shadow: 0 0 0 1px #1ed0ff;
        background: #daf5ff;
        border-radius: 5px;
    }

    .app-attachment-item.checked .app-attachment-active-icon,
    .app-attachment-item.selected .app-attachment-active-icon {
        opacity: 1;
    }

    .app-attachment-item .app-attachment-img {
        display: block;
    }

    .app-attachment-item .file-type-icon {
        width: 30px;
        height: 30px;
        border-radius: 30px;
        background: #666;
        color: #fff;
        text-align: center;
        line-height: 30px;
        font-size: 24px;
    }

    .app-attachment-upload {
        box-shadow: none;
        border: 1px dashed #b2b6bd;
        height: 100px;
        width: 100px;
        margin: 17.5px;
        padding: 0;
    }

    .app-attachment-upload i {
        font-size: 30px;
        color: #909399;
    }

    .app-attachment-upload:hover {
        box-shadow: none;
        border: 1px dashed #409EFF;
    }

    .app-attachment-upload:hover i {
        color: #409EFF;
    }

    .app-attachment-upload:active {
        border: 1px dashed #20669c;
    }

    .app-attachment-upload:active i {
        color: #20669c;
    }

    .app-attachment-dialog .group-menu {
        border-right: none;
        width: 230px;
    }

    .app-attachment-dialog .group-menu .el-menu-item {
        padding-left: 10px !important;
        padding-right: 10px;
    }

    .app-attachment-dialog .group-menu .el-menu-item .el-button {
        padding: 3px 0;
    }

    .del-app-attachment-dialog .group-menu .el-menu-item .el-button:hover {
        background: #e2e2e2;
    }

    .app-attachment-dialog .group-menu .el-menu-item .el-button i {
        margin-right: 0;
    }

    .app-attachment-simple-upload {
        width: 100% !important;
        height: 120px;
        border: 1px dashed #e3e3e3;
        cursor: pointer;
    }

    .app-attachment-simple-upload:hover {
        background: rgba(0, 0, 0, .05);
    }

    .app-attachment-simple-upload i {
        font-size: 32px;
    }


    .app-attachment-video-cover {
        background-size: cover;
        background-position: center;
        width: 100%;
        height: 100%;
    }

    .app-attachment-video-info {
        background: rgba(0, 0, 0, .35);
        color: #fff;
        position: absolute;
        left: 0;
        bottom: 0;
        padding: 1px 3px;
        font-size: 14px;
    }

    .app-attachment-video-name {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        font-size: 12px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .app-attachment-dialog .app-attachment-name {
        color: #666666;
        margin-top: 5px;
        font-size: 13px;
        word-break: break-all;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 1;
        overflow: hidden;
    }

    .app-attachment-dialog .search {
        margin-top: 10px;
        margin-left: 20px;
        width: 250px;
    }

    .app-attachment-dialog .search .el-input__inner {
        border-right: 0;
    }

    .app-attachment-dialog .search .el-input__inner:hover {
        border: 1px solid #dcdfe6;
        border-right: 0;
        outline: 0;
    }

    .app-attachment-dialog .search .el-input__inner:focus {
        border: 1px solid #dcdfe6;
        border-right: 0;
        outline: 0;
    }

    .app-attachment-dialog .search .el-input-group__append {
        background-color: #fff;
        border-left: 0;
        width: 10%;
        padding: 0;
    }

    .app-attachment-dialog .search .el-input-group__append {
        background-color: #fff;
        border-left: 0;
        width: 10%;
        padding: 0;
    }

    .app-attachment-dialog .search .el-input-group__append .el-button {
        padding: 0;
    }

    .app-attachment-dialog .search .el-input-group__append .el-button {
        margin: 0;
    }

    .app-attachment-dialog .scrollbar {
        height: 100%;
    }

    .app-attachment-dialog .scrollbar .el-scrollbar__wrap {
        overflow-y: hidden;
    }
</style>
<template id="app-attachment">
    <div class="app-attachment">
        <el-dialog class="app-attachment-dialog"
                   :title="title ? title : '选择文件'"
                   :visible.sync="dialogVisible"
                   @opened="dialogOpened"
                   :close-on-click-modal="false"
                   :width="simple?'25%':'65%'"
                   top="10vh"
                   append-to-body>
            <template v-if="simple">
                <app-upload
                        class="app-attachment-simple-upload"
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
                        flex="main:center cross:center">
                    <div v-if="uploading">{{uploadCompleteFilesNum}}/{{uploadFilesNum}}</div>
                    <i v-else class="el-icon-upload"></i>
                </app-upload>
            </template>
            <template v-else>
                <div style="height: 0;overflow: hidden;">
                    <canvas id="app-attachment-canvas" style="border: 1px solid #ccc;visibility: hidden;"></canvas>
                </div>
                <div v-if="false" flex="cross:center box:last" style="margin-bottom: 12px;">
                    <div></div>
                    <div flex="cross:center">
                        <el-button v-if="!showEditBlock" @click="showEditBlock=true">开启编辑</el-button>
                        <template v-if="showEditBlock">
                            <el-button @click="showEditBlock=false" style="margin-right: 12px">退出编辑模式</el-button>
                            <el-checkbox border v-model="selectAll"
                                         @change="selectAllChange"
                                         label="全选"
                                         style="margin-right: 12px;margin-bottom: 0"></el-checkbox>
                            <el-button :loading="deleteLoading"
                                       @click="deleteItems"
                                       style="margin-right: 12px">删除
                            </el-button>
                            <el-dropdown v-loading="moveLoading"
                                         trigger="click"
                                         :split-button="true"
                                         @command="moveItems">
                                <span>移动至</span>
                                <el-dropdown-menu slot="dropdown">
                                    <el-dropdown-item v-for="(item, index) in groupList"
                                                      :command="index"
                                                      :key="index">
                                        {{item.name}}
                                    </el-dropdown-item>
                                </el-dropdown-menu>
                            </el-dropdown>
                        </template>
                    </div>
                </div>
                <div flex="box:first" style="border: 1px solid #e3e3e3;margin-bottom: 10px;min-height: 300px">
                    <div style="border-right: 1px solid #e3e3e3">
                        <el-menu class="group-menu"
                                 mode="vertical"
                                 v-loading="groupListLoading">
                            <el-button style="margin-top:12px;margin-left:5%;" type="primary" @click="showAddGroup(-1)">
                                添加分组
                            </el-button>
                            <el-input style="width:90%;margin: 20px 5%" v-model="keyword"
                                      placeholder="请输入分类名称搜索"></el-input>
                            <el-scrollbar style="height:450px;width:100%">
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
                                            <div flex="dir:left">
                                                <el-button type="text" @click.stop="showAddGroup(index)">编辑</el-button>
                                                <div style="color:#409EFF;margin:0 2px">|</div>
                                                <el-button type="text" @click.stop="deleteGroup(index)">删除</el-button>
                                            </div>
                                        </div>
                                    </el-menu-item>
                                </template>
                            </el-scrollbar>
                        </el-menu>
                    </div>
                    <div v-loading="loading" flex="dir:top">
                        <el-scrollbar class="scrollbar">
                            <div class="search" style="margin-right: 12px">
                                <el-input placeholder="请输入名称搜索" v-model="p_keyword"
                                          @keyup.enter.native="picSearch"
                                          class="input-with-select">
                                    <el-button @click="picSearch" slot="append" icon="el-icon-search"></el-button>
                                </el-input>
                            </div>

                            <div class="app-attachment-list">
                                <div class="app-attachment-item app-attachment-upload">
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
                                            style="width: 100px;height: 100px">
                                        <div v-if="uploading">{{uploadCompleteFilesNum}}/{{uploadFilesNum}}</div>
                                        <i v-else class="el-icon-upload"></i>
                                    </app-upload>
                                </div>
                                <template v-for="(item, index) in attachments">
                                    <el-tooltip class="item" effect="dark" :content="item.name" placement="top"
                                                :open-delay="1">
                                        <div
                                                :key="index"
                                                :class="'app-attachment-item'+((item.checked&&!showEditBlock)?' checked':'')+(item.selected&&showEditBlock?' selected':'')"
                                                @click="handleClick(item)">
                                            <img v-if="item.type == 1" class="app-attachment-img" :src="item.thumb_url"
                                                 style="width: 100px;height: 100px;">
                                            <div v-if="item.type == 2" class="app-attachment-img"
                                                 style="width: 100px;height: 100px;position: relative">
                                                <div v-if="item.cover_pic_src"
                                                     class="app-attachment-video-cover"
                                                     :style="'background-image: url('+item.cover_pic_src+');'"></div>
                                                <video style="width: 0;height: 0;visibility: hidden;"
                                                       :id="'app_attachment_'+ _uid + '_' + index">
                                                    <source :src="item.url">
                                                </video>
                                                <div class="app-attachment-video-info">
                                                    <i class="el-icon-video-play"></i>
                                                    <span>{{item.duration?item.duration:'--:--'}}</span>
                                                </div>
                                            </div>
                                            <div v-if="item.type == 3" class="app-attachment-img"
                                                 style="width: 100px;height: 100px;line-height: 100px;text-align: center">
                                                <i class="file-type-icon el-icon-document"></i>
                                            </div>
                                            <div class="app-attachment-name">{{item.name}}</div>
                                            <i v-if="false" class="app-attachment-active-icon el-icon-circle-check"></i>
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
                <div style="text-align: right">
                    <span v-if="showEditBlock" style="color: #909399">请先退出编辑模式</span>
                    <el-button @click="confirm" type="primary" :disabled="showEditBlock">选定</el-button>
                </div>
            </template>
        </el-dialog>
        <el-dialog append-to-body title="分组管理" :visible.sync="addGroupVisible" :close-on-click-modal="false"
                   width="25%">
            <el-form @submit.native.prevent label-width="80px" ref="groupForm" :model="groupForm"
                     :rules="groupFormRule">
                <el-form-item label="分组名称" prop="name" style="margin-bottom: 22px;">
                    <el-input v-model="groupForm.name" maxlength="8" show-word-limit></el-input>
                </el-form-item>
                <el-form-item style="text-align: right">
                    <el-button type="primary" @click="groupFormSubmit('groupForm')" :loading="groupFormLoading">保存
                    </el-button>
                </el-form-item>
            </el-form>
        </el-dialog>
        <div style="line-height: normal;" @click="dialogVisible = !dialogVisible"
             :style="'display:'+(display?display:'inline-block')">
            <slot></slot>
        </div>
    </div>
</template>
<script>
    Vue.component('app-attachment', {
        template: '#app-attachment',
        props: {
            display: String,
            title: String,
            multiple: Boolean,
            max: Number,
            params: Object,
            simple: {
                type: Boolean,
                value: false,
            },
            type: {
                type: String,
                default: 'image',
            },
            value: {
                type: String,
                default: '',
            },
            openDialog: {
                type: Boolean,
                default: false,
            },
        },
        computed: {
            accept: {
                get() {
                    this.uploadParams.type = this.type;
                    if (this.type === 'image') {
                        return 'image/*';
                    }
                    if (this.type === 'video') {
                        return 'audio/*,video/*';
                    }
                    return '*/*';
                },
            },
        },
        watch: {
            openDialog(newVal, oldVal) {
                this.dialogVisible = newVal;
            },
            dialogVisible(newVal, oldVal) {
                if (!newVal) {
                    this.$emit("closed");
                }
            },
            keyword(newVal, oldVal) {
                const groupList = this.groupList;
                let arr = [];
                groupList.map(v => {
                    if (v.name.indexOf(newVal) !== -1) {
                        arr.push(v);
                    }
                });
                this.groupItem = arr;
            }
        },
        data() {
            return {
                canvas: null,
                uploading: false,
                dialogVisible: false,
                loading: true,
                loadingMore: false,
                noMore: false,
                attachments: [],
                checkedAttachments: [],
                uploadParams: {
                    attachment_group_id: null,
                    type: null,
                },
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
                        {required: true, message: '请填写分组名称。',}
                    ],
                },
                groupFormLoading: false,
                showEditBlock: false,
                selectAll: false,
                deleteLoading: false,
                moveLoading: false,
                currentAttachmentGroupId: null,
                video: null,
                keyword: '',
                pagination: null,
                p_keyword: '',
            };
        },
        created() {
        },
        methods: {
            picSearch() {
                this.page = 1;
                this.loading = true;
                this.loadGroups();
                this.loadList({});
            },

            dialogOpened() {
                if (this.simple) {
                    return;
                }
                if (!this.attachments || !this.attachments.length) {
                    this.loading = true;
                    this.loadGroups();
                    this.loadList({});
                }
            },
            deleteItems() {
                const itemIds = [];
                for (let i in this.attachments) {
                    if (this.attachments[i].selected) {
                        itemIds.push(this.attachments[i].id);
                    }
                }
                if (!itemIds.length) {
                    this.$message.warning('请先选择需要删除的图片。');
                    return;
                }
                this.$confirm('确认删除所选的' + itemIds.length + '张图片？', '提示', {
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
                    this.$message.warning('请先选择需要移动的图片。');
                    return;
                }
                this.$confirm('确认移动所选的' + itemIds.length + '张图片？', '提示', {
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
                        is_recycle: 0,
                        type: this.type,
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
            deleteGroup(index) {
                let title = '是否确认将分组放入回收站中？删除的分组可通过回收站还原';
                this.$confirm(title, '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.$request({
                        params: {
                            r: 'common/attachment/group-delete',
                        },
                        method: 'POST',
                        data: {
                            id: this.groupItem[index].id,
                            type: 1,
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
            groupFormSubmit(formName) {
                this.$refs[formName].validate(valid => {
                    if (valid) {
                        this.groupFormLoading = true;
                        this.$request({
                            params: {
                                r: 'common/attachment/group-update',
                            },
                            method: 'post',
                            data: Object.assign({}, this.groupForm, {'type': this.type}),
                        }).then(e => {
                            this.groupFormLoading = false;
                            if (e.data.code === 0) {
                                this.$message.success(e.data.msg);
                                this.addGroupVisible = false;
                                if (this.groupForm.edit_index > -1) {
                                    this.groupItem[this.groupForm.edit_index] = e.data.data;
                                } else {
                                    this.groupItem.push(e.data.data);
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
                if(index > -1) {
                    this.uploadParams.attachment_group_id = this.groupItem[index].id;
                }
                this.currentAttachmentGroupId = index > -1 ? this.groupItem[index].id : null;
                this.loadList({});
            },
            loadList(params) {
                this.noMore = false;
                this.selectAll = false;
                params['r'] = 'common/attachment/list';
                params['page'] = this.page;
                params['attachment_group_id'] = this.currentAttachmentGroupId;
                params['type'] = this.type;
                params['is_recycle'] = 0;
                params['keyword'] = this.p_keyword;
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
            handleClick(item) {
                if (this.showEditBlock) {
                    this.selectItem(item);
                    return;
                }
                if (item.checked) {
                    item.checked = false;
                    for (let i in this.checkedAttachments) {
                        if (item.id === this.checkedAttachments[i].id) this.checkedAttachments.splice(i, 1);
                    }
                    return;
                }
                if (this.multiple) {
                    let checkedCount = 0;
                    for (let i in this.attachments) if (this.attachments[i].checked) checkedCount++;
                    if (this.max && !item.checked && checkedCount >= this.max) return;
                    item.checked = true;
                    this.checkedAttachments.push(item);
                } else {
                    for (let i in this.attachments) this.attachments[i].checked = false;
                    item.checked = true;
                    this.checkedAttachments = [item];
                }
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
                    this.canvas = document.getElementById('app-attachment-canvas');
                }
                for (let i in this.attachments) {
                    if (this.attachments[i].type == 2) {
                        if (this.attachments[i].duration) {
                            continue;
                        }
                        let times = 0;
                        let video = null;
                        const maxRetry = 10;
                        const id = 'app_attachment_' + this._uid + '_' + i;
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
            },
        },
    });
</script>
