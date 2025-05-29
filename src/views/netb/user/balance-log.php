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
                <span>余额收支</span>
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

                <el-table-column prop="id" label="ID" width="80"></el-table-column>

                <el-table-column prop="user.nickname" label="昵称"></el-table-column>

                <el-table-column label="收支情况" width="180">
                    <template slot-scope="scope">
                        <div style="font-size: 18px;"
                             :style="{color: scope.row.type == 1 ? '#68CF3D' : scope.row.type == 2 ? '#F6AA5A' : ''}"
                        >
                            {{scope.row.type == 1 ? '+' : scope.row.type == 2 ? '-' : ''}}
                            {{scope.row.money}}
                        </div>
                    </template>
                </el-table-column>

                <el-table-column prop="desc" label="说明" width="250px"></el-table-column>
                <el-table-column prop="desc" label="订单号" width="250px">
                    <template slot-scope="scope" v-if="scope.row.order_no">
                        {{scope.row.order_no}}
                    </template>
                </el-table-column>

                <el-table-column label="备注">
                    <template slot-scope="scope">
                        <div flex="box:first" v-if="scope.row.info_desc">
                            <div @click="toLook(scope.row.info_desc.pic_url)" style="padding-right: 10px" v-if="scope.row.info_desc.hasOwnProperty('pic_url')">
                                <app-image mode="aspectFill" :src="scope.row.info_desc.pic_url"></app-image>
                            </div>
                            <div v-if="scope.row.info_desc.hasOwnProperty('remark')">{{scope.row.info_desc.remark}}</div>
                        </div>
                    </template>
                </el-table-column>

                <el-table-column prop="created_at" width="180" label="充值时间"></el-table-column>

            </el-table>
            <div style="text-align: right;margin: 20px 0;">
                <el-pagination @current-change="pagination" background layout="prev, pager, next, jumper"
                               :page-count="pageCount"></el-pagination>
            </div>
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
                    end_date: '',
                },
                date: '',
                keyword: getQuery('keyword'),
                bigImg: '',
                form: [],
                pageCount: 0,
                listLoading: false,
            };
        },
        methods: {
            toLook(img) {
                console.log(img)
                this.bigImg = img;
            },
            exportConfirm() {
                this.searchData.keyword = this.keyword;
                this.searchData.date = this.date;
            },
            pagination(currentPage) {
                this.page = currentPage;
                this.getList();
            },
            search() {
                this.page = 1;
                if(this.date == null) {
                    this.searchData.start_date = '';
                    this.searchData.end_date = ''
                }
                this.getList();
            },
            getList() {
                this.listLoading = true;
                request({
                    params: {
                        r: 'netb/user/balance-log',
                        page: this.page,
                        date: this.date,
                        user_id: getQuery('user_id'),
                        keyword: this.keyword,
                        start_date: this.searchData.start_date,
                        end_date: this.searchData.end_date,
                    },
                }).then(e => {
                    if (e.data.code === 0) {
                        let { list, pagination } = e.data.data;
                        this.form = list;
                        this.pageCount = pagination.page_count;
                    } else {
                        this.$message.error(e.data.msg);
                    }
                    this.listLoading = false;
                }).catch(e => {
                    this.listLoading = false;
                });
            },
            selectDateTime(e) {
                if(e != null) {
                    this.searchData.start_date = e[0];
                    this.searchData.end_date = e[1];
                }else {
                    this.searchData.start_date = '';
                    this.searchData.end_date = '';
                }
                this.search();
            }
        },
        mounted: function () {
            this.getList();
        }
    });
</script>
