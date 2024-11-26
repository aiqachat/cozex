<?php
/**
 * Created by PhpStorm
 * User: chenzs
 * Date: 2024/9/29
 * Time: 4:08 下午
 * @copyright: ©2024 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */
?>
<style>
    .currency-width {
        width: 500px;
    }

    .currency-width .el-input__inner {
        height: 35px;
        line-height: 35px;
        border-radius: 8px;
    }

    .isAppend .el-input__inner {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .currency-width .el-input-group__append {
        background-color: #2E9FFF;
        color: #fff;
        text-align: center;
        border-radius: 8px;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border: 0;
    }

    .preview {
        height: 75px;
        line-height: 75px;
        text-align: center;
        width: 200px;
        background-color: #F7F7F7;
        color: #BBBBBB;
        font-size: 12px;
    }

    .button-item {
        margin-top: 12px;
        padding: 9px 25px;
    }

    .reset {
        position: absolute;
        top: 6px;
        left: 90px;
    }
</style>
<div id="app" v-cloak>
    <el-card shadow="never" v-loading="cardLoading">
        <el-form @submit.native.prevent :model="form" :rules="rules" label-width="120px" ref="form">
            <el-tabs v-model="activeName" @tab-click="handleClick">
                <el-tab-pane label="基础设置" name="basic">
                    <el-card class="box-card">
                        <div slot="header" class="clearfix">
                            <span>基本设置</span>
                        </div>
                        <el-form-item label="系统名称" prop="name">
                            <el-input v-model="form.name" maxlength="15" class="currency-width" placeholder="最多输入15个字符"></el-input>
                        </el-form-item>

                        <el-form-item label="系统logo" prop="mall_logo_pic">
                            <app-attachment style="margin-bottom:10px" :multiple="false" :max="1"
                                            v-model="form.mall_logo_pic">
                                <el-tooltip effect="dark"
                                            content="建议尺寸:40 * 40"
                                            placement="top">
                                    <el-button size="mini">选择图标</el-button>
                                </el-tooltip>
                            </app-attachment>
                            <div style="margin-right: 20px;display:inline-block;position: relative;cursor: pointer;">
                                <app-attachment :multiple="false" :max="1" v-model="form.mall_logo_pic">
                                    <app-image mode="aspectFill"
                                               width="45px"
                                               height='45px'
                                               :src="form.mall_logo_pic">
                                    </app-image>
                                </app-attachment>
                            </div>
                            <el-button size="mini" @click="resetImg('mall_logo_pic')" class="reset" type="primary">恢复默认</el-button>
                        </el-form-item>
                    </el-card>
                    <el-card class="box-card" style="margin-top: 10px;">
                        <div slot="header" class="clearfix">
                            <span>登录页配置</span>
                        </div>
                        <el-form-item label="LOGO图片URL">
                            <el-input class="currency-width isAppend" v-model="form.passport_logo">
                                <template slot="append">
                                    <app-attachment v-model="form.passport_logo">
                                        <el-button>上传图片</el-button>
                                    </app-attachment>
                                </template>
                            </el-input>
                            <div v-if="form.passport_logo">
                                <el-image style="height: 75px;" :src="form.passport_logo" :preview-src-list="[form.passport_logo]"></el-image>
                            </div>
                            <div v-else class="preview">建议尺寸：98*50</div>
                            <el-button size="mini" @click="resetImg('passport_logo')" type="primary">恢复默认</el-button>
                        </el-form-item>
                        <el-form-item label="登录页背景图">
                            <el-input class="currency-width isAppend" v-model="form.passport_bg">
                                <template slot="append">
                                    <app-attachment v-model="form.passport_bg">
                                        <el-button>上传图片</el-button>
                                    </app-attachment>
                                </template>
                            </el-input>
                            <div v-if="form.passport_bg">
                                <el-image style="height: 75px;" :src="form.passport_bg" :preview-src-list="[form.passport_bg]"></el-image>
                            </div>
                            <div v-else class="preview">建议尺寸：1920*1080</div>
                            <el-button size="mini" @click="resetImg('passport_bg')" type="primary">恢复默认</el-button>
                        </el-form-item>
                        <el-form-item label="底部版权信息">
                            <el-input class="currency-width" v-model="form.copyright"></el-input>
                        </el-form-item>
                        <el-form-item label="底部版权url">
                            <el-input class="currency-width" v-model="form.copyright_url"
                                      placeholder="例如:https://www.baidu.com">
                            </el-input>
                        </el-form-item>
                    </el-card>
                </el-tab-pane>
                <el-tab-pane label="内容设置" name="content">
                    <el-card class="box-card">
                        <div slot="header" class="clearfix">
                            <span>内容设置</span>
                        </div>
                        <el-form-item label="语音技术默认词" prop="voice_text">
                            <el-input v-model="form.voice_text" type="textarea" rows="4" class="currency-width"></el-input>
                        </el-form-item>
                        <el-form-item label="版本授权默认词" prop="version_text">
                            <el-input v-model="form.version_text" type="textarea" rows="3" class="currency-width"></el-input>
                        </el-form-item>
                    </el-card>
                </el-tab-pane>
                <el-button class='button-item' :loading="btnLoading" type="primary" @click="store('form')" size="small">保存</el-button>
            </el-tabs>
        </el-form>
    </el-card>
</div>
<script>
    new Vue({
        el: '#app',
        data() {
            return {
                activeName: 'basic',
                cardLoading: false,
                btnLoading: false,
                form: {},
                default: {},
                rules: {},
            };
        },
        created() {
            this.getDetail();
        },
        methods: {
            resetImg(type) {
                this.form[type] = this.default[type] || '';
            },
            handleClick(tab, event) {
                console.log(tab, event);
            },
            store(formName) {
                let self = this;
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        self.btnLoading = true;
                        request({
                            params: {
                                r: 'mall/index/index'
                            },
                            method: 'post',
                            data: self.form
                        }).then(e => {
                            self.btnLoading = false;
                            if (e.data.code === 0) {
                                self.$message.success(e.data.msg);
                            } else {
                                self.$message.error(e.data.msg);
                            }
                        }).catch(e => {
                            self.$message.error(e.data.msg);
                            self.btnLoading = false;
                        });
                    }
                });
            },
            getDetail() {
                let self = this;
                self.cardLoading = true;
                request({
                    params: {
                        r: 'mall/index/index',
                    },
                    method: 'get',
                }).then(e => {
                    self.cardLoading = false;
                    if (e.data.code === 0) {
                        self.form = Object.assign({}, e.data.data.data)
                        self.default = Object.assign({}, e.data.data.default)
                    } else {
                        self.$message.error(e.data.msg);
                    }
                }).catch(e => {
                    console.log(e);
                });
            },
        },
    });
</script>