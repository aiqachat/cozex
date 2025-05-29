<?php
require __DIR__ . '/user/register.php';
require __DIR__ . '/user/setting.php';
?>
<style>
    .el-form {
        margin-bottom: 35px;
    }

    .currency-width {
        width: 500px;
    }

    .currency-width .el-input__inner {
        height: 35px;
        line-height: 35px;
        border-radius: 8px;
    }

    .currency-width .el-textarea__inner {
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

    .button-item {
        margin-top: 12px;
        padding: 9px 25px;
    }
</style>
<div id="app" v-cloak>
    <el-tabs v-model="activeName" type="border-card" @tab-click="handleClick" v-loading="loading">
        <el-tab-pane label="基础设置" name="setting">
            <setting ref="setting" :form-name="formName"></setting>
        </el-tab-pane>
        <el-tab-pane label="注册登录" name="basic">
            <register ref="basic" :form-name="formName"></register>
        </el-tab-pane>
        <div flex="main:center">
            <el-button class='button-item' :loading="btnLoading" type="primary" @click="store" size="small">保存</el-button>
        </div>
    </el-tabs>
</div>
<script>
    new Vue({
        el: '#app',
        data() {
            return {
                activeName: 'setting',
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
                        r: 'netb/setting/user',
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
                                r: 'netb/setting/user'
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