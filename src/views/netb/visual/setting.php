<?php
require __DIR__ . '/setting/common.php';
require __DIR__ . '/setting/basic.php';
require __DIR__ . '/setting/ark.php';
require __DIR__ . '/setting/ark_global.php';
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
        background-color: #ffffff;
        width: 50%;
    }
</style>
<div id="app" v-cloak>
    <el-tabs v-model="activeName" type="border-card" @tab-click="handleClick" v-loading="loading">
        <el-tab-pane label="即梦AI设置" name="one">
            <basic ref="one" :form-name="formName" :keys="keys" @refresh-keys="refreshKeys"></basic>
        </el-tab-pane>
        <el-tab-pane label="火山方舟国内版" name="two">
            <ark ref="two" :form-name="formName" :keys="keys" @refresh-keys="refreshKeys"></ark>
        </el-tab-pane>
        <el-tab-pane label="火山方舟国际版" name="three">
            <ark-global ref="three" :form-name="formName" :keys="keys" @refresh-keys="refreshKeys"></ark-global>
        </el-tab-pane>
        <div class='bottom-div' flex="main:center">
            <el-button class='button-item' :loading="btnLoading" type="primary" @click="store"
                size="small">保存</el-button>
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
                keys: [],
            };
        },
        mounted() {
            this.getDetail();
            this.getKeys();
        },
        methods: {
            handleClick(tab, event) {
               this.getDetail()
            },
            getKeys() {
                request({
                    params: { r: 'netb/index/volcengine-all' },
                }).then(e => {
                    if (e.data.code === 0) {
                        this.keys = e.data.data;
                    } else {
                        this.$message.error(e.data.msg);
                    }
                });
            },
            refreshKeys() {
                this.getKeys();
            },
            getDetail() {
                this.loading = true;
                request({
                    params: {
                        r: 'netb/visual/setting',
                        tab: this.activeName
                    },
                    method: 'get',
                }).then(e => {
                    if (e.data.code === 0) {
                        this.$refs[this.activeName].form = Object.assign({}, e.data.data.data)
                        if (this.$refs[this.activeName].default) {
                            this.$refs[this.activeName].default = Object.assign({}, e.data.data.default)
                        }
                        if (e.data.data.models) {
                            this.$refs[this.activeName].models = e.data.data.models
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
                                r: 'netb/visual/setting'
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