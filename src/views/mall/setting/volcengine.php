<?php
/**
 * Created by PhpStorm
 * User: chenzs
 * Date: 2024/9/25
 * Time: 4:08 下午
 * @copyright: ©2024 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */
?>
<style></style>
<div id="app" v-cloak>
    <el-card shadow="never" v-loading="cardLoading">
        <el-form @submit.native.prevent :model="form" :rules="rules" label-width="150px" ref="form">
            <el-tabs v-model="activeName" @tab-click="handleClick">
                <el-tab-pane label="基本配置" name="basic">
                    <el-col :span="16" style="margin-top: 24px;">
                        <el-form-item class="switch" label="app_id" prop="app_id">
                            <el-input size="small" placeholder="请输入APPID" v-model.trim="form.app_id" autocomplete="off"></el-input>
                        </el-form-item>
                        <el-form-item class="switch" label="Access Token" prop="access_token">
                            <el-input size="small" placeholder="请输入TOKEN" v-model.trim="form.access_token" autocomplete="off"></el-input>
                        </el-form-item>
                    </el-col>
                </el-tab-pane>
            </el-tabs>
            <el-button :loading="btnLoading" class="button-item" size="small" type="primary" @click="store('form')" size="small">保存</el-button>
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
                rules: {},
            };
        },
        created() {
            this.getDetail();
        },
        methods: {
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
                                r: 'mall/setting/volcengine'
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
                        r: 'mall/setting/volcengine',
                    },
                    method: 'get',
                }).then(e => {
                    self.cardLoading = false;
                    if (e.data.code === 0) {
                        self.form = Object.assign({}, e.data.data)
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