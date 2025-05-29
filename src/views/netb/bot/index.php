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

    .btn {
        border: none;
        background: transparent;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn:hover {
        background: rgba(0, 0, 0, 0.05);
    }

    .btn i {
        font-size: 16px;
    }

    .btn i.bi-check-circle-fill {
        color: #67C23A;
    }

    .btn i.bi-power {
        color: #E6A23C;
    }

    .btn i.bi-gear {
        color: #409EFF;
    }

    .btn i.bi-mic {
        color: #F56C6C;
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
                        <el-tag size="mini" style="margin-bottom: 2px;"
                            v-if="searchData.space_id == 'all'">{{item.space_name || '--'}}</el-tag>
                        <div style="font-size: 16px;">{{item.bot_name}}</div>
                        <div style="font-size: 14px;color: #8E918F;margin-top: 5px;">{{item.description}}</div>
                    </div>
                    <div style="margin-top: 5px;" flex="cross:center box:last">
                        <span style="color: #8E918F">发布时间: {{item.publish_time}}</span>
                        <div>
                            <button class="btn" v-if="set_bot.bot_id == item.bot_id" @click="use(item)"
                                v-loading="btnLoading" title="使用中">
                                <i class="bi bi-check-circle-fill"></i>
                            </button>
                            <button class="btn" v-else @click="use(item)" v-loading="btnLoading" title="可开启">
                                <i class="bi bi-power"></i>
                            </button>
                        </div>
                    </div>
                    <div style="margin-top: 10px; border: 1px solid #eee; border-radius: 6px; padding: 8px;">
                        <div flex="main:justify cross:center" style="margin-bottom: 6px;">
                            <el-button size="mini" icon="el-icon-setting" @click="chatConfig(item)">Chat发布配置</el-button>
                            <el-button size="mini" @click="chatPreview(item)">预览</el-button>
                        </div>
                        <div flex="main:justify cross:center">
                            <el-button size="mini" icon="el-icon-setting"
                                @click="voiceConfig(item)">Voice发布配置</el-button>
                            <el-button size="mini" @click="voicePreview(item)" v-if="item.voice_url">预览</el-button>
                        </div>
                    </div>
                </el-card>
                <div v-if="form.length == 0">
                    暂无数据
                </div>
            </div>
            <div flex="dir:right" style="margin-top: 20px;">
                <el-pagination @current-change="pagination" hide-on-single-page background
                    layout="total, prev, pager, next, jumper" :total="pageCount" :page-size="pageSize"
                    :current-page="currentPage"></el-pagination>
            </div>
        </div>
    </el-card>
    <el-dialog title="查看图片" :visible.sync="dialog" width="30%">
        <div flex="dir:left main:center">
            <img src="statics/img/source/apiopen.jpg" width="580" />
        </div>
        <div slot="footer" class="dialog-footer">
            <el-button @click="dialog = false" type="primary">我知道了</el-button>
        </div>
    </el-dialog>
</div>
<script src="https://lf-cdn.coze.cn/obj/unpkg/flow-platform/chat-app-sdk/1.2.0-beta.8/libs/cn/index.js"></script>
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
                chatBot: null,
                refresh_token: null
            };
        },
        watch: {
            'searchData.space_id': function (val, oldValue) {
                this.getList();
            },
        },
        methods: {
            use(row) {
                let txt = '';
                if (row.bot_id === this.set_bot.bot_id) {
                    txt = "确认取消使用该bot吗？";
                } else {
                    txt = "确认使用该bot吗？";
                }
                this.$confirm(txt, '提示', {
                    type: 'warning'
                }).then(() => {
                    this.btnLoading = true;
                    request({
                        params: { r: 'netb/bot/use' },
                        data: {
                            bot_id: row.bot_id,
                            space_id: row.space_id,
                            account_id: this.searchData.account_id
                        },
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
            chatConfig(row) {
                navigateTo({
                    r: 'netb/bot/set',
                    bot_id: row.bot_id,
                    space_id: row.space_id,
                    account_id: this.searchData.account_id
                });
            },
            chatPreview(row) {
                if (this.chatBot) {
                    this.chatBot.destroy();
                }
                let config = row.preview_js;
                if (typeof config === 'string') {
                    config = JSON.parse(config);
                }
                config.auth.onRefreshToken = async () => {
                    return this.refresh_token;
                };
                this.chatBot = new CozeWebSDK.WebChatClient(config);
                this.chatBot.showChatBot();
            },
            voicePreview(row) {
                window.open(row.voice_url, '_blank', 'width=400,height=800')
            },
            voiceConfig(row) {
                navigateTo({
                    r: 'netb/bot/voice',
                    bot_id: row.bot_id,
                    account_id: this.searchData.account_id
                });
            },
            changeAccount(val) {
                this.searchData.account_id = val;
            },
            changeSpace(val) {
                this.searchData.space_id = val;
            },
            pagination(currentPage) {
                this.page = currentPage;
                this.getList();
            },
            getList() {
                if (!this.searchData.space_id) {
                    return;
                }
                this.listLoading = true;
                this.form = [];
                let param = Object.assign({ r: 'netb/bot/index', page: this.page }, this.searchData);
                request({
                    params: param,
                }).then(e => {
                    if (e.data.code === 0) {
                        this.form = e.data.data.list;
                        this.set_bot = e.data.data.set_bot;
                        this.pageCount = e.data.data.pagination.total_count;
                        this.pageSize = e.data.data.pagination.pageSize;
                        this.currentPage = e.data.data.pagination.current_page;
                        this.refresh_token = e.data.data.refresh_token
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