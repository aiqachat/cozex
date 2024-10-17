<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: chenzs
 */
?>
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

    .input-item {
        display: inline-block;
        width: 290px;
        margin: 0 0 20px;
    }

    .input-item .el-input__inner {
        border-right: 0;
    }

    .input-item .el-input__inner:hover{
        border: 1px solid #dcdfe6;
        border-right: 0;
        outline: 0;
    }

    .input-item .el-input__inner:focus{
        border: 1px solid #dcdfe6;
        border-right: 0;
        outline: 0;
    }

    .input-item .el-input-group__append {
        background-color: #fff;
        border-left: 0;
        width: 10%;
        padding: 0;
    }

    .input-item .el-input-group__append .el-button {
        padding: 0;
    }

    .input-item .el-input-group__append .el-button {
        margin: 0;
    }

    .select {
        float: left;
        width: 100px;
        margin-right: 10px;
    }

    .remark_name {
        color: #888888;
        font-size: 12px;
        margin-top: -5px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 200px;
        display: inline-block;
        height: 15px;
        line-height: 15px;
    }
    .remark {
        word-break: break-all;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 4;
        overflow: hidden;
        line-height: 25px;
        max-height: 100px;
    }

    .platform-img {
        width: 24px;
        height: 24px;
        margin-right: 4px;
    }
</style>
<div id="app" v-cloak>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <div>
                <span>用户管理</span>
                <app-new-export-dialog-2 
                    style="float: right;margin-top: -5px" 
                    :field_list='exportList' 
                    :params="searchData"
                    action_url="mall/user/index"
                    @selected="exportConfirm">
                </app-new-export-dialog-2>
            </div>
        </div>
        <div class="table-body">
            <div class="input-item">
                <el-input @keyup.enter.native="search" size="small" placeholder="请输入ID/昵称/手机号/备注/联系方式" v-model="searchData.keyword" clearable @clear="search">
                    <el-button slot="append" icon="el-icon-search" @click="search"></el-button>
                </el-input>
            </div>
            <el-table
            ref="multipleTable"
            class="table-info"
            :data="form"
            border
            style="width: 100%"
            v-loading="listLoading">
                <el-table-column
                  type="selection"
                  width="55">
                </el-table-column>
                <el-table-column prop="user_id" label="ID" width="75"></el-table-column>
                <el-table-column label="头像" width="300">
                    <template slot-scope="scope">
                        <div>
                            <div flex="dir:left cross:center">
                                <app-image mode="aspectFill" style="margin-right: 8px;flex-shrink: 0" :src="scope.row.avatar"></app-image>
                                <div style="width: 100%;">
                                    <div>{{scope.row.nickname}}</div>
                                    <el-tooltip v-if="scope.row.remark_name" effect="dark" placement="bottom-start" :content="`备注名：${scope.row.remark_name}`">
                                        <div class="remark_name">备注名：{{scope.row.remark_name}}</div>
                                    </el-tooltip>
                                    <div flex="main:justify" style="width: 100%;">
                                        <img class="platform-img" :src="scope.row.platform_icon" alt="">
                                        <el-button v-if="scope.row.platform_user_id" @click="openId(scope.$index)" type="success" style="padding:5px !important;">显示OpenId</el-button>
                                    </div>
                                </div>
                            </div>
                            <div v-if="scope.row.is_open_id">
                                <block v-for="(item, index) in scope.row.icon" v-key="index">
                                    <div flex="dir:left cross:center" style="margin-top: 5px">
                                        <img class="platform-img" :src="item" alt="">
                                        <span>{{scope.row.openid[index]}}</span>
                                    </div>
                                </block>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="remark" label="备注" width="180">
                    <template slot-scope="scope">
                        <el-tooltip class="item" v-if="scope.row.showRemark" effect="dark" :content="scope.row.remark" placement="top">
                            <div class="remark">{{scope.row.remark}}</div>
                        </el-tooltip>
                        <div v-else class="remark">{{scope.row.remark}}</div>
                    </template>
                </el-table-column>
                <el-table-column prop="mobile" label="手机号" width="120">
                </el-table-column>
                <el-table-column prop="balance" label="余额">
                    <template slot-scope="scope">
                        <el-button type="text" @click="$navigate({r: 'mall/user/balance-log', user_id:scope.row.user_id})"
                                   v-text="scope.row.balance"></el-button>
                    </template>
                </el-table-column>
                <el-table-column prop="created_at" label="加入时间" width="180"></el-table-column>
                <el-table-column label="操作" width="220"  fixed="right">
                    <template slot-scope="scope">
                        <el-tooltip class="item" effect="dark" content="编辑" placement="top">
                            <el-button circle type="text" size="mini" @click="$navigate({r: 'mall/user/edit', id:scope.row.user_id, page: page})">
                                <img src="statics/img/mall/edit.png" alt="">
                            </el-button>
                        </el-tooltip>
                        <el-tooltip class="item" effect="dark" content="编辑余额" placement="top">
                            <el-button circle type="text" size="mini" @click="handleBalance(scope.row)">
                                <img src="statics/img/mall/balance.png" alt="">
                            </el-button>
                        </el-tooltip>
                    </template>
                </el-table-column>
            </el-table>
            <div flex="dir:right" style="margin-top: 20px;">
                <el-pagination @current-change="pagination" hide-on-single-page background layout="total, prev, pager, next, jumper"
                               :total="pageCount" :page-size="pageSize" :current-page="currentPage"></el-pagination>
            </div>
        </div>
        <!-- 编辑余额 -->
        <el-dialog title="编辑余额" :visible.sync="dialogBalance" width="30%">
            <el-form :model="balanceForm" label-width="80px" :rules="balanceFormRules" ref="integralForm">
                <el-form-item label="操作" prop="type">
                    <el-radio v-model="balanceForm.type" label="1">充值</el-radio>
                    <el-radio v-model="balanceForm.type" label="2">扣除</el-radio>
                </el-form-item>
                <el-form-item label="金额" prop="price" size="small">
                    <el-input type="number" v-model="balanceForm.price" :max="99999999"></el-input>
                </el-form-item>
                <el-form-item label="充值图片" prop="pic_url">
                    <app-attachment :multiple="false" :max="1" @selected="balancePicUrl">
                        <el-button size="mini">选择文件</el-button>
                    </app-attachment>
                    <app-image width="80px" height="80px" mode="aspectFill" :src="balanceForm.pic_url"></app-image>
                </el-form-item>
                <el-form-item label="备注" prop="remark" size="small">
                    <el-input v-model="balanceForm.remark"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogBalance = false">取消</el-button>
                <el-button :loading="btnLoading" type="primary" @click="balanceSubmit">确认</el-button>
            </div>
        </el-dialog>
    </el-card>
