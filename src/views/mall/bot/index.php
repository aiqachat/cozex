<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: chenzs
 */
Yii::$app->loadViewComponent('app-coze-choose')
?>
<style>
    .table-body {
        padding: 20px;
        background-color: #fff;
    }

    .card-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .image {
        float: right;
        width: 64px;
        border-radius: 10px;
    }

    .card-container .el-card {
        width: 340px;
    }

    .line {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
        max-width: 220px;
        height: 68px;
    }
</style>
<div id="app" v-cloak>
    <app-coze-choose @account="changeAccount" @space="changeSpace" :all="1"></app-coze-choose>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <span>
                智能体管理
                <span style="color: #a4a4a4;">
                    （注：需要在coze平台发布智能体时选择开通Agent as API 和 Web SDK）
                    <el-tooltip class="item" effect="dark" content="点击图片显示提示图片" placement="top">
                      <el-button type="text" icon="el-icon-warning" @click="dialog = true"></el-button>
                    </el-tooltip>
                </span>
            </span>
        </div>
        <div class="table-body">
            <div class="card-container" v-loading="listLoading">
                <el-card v-for="(item, index) in form">
                    <img :src="item.icon_url" class="image">
                    <div class="line">
                        <el-tag size="mini" style="margin-bottom: 2px;" v-if="searchData.space_id == 'all'">{{item.space_name || '--'}}</el-tag>
                        <div style="font-size: 16px;">{{item.bot_name}}</div>
                        <div style="font-size: 14px;color: #8E918F;margin-top: 5px;">{{item.description}}</div>
                    </div>
                    <div style="margin-top: 5px;" flex="cross:center box:last">
                        <span style="color: #8E918F">发布时间: {{item.publish_time}}</span>
                        <div>
                            <el-button type="success" size="mini" v-if="set_bot.bot_id == item.bot_id" @click="use(item)" v-loading="btnLoading">使用中</el-button>
                            <el-button type="warning" size="mini" v-else @click="use(item)" v-loading="btnLoading">可开启</el-button>
                            <el-button type="primary" size="mini" @click="conf(item)">配置</el-button>
                        </div>
                    </div>
                </el-card>
                <div v-if="form.length == 0">
                    暂无数据
                </div>
            </div>
            <div flex="dir:right" style="margin-top: 20px;">
                <el-pagination @current-change="pagination" hide-on-single-page background layout="total, prev, pager, next, jumper"
                               :total="pageCount" :page-size="pageSize" :current-page="currentPage"></el-pagination>
            </div>
        </div>
    </el-card>
    <el-dialog title="查看图片" :visible.sync="dialog" width="30%">
        <div flex="dir:left main:center">
            <img :src="img" width="580"/>
        </div>
        <div slot="footer" class="dialog-footer">
            <el-button @click="dialog = false" type="primary">我知道了</el-button>
        </div>
    </el-dialog>
</div>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                searchData: {
                    account_id: null,
                    space_id: null,
                },
                form: [],
                pageCount: 0,
                pageSize: 10,
                page: 1,
                currentPage: null,
                listLoading: false,
                btnLoading: false,
                dialog: false,
                set_bot: {},
                img: 'statics/img/source/apiopen.jpg'
            };
        },
        watch: {
            'searchData.space_id': function (val, oldValue){
                this.getList();
            },
        },
        methods: {
            use(row){
                let txt = '';
                if(row.bot_id === this.set_bot.bot_id){
                    txt = "确认取消使用该bot吗？";
                }else{
                    txt = "确认使用该bot吗？";
                }
                this.$confirm(txt, '提示', {
                    type: 'warning'
                }).then(() => {
                    this.btnLoading = true;
                    request({
                        params: {r: 'mall/bot/use'},
                        data: {bot_id: row.bot_id},
                        method: 'post'
                    }).then(e => {
                        if (e.data.code === 0) {
                            this.$message.success(e.data.msg);
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        } else {
                            this.$message.error(e.data.msg);
                            this.btnLoading = false;
                        }
                    }).catch(e => {
                    });
                }).catch(e => {
                });
            },
            conf(row){
                navigateTo({r: 'mall/bot/set', bot_id: row.bot_id});
            },
            changeAccount(val){
                this.searchData.account_id = val;
            },
            changeSpace(val){
                this.searchData.space_id = val;
            },
            pagination(currentPage) {
                this.page = currentPage;
                this.getList();
            },
            getList() {
                if(!this.searchData.space_id){
                    return;
                }
                this.listLoading = true;
                this.form = [];
                let param = Object.assign({r: 'mall/bot/index', page: this.page}, this.searchData);
                request({
                    params: param,
                }).then(e => {
                    if (e.data.code === 0) {
                        this.form = e.data.data.list;
                        this.set_bot = e.data.data.set_bot;
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
