<?php

use app\forms\mall\setting\PayConfigForm;

require __DIR__ . '/pay/basic.php';
require __DIR__ . '/pay/wechat.php';
require __DIR__ . '/pay/stripe.php';
?>
<style>
    .header-box {
        padding: 20px;
        background-color: #fff;
        margin-bottom: 10px;
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
    }

    .out-max {
        width: 50vh;
    }
</style>
<div id="app" v-cloak>
    <div slot="header" class="header-box">
        <el-breadcrumb separator="/">
            <el-breadcrumb-item>
                支付设置
            </el-breadcrumb-item>
        </el-breadcrumb>
    </div>
    <el-tabs type="border-card" v-model="activeName" @tab-click="handleClick" v-loading="loading">
        <el-tab-pane label="基础设置" name="<?= PayConfigForm::TAB_BASIC ?>">
            <basic ref="<?= PayConfigForm::TAB_BASIC ?>" :form-name="formName"></basic>
        </el-tab-pane>
        <el-tab-pane label="微信" name="<?= PayConfigForm::TAB_WX ?>">
            <wechat ref="<?= PayConfigForm::TAB_WX ?>" :form-name="formName"></wechat>
        </el-tab-pane>
        <el-tab-pane label="Stripe支付" name="<?= PayConfigForm::TAB_STRIPE ?>">
            <stripe ref="<?= PayConfigForm::TAB_STRIPE ?>" :form-name="formName"></stripe>
        </el-tab-pane>
        <div class='bottom-div' flex="main:center">
            <el-button size="small" :loading="btnLoading" type="primary" @click="store">保存</el-button>
        </div>
    </el-tabs>
</div>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                activeName: '<?= PayConfigForm::TAB_BASIC ?>',
                loading: false,
                formName: 'form',
                btnLoading: false,
            }
        },

        methods: {
            handleClick(tab, event) {
                this.getDetail()
            },
            getDetail() {
                this.loading = true;
                request({
                    params: {
                        r: 'netb/setting/pay',
                        tab: this.activeName
                    },
                    method: 'get',
                }).then(e => {
                    if (e.data.code === 0) {
                        this.$refs[this.activeName].form = Object.assign({}, e.data.data.data)
                        if(this.$refs[this.activeName].default){
                            this.$refs[this.activeName].default = Object.assign({}, e.data.data.default)
                        }
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
                                r: 'netb/setting/pay'
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
        mounted: function () {
            this.getDetail()
        }
    });
</script>
