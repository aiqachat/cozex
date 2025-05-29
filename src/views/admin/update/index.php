<?php

/**
 * Created by IntelliJ IDEA.
 * author: wstianxia
 * Date: 2019/3/1
 * Time: 17:05
 */

/* @var $this \yii\web\View */
?>
<style>
    .row-item {
        height: 105px;
        width: 30%;
        padding-left: 30px;
        padding-top: 15px;
        color: #353535;
        font-size: 26px;
    }

    .row-item:first-of-type {
        border-right: 1px dashed #e6e6e6;
    }

    .row-label {
        color: #9c9fa4;
        font-size: 16px;
        margin-bottom: 10px;
    }

    .version-item, .next-version {
        line-height: 1.5;
    }

    .next-version p,
    .version-item p {
        margin-top: 0;
        margin-bottom: 0;
    }

    .version-item::after {
        content: " ";
        display: block;
        height: 0;
        border-bottom: 1px dashed #c9c9c9;
        margin: 10px 0;
        width: 500px;
        max-width: 100%;
    }

    .update-tab .el-tabs__header {
        margin-right: 20px !important;
        background: #fff;
        border-radius: 5px;
        border: 1px solid #eee;
    }

    .update-tab .el-tabs__content {
        background: #fff;
        padding: 20px;
        border-radius: 5px;
        border: 1px solid #eee;
    }

    .update-tab.el-tabs--left .el-tabs__item.is-left {
        text-align: left;
        min-width: 150px;
    }

    .update-tab.el-tabs--left .el-tabs__active-bar.is-left {
        left: 0;
        right: auto;
        height: 30px !important;
        top: 15px;
    }

    .update-tab.el-tabs--left .el-tabs__nav-wrap.is-left::after {
        background: transparent;
    }

    .update-tab .el-tabs__item {
        height: 60px;
        line-height: 60px;
    }

    .update-num-icon {
        display: inline-block;
        width: 18px;
        height: 18px;
        text-align: center;
        line-height: 18px;
        background: #ff4544;
        color: #fff;
        border-radius: 999px;
        font-size: 10px;
        position: relative;
        top: -3px;
        right: -3px;
    }

    .plugin-item {
        width: 235px;
        border: 1px solid #e2e2e2;
        background: #fff;
        margin: 20px 0 0 20px;
        padding: 15px;
    }

    .plugin-icon {
        width: 50px;
        height: 50px;
        background-size: cover;
        background-position: center;
        background-color: #e2e2e2;
    }

    .plugin-update-btn {
        font-size: 16px;
        padding: 4px !important;
    }

    .plugin-display-name {
        line-height: 1.75;
    }

    .text-ellipsis {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
<div id="app" v-cloak>
    <el-tabs class="update-tab" tab-position="left">
        <el-tab-pane>
            <div class="update-tab-title" slot="label">系统更新</div>
            <div v-loading="loading">
                <template v-if="result">
                    <div flex="dir-left" style="margin-bottom: 20px;">
                        <div class="row-item">
                            <div class="row-label">当前版本</div>
                            <div>{{result.host_version}}</div>
                        </div>
                        <div class="row-item">
                            <div class="row-label">下一版本</div>
                            <div>
                                <div class="next-version" v-if="result.next_version">
                                    <div flex="dir-left" style="margin-bottom: 10px;align-items: center">
                                        <div style="margin-right: 8px">v{{result.next_version.version_number}}</div>
                                        <el-button size="small" type="primary"
                                                   style="padding: 9px 25px;margin-bottom: 10px"
                                                   @click="updateConfirm" :loading="updateLoading">更新
                                        </el-button>
                                    </div>
                                </div>
                                <div v-else>暂无新版本</div>
                            </div>
                        </div>
                    </div>
                    <div style="border-bottom: 1px solid #e2e2e2;margin-bottom: 20px;"></div>
                    <div>
                        <div class="row-label" style="margin-bottom: 20px">历史版本记录</div>
                        <div style="padding-left: 50px;" v-if="result.list && result.list.length">
                            <div v-for="item in result.list" class="version-item">
                                <div style="margin-bottom: 10px">版本号: {{item.version_number}}</div>
                                <div v-html="item.content"></div>
                            </div>
                        </div>
                        <div style="padding-left: 50px;" v-else>暂无记录</div>
                    </div>
                </template>
            </div>
        </el-tab-pane>
    </el-tabs>
</div>
<script>
    new Vue({
        el: '#app',
        data() {
            return {
                loading: true,
                result: null,
                updateLoading: false,
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
                        r: 'admin/update/index',
                    },
                }).then(e => {
                    this.loading = false;
                    if (e.data.code === 0) {
                        this.result = e.data.data;
                    } else {
                        this.$alert(e.data.msg, '提示', {
                            type: 'error',
                        });
                    }
                }).catch(e => {
                    this.loading = false;
                });
            },
            updateConfirm() {
                this.$confirm("确认更新到版本 v" + this.result.next_version.version_number + ' ?', "警告", {
                    type: 'warning',
                }).then(() => {
                    this.update();
                }).catch(() => {
                    location.reload();
                });
            },
            update() {
                this.updateLoading = true;
                this.$request({
                    params: {
                        r: 'admin/update/update'
                    },
                    data: {
                        _csrf: this._csrf,
                    },
                    method: 'post',
                }).then(e => {
                    if (e.data.code === 0) {
                        if(e.data.data.reply === 1){
                            this.update();
                        }else {
                            this.$alert(e.data.msg, "提示").then(() => {
                                location.reload();
                            }).catch(() => {
                                location.reload();
                            });
                        }
                    } else {
                        this.$alert(e.data.msg, "提示", {
                            type: 'error',
                        }).then(() => {
                            location.reload();
                        }).catch(() => {
                            location.reload();
                        });
                    }
                }).catch(e => {
                });
            },
        },
    });
</script>