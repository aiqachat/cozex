<?php
/**
 * Created by PhpStorm
 * User: chenzs
 * Date: 2024/9/29
 * Time: 4:08 下午
 * @copyright: ©2024 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */
require __DIR__ . '/price/basic.php';
?>
<style>
    .currency-width {
        max-width: 500px;
    }

    .currency-width .el-input__inner {
        height: 35px;
        line-height: 35px;
    }

    .isAppend .el-input__inner {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .currency-width .el-input-group__append {
        background-color: #2E9FFF;
        color: #fff;
        text-align: center;
        border: 0;
    }

    .button-item {
        margin-top: 12px;
        padding: 9px 25px;
    }

    .bottom-div {
        /*position: fixed;*/
        /*bottom: 40px;*/
        background-color: #ffffff;
        /*z-index: 999;*/
        /*padding: 0 10px 15px 0;*/
        width: 50%;
    }
</style>
<div id="app" v-cloak>
    <el-tabs v-model="activeName" type="border-card" @tab-click="handleClick" v-loading="loading">
        <el-tab-pane label="基础设置" name="one">
            <basic ref="one" :form-name="formName"></basic>
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
                activeName: 'one',
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
                        r: 'netb/setting/price',
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
                                r: 'netb/setting/price'
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