</div>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                searchData: {
                    keyword: '',
                },
                keyword: '',
                form: [],
                pageCount: 0,
                pageSize: 10,
                page: 1,
                currentPage: null,
                listLoading: false,
                btnLoading: false,

                // 导出
                exportList: [],

                //余额
                dialogBalance: false,
                balanceForm: {
                    type: '1',
                    price: '',
                    pic_url: '',
                    remark: '',
                },
                balanceFormRules: {
                    type: [
                        {required: true, message: '操作不能为空', trigger: 'blur'},
                    ],
                    num: [
                        {required: true, message: '金额不能为空', trigger: 'blur'},
                    ],
                },
            };
        },
        methods: {
            strlen(str){
                var len = 0;
                for (var i=0; i<str.length; i++) {
                 var c = str.charCodeAt(i);
                //单字节加1
                 if ((c >= 0x0001 && c <= 0x007e) || (0xff60<=c && c<=0xff9f)) {
                   len++;
                 }
                 else {
                  len+=2;
                 }
                }
                return len;
            },
            openId(index) {
                let item = this.form;
                item[index].is_open_id = !item[index].is_open_id;
                this.form = JSON.parse(JSON.stringify(this.form));
            },
            exportConfirm() {
                this.searchData.keyword = this.keyword;
            },
            //余额
            balancePicUrl(e) {
                if (e.length) {
                    this.balanceForm.pic_url = e[0].url;
                }
            },
            handleBalance(row) {
                this.balanceForm = Object.assign(this.balanceForm, {user_id: row.user_id});
                this.dialogBalance = true;
            },
            balanceSubmit() {
                this.$refs.integralForm.validate((valid) => {
                    if (valid) {
                        let para = Object.assign({}, this.balanceForm);
                        this.btnLoading = true;
                        this.dialogBalance = false;
                        request({
                            params: {
                                r: 'mall/user/balance',
                            },
                            method: 'post',
                            data: para,
                        }).then(e => {
                            if (e.data.code === 0) {
                                location.reload();
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
            //
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
                        r: 'mall/user/index',
                        page: this.page,
                        keyword: this.searchData.keyword,
                    },
                }).then(e => {
                    if (e.data.code === 0) {
                        this.form = e.data.data.list;
                        for(item of this.form) {
                            item.showRemark = false;
                            if(this.strlen(item.remark) > 42) {
                                item.showRemark = true;
                            }
                        }
                        this.exportList = e.data.data.exportList;
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
        },
        mounted: function () {
            this.page = getQuery('page') ? getQuery('page') : 1;
            this.getList();
        }
    });
</script>
