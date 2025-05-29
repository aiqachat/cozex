<?php
/**
 * Created by PhpStorm.
 */
?>
<style>
    .form-body {
        padding: 20px 50% 20px 20px;
        background-color: #fff;
        margin-bottom: 20px;
    }

    .form-button {
        margin: 0 !important;
    }

    .form-button .el-form-item__content {
        margin-left: 0 !important;
    }

    .button-item {
        padding: 9px 25px;
    }
</style>
<template id="app-mail-setting">
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 0;" v-cloak
             v-loading="listLoading">
        <el-form size="small" :model="ruleForm" ref="ruleForm" :rules="rules" label-width="150px">
            <div class="form-body">
                <el-form-item label="发送平台" prop="status">
                    <el-input v-model="ruleForm.send_platform"></el-input>
                    <div class="fs-sm">
                        smtp.qq.com（QQ邮箱），密码是授权码
                    </div>
                </el-form-item>
                <el-form-item label="发件人邮箱" prop="send_mail">
                    <el-input v-model="ruleForm.send_mail"></el-input>
                </el-form-item>
                <el-form-item label="授权码" prop="send_pwd">
                    <el-input @focus="updateHideStatus"
                              v-if="hide"
                              readonly
                              placeholder="授权码 被隐藏,点击查看">
                    </el-input>
                    <el-input v-else v-model="ruleForm.send_pwd"></el-input>
                    <div class="fs-sm">
                        <el-button @click="goto" type="text" style="color: #92959B">什么是授权码<i
                                class="el-icon-question"></i></el-button>
                    </div>
                </el-form-item>
                <el-form-item label="发件平台名称" prop="send_name">
                    <el-input v-model="ruleForm.send_name"></el-input>
                </el-form-item>
                <el-form-item>
                    <template slot="label">
                        <span>测试邮箱</span>
                    </template>
                    <el-input style="width: 200px" v-model="ruleForm.receive_mail"></el-input>
                </el-form-item>
                <el-form-item label="" prop="receive_mail">
                    <el-button size="small" @click="testSubmit('ruleForm')"
                               :loading="testLoading">测试发送
                    </el-button>
                </el-form-item>
            </div>
            <el-form-item class="form-button">
                <el-button sizi="mini" class="button-item" :loading="submitLoading" type="primary"
                           @click="onSubmit('ruleForm')">
                    保存
                </el-button>
            </el-form-item>
        </el-form>
    </el-card>
</template>
<script>
    Vue.component('app-mail-setting', {
        template: '#app-mail-setting',
        data() {
            let validator = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('请输入发件人邮箱'));
                } else {
                    callback();
                }
            };
            return {
                ruleForm: {
                    send_mail: '',
                    send_pwd: '',
                    send_name: '',
                    receive_mail: '',
                    send_platform: '',
                    test: 0,
                },
                mail: '',
                testLoading: false,
                submitLoading: false,
                listLoading: false,
                rules: {
                    send_mail: [
                        {
                            validator: validator, trigger: 'blur', message: '请输入发件人邮箱'
                        }
                    ],
                    send_pwd: [
                        {
                            validator: validator, trigger: 'blur', message: '请输入授权码'
                        }
                    ],
                    send_name: [
                        {
                            validator: validator, trigger: 'blur', message: '请输入发件平台名称'
                        }
                    ]
                },
                hide: true
            }
        },
        mounted: function () {
            this.load();
        },
        methods: {
            load() {
                this.listLoading = true;
                request({
                    params: {
                        r: 'netb/index/mail'
                    },
                    method: 'get'
                }).then(e => {
                    this.listLoading = false;
                    this.ruleForm = Object.assign(this.ruleForm, e.data.data.model);
                }).catch(e => {
                    this.listLoading = false;
                    this.$message.error(e.data.msg);
                });
            },
            onSubmit(formName) {
                this.ruleForm.test = 0;
                this.submitLoading = true;
                this.submit(formName);
            },
            updateHideStatus() {
                this.hide = false;
            },
            goto() {
                navigateTo('https://service.mail.qq.com/cgi-bin/help?subtype=1&&no=1001256&&id=28', true);
            },
            testSubmit(formName) {
                this.ruleForm.test = 1;
                this.testLoading = true;
                this.submit(formName)
            },
            submit(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        request({
                            params: {
                                r: 'netb/index/mail'
                            },
                            method: 'post',
                            data: {
                                form: this.ruleForm
                            }
                        }).then(e => {
                            this.submitLoading = false;
                            this.testLoading = false;
                            if (e.data.code === 0) {
                                this.$message.success(e.data.msg);
                            } else {
                                this.$message.error(e.data.msg);
                            }
                        }).catch(e => {
                            this.submitLoading = false;
                            this.testLoading = false;
                            this.$message.error(e.data.msg);
                        });
                    } else {
                        console.log('error submit!!');
                        this.submitLoading = false;
                        return false;
                    }
                })
            },
        }
    });
</script>

