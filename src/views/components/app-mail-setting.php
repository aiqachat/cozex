<?php
/**
 * Created by PhpStorm.
 */
Yii::$app->loadViewComponent('app-rich-text')
?>
<style>
    .form-button {
        margin: 20px 0 0 !important;
    }

    .form-button .el-form-item__content {
        margin-left: 0 !important;
    }

    .button-item {
        padding: 9px 25px;
    }

    .el-form-item {
        padding-left: 50px;
    }

    .el-form-item .el-input {
         width: 50vh;
     }

    .el-form-item .app-rich-text {
         width: 70vh;
     }
</style>
<template id="app-mail-setting">
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 0;" v-cloak
             v-loading="listLoading">
        <el-form size="small" :model="ruleForm" ref="ruleForm" :rules="rules" label-position="top">
            <el-tabs type="border-card" v-model="activeName">
                <el-tab-pane label="基础信息" name="one">
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
                    <el-form-item label="平台标题（中文）" prop="subject_name">
                        <el-input v-model="ruleForm.subject_name"></el-input>
                    </el-form-item>
                    <el-form-item label="平台标题（英文）" prop="subject_name">
                        <el-input v-model="ruleForm.language_data.en.subject_name"></el-input>
                    </el-form-item>
                    <el-form-item label="发件人名称（中文）" prop="send_name">
                        <el-input v-model="ruleForm.send_name"></el-input>
                    </el-form-item>
                    <el-form-item label="发件人名称（英文）" prop="send_name">
                        <el-input v-model="ruleForm.language_data.en.send_name"></el-input>
                    </el-form-item>
                    <el-form-item label="测试邮箱">
                        <el-input style="width: 280px" v-model="ruleForm.receive_mail"></el-input>
                        <el-button size="small" @click="testSubmit('ruleForm')" :loading="testLoading">测试发送</el-button>
                    </el-form-item>
                </el-tab-pane>
                <el-tab-pane label="扩展信息" name="two">
                    <el-form-item label="邮件描述说明（中文）" prop="desc">
                        <app-rich-text v-model.trim="ruleForm.desc" :is-dark="false"></app-rich-text>
                    </el-form-item>
                    <el-form-item label="邮件描述说明（英文）" prop="desc">
                        <app-rich-text v-model.trim="ruleForm.language_data.en.desc" :is-dark="false"></app-rich-text>
                    </el-form-item>
                </el-tab-pane>
            </el-tabs>
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
            return {
                ruleForm: {
                    send_mail: '',
                    send_pwd: '',
                    send_name: '',
                    subject_name: '',
                    receive_mail: '',
                    send_platform: '',
                    test: 0,
                    language_data: {en : {}}
                },
                mail: '',
                testLoading: false,
                submitLoading: false,
                listLoading: false,
                rules: {
                    send_mail: [
                        {
                            required: true, trigger: 'blur', message: '请输入发件人邮箱'
                        }
                    ],
                    send_pwd: [
                        {
                            required: true, trigger: 'blur', message: '请输入授权码'
                        }
                    ],
                },
                hide: true,

                activeName: 'one',
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
                    if (!this.ruleForm.language_data.en) {
                        this.ruleForm.language_data = {en : {}};
                    }
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
                        this.$message.error('有必填项未填写，请检查');
                        this.submitLoading = false;
                        return false;
                    }
                })
            },
        }
    });
</script>

