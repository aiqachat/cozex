<?php
/**
 * Created by PhpStorm
 * User: chenzs
 * Date: 2024/9/29
 * Time: 4:08 下午
 * @copyright: ©2024 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */
Yii::$app->loadViewComponent('google', __DIR__ . '/oauth');
?>
<style>
    .currency-width {
        width: 550px;
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
        border-radius: 0 8px 8px 0;
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

    .bottom-div {
        width: 50%;
    }
</style>
<div id="app" v-cloak>
    <el-tabs v-model="activeName" type="border-card" @tab-click="handleClick" v-loading="loading">
        <el-tab-pane label="谷歌OAuth2.0" name="basic">
            <google ref="basic" :form-name="formName"></google>
        </el-tab-pane>
        <div class='bottom-div' flex="main:center">
            <el-button class='button-item' :loading="btnLoading" type="primary" @click="store" size="small">保存</el-button>
        </div>
    </el-tabs>
</div>
<script>
    new Vue({
        el: '#app',
        data() {
            return {
                activeName: 'basic',
                formName: 'form',
                btnLoading: false,
                loading: false,
            };
        },
        mounted() {
            this.getDetail()
        },
        methods: {
            handleClick(tab, event) {
               this.getDetail()
            },
            getDetail() {
                this.loading = true;
                request({
                    params: {
                        r: 'netb/setting/oauth',
                        tab: this.activeName
                    },
                    method: 'get',
                }).then(e => {
                    if (e.data.code === 0) {
                        this.$refs[this.activeName].form = Object.assign({}, e.data.data)
                    } else {
                        this.$message.error(e.data.msg);
                    }
                    this.loading = false;
                }).catch(e => {
                    console.log(e);
                    this.loading = false;
                });
            },
            store() {
                let self = this;
                this.$refs[this.activeName].$refs[this.formName].validate((valid) => {
                    if (valid) {
                        self.btnLoading = true;
                        request({
                            params: {
                                r: 'netb/setting/oauth'
                            },
                            method: 'post',
                            data: {
                                formData: self.$refs[self.activeName].form,
                                tab: self.activeName,
                            }
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
            }
        },
    });
</script>