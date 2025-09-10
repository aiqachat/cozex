<?php
/**
 * link: https://www.netbcloud.com//
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

?>
<style>
    .table-body {
        padding: 20px;
        background-color: #fff;
    }

    .input-item {
        display: inline-block;
        width: 250px;
        margin: 0 0 20px 20px;
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

    .table-body .el-button {
        padding: 0!important;
        border: 0;
        margin: 0 5px;
    }
    .show-img {
        position: fixed;
        top: 0;
        left: 0;
        background-color: rgba(0, 0, 0, 0.3);
        z-index: 3000;
        width: 100%;
        height: 100%;
    }
    .show-img img {
        max-height: 100%;
        max-width: 100%;
        position: fixed;
        z-index: 3024;
    }
</style>
<div id="app" v-cloak>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <div>
                <span>积分记录</span>
            </div>
        </div>
        <div class="table-body">
            <el-date-picker size="small" v-model="date" type="datetimerange"
                            style="float: left"
                            value-format="yyyy-MM-dd HH:mm:ss"
                            range-separator="至" start-placeholder="开始日期"
                            @change="selectDateTime"
                            end-placeholder="结束日期">
            </el-date-picker>
            <div class="input-item">
                <el-input @keyup.enter.native="search" size="small" placeholder="请输入昵称" v-model="keyword" clearable @clear="search">
                    <el-button slot="append" icon="el-icon-search" @click="search"></el-button>
                </el-input>
            </div>
            <el-table :data="form" border style="width: 100%" v-loading="listLoading">
                <el-table-column prop="user.nickname" label="昵称" min-width="120"></el-table-column>
                <el-table-column label="收支情况积分" min-width="110">
                    <template slot-scope="scope">
                        <div style="font-size: 18px;color: #68CF3D" v-if="scope.row.type == 1">+{{scope.row.integral}}</div>
                        <div style="font-size: 18px;color: #F6AA5A" v-if="scope.row.type == 2">-{{scope.row.integral}}</div>
                    </template>
                </el-table-column>
                <el-table-column prop="desc" label="说明" min-width="200"></el-table-column>
                <el-table-column label="订单号" min-width="130">
                    <template slot-scope="scope" v-if="scope.row.order_no">
                        {{scope.row.order_no}}
                    </template>
                </el-table-column>
                <el-table-column label="备注" min-width="100">
                    <template slot-scope="scope">
                        <div flex="box:first" v-if="scope.row.info_desc">
                            <div @click="toLook(scope.row.info_desc.pic_url)" style="padding-right: 10px" v-if="scope.row.info_desc.hasOwnProperty('pic_url') && scope.row.info_desc.pic_url && scope.row.info_desc.pic_url.length > 0">
                                <app-image mode="aspectFill" :src="scope.row.info_desc.pic_url"></app-image>
                            </div>
                            <div v-if="scope.row.info_desc.name">{{scope.row.info_desc.name}}
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="created_at" width="150" label="充值时间"></el-table-column>
            </el-table>

            <!--工具条 批量操作和分页-->
            <el-col :span="24" class="toolbar">
                <el-pagination
                        background
                        layout="prev, pager, next, jumper"
                        @current-change="pageChange"
                        :page-size="pagination.pageSize"
                        :total="pagination.total_count"
                        style="float:right;margin:15px"
                        v-if="pagination">
                </el-pagination>
            </el-col>
        </div>
    </el-card>
    <div @click="bigImg = ''" class="show-img" flex="main:center cross:center" v-if="bigImg.length > 0">
        <img :src="bigImg" @click.stop="">
    </div>
</div>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                searchData: {
                    keyword: '',
                    date: '',
                    start_date: '',
                    end_time: '',
                },
                date: '',
                keyword: '',
                bigImg: '',
                form: [],
                pagination: null,
                listLoading: false,
            };
        },
        methods: {
            toLook(img) {
                this.bigImg = img;
            },
            exportConfirm() {
                this.searchData.keyword = this.keyword;
                this.searchData.start_date = this.date[0];
                this.searchData.end_date = this.date[1];
            },
            pageChange(currentPage) {
                this.page = currentPage;
                this.getList();
            },
            search() {
                this.page = 1;
                if (this.date == null) {
                    this.date = ''
                }
                this.getList();
            },
            selectDateTime(e) {
                if (e != null) {
                    this.searchData.start_date = e[0];
                    this.searchData.end_date = e[1];
                } else {
                    this.searchData.start_date = '';
                    this.searchData.end_date = '';
                }
                this.page = 1;
                this.search();
            },

            getList() {
                let params = {
                    r: 'netb/integral/log',
                    page: this.page,
                    date: this.date,
                    user_id: getQuery('user_id'),
                    keyword: this.keyword,
                };
                if (this.date) {
                    Object.assign(params, {
                        start_date: this.date[0],
                        end_date: this.date[1],
                    });
                }
                request({
                    params,
                }).then(e => {
                    if (e.data.code === 0) {
                        this.form = e.data.data.list;
                        this.pagination = e.data.data.pagination;
                    } else {
                        this.$message.error(e.data.msg);
                    }
                    this.listLoading = false;
                }).catch(e => {
                    this.listLoading = false;
                });
                this.listLoading = true;
            },
        },
        mounted: function() {
            this.getList();
        }
    });
</script>
