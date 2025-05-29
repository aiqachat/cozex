<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

$indSetting = (new \app\forms\admin\ConfigForm())->config();
?>
<script>const registeredBg = '<?=(!empty($indSetting['registered_bg'])) ? $indSetting['registered_bg'] : ''?>';</script>
<style>

    .el-header {
        font-size: 16px;
        color: #333;
        padding-top: 75px;
        text-align: center;
    }

    .el-header .title {
        height: 50px;
        font-size: 26px;
        width: 250px;
        margin: 0 auto 5px;
    }

    .el-header .title div {
        padding: 0 10px;
        width: 125px;
    }

    .logo {
        float: right;
        height: 50px;
    }

    .el-footer {
        background-color: #F6F6F6;
        color: #333;
        text-align: center;
        line-height: 60px;
    }

    .register-box {
        height: 100%;
    }

    .login {
        color: #409EFF;
        cursor: pointer;
    }

    .box-card {
        width: 700px;
        margin: 40px auto 0;
    }

    .audit-box {
        width: 700px;
        height: 280px;
    }

    .audit-success {
        color: #67C23A;
    }

    .box-card .el-form-item__label {
        height: 45px;
        line-height: 45px;
        font-size: 16px;
        padding-right: 25px;
    }

    .box-card .el-input .el-input__inner {
        height: 45px;
        border-radius: 22.5px;
    }

    .tips {
        color: #C5C5C5;
        font-size: 12px;
    }

    .box-card .code .el-input__inner {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .box-card .code .el-input-group__append {
        background-color: #007BFF;
        color: #fff;
        border-top-right-radius: 22.5px;
        border-bottom-right-radius: 22.5px;
        border: 1px solid #007BFF;
    }

    .el-form-item__content {
        width: 420px;
    }

    .id-card {
        position: relative;
        height: 170px;
        width: 190px;
        text-align: center;
        margin-bottom: 20px;
        font-size: 12px;
        color: #666;
    }

    .id-card img {
        height: 134px;
        width: 190px;
        background-color: #EEEFF3;
    }

    .id-card .add-info {
        position: absolute;
        top: 47px;
        left: 75px;
        height: 40px;
        width: 40px;
        text-align: center;
        line-height: 35px;
        border-radius: 50%;
        background-color: #528AFF;
        color: #fff;
        font-weight: bold;
        font-size: 20px;
        cursor: pointer;
    }

    .business .add-info {
        position: absolute;
        top: 109px;
        left: 75px;
        height: 40px;
        width: 40px;
        text-align: center;
        line-height: 35px;
        border-radius: 50%;
        background-color: #528AFF;
        color: #fff;
        font-weight: bold;
        font-size: 20px;
        cursor: pointer;
    }

    .business {
        position: relative;
        height: 280px;
        width: 190px;
        text-align: center;
        margin-bottom: 20px;
        font-size: 12px;
        color: #666;
        float: right;
    }

    .business img {
        height: 258px;
        width: 190px;
        background-color: #EEEFF3;
    }

    .submit-success {
        width: 210px;
        text-align: center;
        float: left;
    }

    .submit-success img {
        width: 210px;
        height: 180px;
        margin-bottom: 20px;
    }

    .box-card .forget-submit {
        margin: 20px;
        width: 120px;
        height: 45px;
        border-radius: 22.5px;
        font-size: 16px;
        text-align: center;
    }

    .el-footer {
        color: #ACACAC;
        text-align: center;
        line-height: 60px;
    }

    .el-footer a,
    .el-footer a:visited {
        color: #909399;
    }

    .card-upload {
        font-size: 0;
        position: relative;
        cursor: pointer;
    }

    .card-upload .el-button {
        position: absolute;
        left: 50%;
        top: 50%;
        margin-top: -16px;
        margin-left: -16px;
        z-index: 1;
    }

    .el-main {
        overflow: visible;
    }
</style>

<div id="app" v-cloak>
    <el-container class="register-box">
        <el-header height="190px" :style="'background: url('+banner+') center no-repeat;background-size: cover;'">
            <div class="title" flex="dir:left cross:center">
                <div>
                    <img class="logo" :src="logo" alt="">
                </div>
                <div style="height: 25px;width: 1px; background: #444;padding: 0"></div>
                <div>{{ forget ? "重置密码" : "欢迎注册"}}</div>
            </div>
            <div style="color: #7d7d7d">已有账号</span><span class="login" @click="login">登录</div>
        </el-header>
        <el-main>
            <el-steps v-if="!forget" :active="active" style="width:900px;margin: 65px auto" align-center>
                <el-step title="账号信息"></el-step>
                <el-step title="申请信息"></el-step>
                <el-step title="提交注册申请"></el-step>
            </el-steps>

            <div class="box-card">
                <el-form v-if="!forget" style="padding: 0 50px;" v-show="active != 3"
                         :model="ruleForm"
                         status-icon
                         :rules="rules"
                         ref="ruleForm"
                         label-width="150px"
                         size="small">
                    <div v-show="active == 1">
                        <el-form-item label="账户名" prop="username">
                            <el-input v-model="ruleForm.username" autocomplete="off" placeholder="请输入用户名"></el-input>
                        </el-form-item>
                        <el-form-item label="设置密码" prop="pass">
                            <el-input type="password" v-model="ruleForm.pass" autocomplete="off"
                                      placeholder="请输入密码"></el-input>
                        </el-form-item>
                        <el-form-item label="确认密码" prop="checkPass">
                            <el-input type="password" v-model="ruleForm.checkPass" autocomplete="off"
                                      placeholder="请再次输入密码"></el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-checkbox v-model="read">
                                <span>我已阅读并同意</span>
                                <el-button type="text" @click.stop="registerProtocolDialogVisible = true">《注册协议》
                                </el-button>
                            </el-checkbox>
                        </el-form-item>
                    </div>
                    <div v-show="active == 2">
                        <el-form-item label="姓名" prop="name">
                            <el-input v-model="ruleForm.name" autocomplete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="联系人手机号" prop="mobile">
                            <el-input v-model="ruleForm.mobile" autocomplete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="验证码" prop="captcha">
                            <el-input class="code" v-model="ruleForm.captcha">
                                <el-button @click="captcha" slot="append" round :disabled="sendSmsCaptchaDisabled">
                                    {{sendSmsCaptchaText}}
                                </el-button>
                            </el-input>
                        </el-form-item>
                        <el-form-item label="微信号" prop="wechat_id">
                            <el-input v-model="ruleForm.wechat_id" autocomplete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="申请原因" prop="remark">
                            <el-input type="textarea" :rows="5" v-model="ruleForm.remark" autocomplete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="上传证件信息" prop="info">
                            <div style="float: left">
                                <div class="id-card">
                                    <app-attachment v-model="ruleForm.id_card_front_pic" :simple="true">
                                        <div class="card-upload">
                                            <img v-if="ruleForm.id_card_front_pic" :src="ruleForm.id_card_front_pic">
                                            <img v-else :src="id_card">
                                            <el-button icon="el-icon-plus" circle type="primary"></el-button>
                                        </div>
                                    </app-attachment>
                                    <div>上传身份证正面</div>
                                </div>
                                <div class="id-card">
                                    <app-attachment v-model="ruleForm.id_card_back_pic" :simple="true">
                                        <div class="card-upload">
                                            <img v-if="ruleForm.id_card_back_pic" :src="ruleForm.id_card_back_pic">
                                            <img v-else :src="id_card_off">
                                            <el-button icon="el-icon-plus" circle type="primary"></el-button>
                                        </div>
                                    </app-attachment>
                                    <div>上传身份证反面</div>
                                </div>
                            </div>
                            <div class="business">
                                <app-attachment v-model="ruleForm.business_pic" :simple="true">
                                    <div class="card-upload">
                                        <img v-if="ruleForm.business_pic" :src="ruleForm.business_pic">
                                        <img v-else :src="business">
                                        <el-button icon="el-icon-plus" circle type="primary"></el-button>
                                    </div>
                                </app-attachment>
                                <div>上传营业执照</div>
                            </div>
                        </el-form-item>
                    </div>
                    <el-form-item>
                        <template v-if="active == 2">
                            <el-button style="float: right;font-size: 16px;width: 100px;margin-left: 20px" round
                                       :loading="btnLoading" type="primary" @click="store('ruleForm')">提交
                            </el-button>
                            <el-button style="float: right;font-size: 16px;width: 100px;" round @click="prev">上一步
                            </el-button>
                        </template>
                        <el-button type="primary" style="float: right;font-size: 16px;width: 100px;" v-else
                                   @click="next('ruleForm')" round :loading="nextStepLoading">下一步
                        </el-button>
                    </el-form-item>
                </el-form>
                <div v-show="active == 3" class="audit-box" flex="main:center">
                    <div class="submit-success">
                        <img :src="submit" alt="">
                        <div>{{text}}</div>
                        <el-button style="margin-top: 25px;" type="primary" round
                                   @click="$navigate({r:'admin/passport/login'})">我知道了
                        </el-button>
                    </div>
                </div>
                <div v-if="forget">
                    <el-form :model="form" size="small" label-width="150px" :rules="rules2" ref="form">
                        <el-form-item label="手机号" prop="mobile">
                            <el-input v-model="form.mobile" autocomplete="off" placeholder="请输入手机号"></el-input>
                        </el-form-item>
                        <el-form-item class="code" label="短信验证码" prop="captcha">
                            <el-input v-model="form.captcha" placeholder="请输入短信验证码">
                                <el-button @click="sendResetPasswordCaptcha" slot="append"
                                           :loading="sendResetPasswordCaptchaLoading"
                                           :disabled="sendResetPasswordCaptchaDisabled">{{sendResetPasswordCaptchaText}}
                                </el-button>
                            </el-input>
                        </el-form-item>
                        <el-form-item label="选择账户" prop="user_id">
                            <el-select v-model="form.user_id" placeholder="选择账户" style="width: 100%;">
                                <el-option
                                        v-for="item in user_list"
                                        :key="item.id"
                                        :label="item.username"
                                        :value="item.id">
                                    <span style="float: left">{{item.username}}</span>
                                    <span style="float: right; color: #909399;">{{item.nickname}}</span>
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="设置密码" prop="pass">
                            <el-input type="password" v-model="form.pass" placeholder="请输入密码"
                                      autocomplete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="确认密码" prop="checkPass">
                            <el-input type="password" v-model="form.checkPass" placeholder="请再次输入密码"
                                      autocomplete="off"></el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="primary" class="forget-submit" round @click="resetPasswordSubmit('form')"
                                       :loading="resetPasswordLoading">提交
                            </el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
        </el-main>
        <el-footer class="el-footer">
            <?php if ($indSetting && !empty($indSetting['copyright'])) : ?>
                <a style="text-decoration: none" href="<?= $indSetting['copyright_url'] ?>"
                   target="_blank"><?= $indSetting['copyright'] ?></a>
            <?php else : ?>
                &copy;2019 <a href="https://www.netbcloud.com" target="_blank">深圳网商天下科技</a>
            <?php endif; ?>
        </el-footer>

        <el-dialog title="注册协议" :visible.sync="registerProtocolDialogVisible" :close-on-click-modal="false">
            <div style="width: 100%;overflow-x: auto">
                <?= ($indSetting && !empty($indSetting['register_protocol'])) ? $indSetting['register_protocol'] : '' ?>
            </div>
            <div slot="footer">
                <el-button type="primary" @click="registerProtocolDialogVisible = false; read = true;">我已阅读并同意
                </el-button>
            </div>
        </el-dialog>

    </el-container>
</div>
<script>
    let checkUsernameCallback = null;
    const app = new Vue({
        el: '#app',
        data() {
            var checkPhone = (rule, value, callback) => {
                if (!value) {
                    return callback(new Error("手机号不能为空"));
                } else {
                    const reg = /0?(13|14|15|16|17|18|19)[0-9]{9}/
                    if (reg.test(value)) {
                        callback();
                    } else {
                        return callback(new Error("请输入正确的手机号"));
                    }
                }
            };
            var validatePass = (rule, value, callback) => {
                if (value === '' || value === undefined) {
                    callback(new Error("请输入密码"));
                } else if (value.length < 6 || value.length > 15) {
                    callback(new Error("密码应在6至15位之间"));
                } else {
                    if (this.ruleForm.checkPass !== '') {
                        this.$refs.ruleForm.validateField('checkPass');
                    }
                    callback();
                }
            };
            var validatePass2 = (rule, value, callback) => {
                if (value === '' || value === undefined) {
                    callback(new Error("请再次输入密码"));
                } else if (value.length < 6 || value.length > 15) {
                    callback(new Error("密码应在6至15位之间"));
                } else if (value !== this.ruleForm.pass) {
                    callback(new Error("两次输入密码不一致!"));
                } else {
                    callback();
                }
            };
            var validatePass3 = (rule, value, callback) => {
                if (value === '' || value === undefined) {
                    callback(new Error("请再次输入密码"));
                } else if (value.length < 6 || value.length > 15) {
                    callback(new Error("密码应在6至15位之间"));
                } else if (value !== this.form.pass) {
                    callback(new Error("两次输入密码不一致!"));
                } else {
                    callback();
                }
            };
            var checkName = (rule, value, callback) => {
                checkUsernameCallback = callback;
                console.log(value);
                if (value === '' || value === undefined) {
                    callback(new Error("请输入用户名"));
                } else if (value.length < 6 || value.length > 15) {
                    callback(new Error("用户名长度应在6至15位之间"));
                } else {
                    const reg = /^[a-zA-Z][a-zA-Z0-9_]*$/;
                    if (reg.test(value)) {
                        callback();
                    } else {
                        return callback(new Error("账户名必须是字母开头"));
                    }
                }
            };
            var checkPic = (rule, value, callback) => {
                if (!this.rules.info[0].required) {
                    callback();
                }
                if (this.ruleForm.id_card_front_pic === '') {
                    callback(new Error("请上传身份证正面信息"));
                } else if (this.ruleForm.id_card_back_pic === '') {
                    callback(new Error("请上传身份证反面信息"));
                } else if (this.ruleForm.business_pic === '') {
                    callback(new Error("请上传营业执照"));
                } else {
                    callback();
                }
            };
            return {
                registerProtocolDialogVisible: false,
                logo: _siteLogo,
                banner: registeredBg ? registeredBg : (_baseUrl + '/statics/img/admin/B.png'),
                id_card: _baseUrl + '/statics/img/admin/id-card.png',
                id_card_off: _baseUrl + '/statics/img/admin/id-card-off.png',
                business: _baseUrl + '/statics/img/admin/business.png',
                submit: _baseUrl + '/statics/img/admin/submit.png',
                active: 1,
                nextStepLoading: false,
                btnLoading: false,
                read: false,
                form: {
                    user_id: '',
                    pass: '',
                    checkPass: '',
                },
                forget: false,
                ruleForm: {
                    pass: '',
                    checkPass: '',
                    wechat_id: '',
                    id_card_front_pic: '',
                    id_card_back_pic: '',
                    business_pic: '',
                },
                rules2: {
                    user_id: [
                        {required: true, message: "请选择账户", trigger: 'blur'},
                    ],
                    pass: [
                        {required: true, validator: validatePass, trigger: 'blur'}
                    ],
                    checkPass: [
                        {required: true, validator: validatePass3, trigger: 'blur'}
                    ],
                    code: [
                        {required: true, message: "验证码不能为空", trigger: 'blur'},
                    ],
                    mobile: [
                        {required: true, message: "手机号不能为空", trigger: 'blur'},
                    ],
                    captcha: [
                        {required: true, message: "手机验证码不能为空", trigger: 'blur'},
                    ],
                },
                rules: {
                    username: [
                        {required: true, validator: checkName, trigger: 'change'}
                    ],
                    pass: [
                        {required: true, validator: validatePass, trigger: 'change'}
                    ],
                    checkPass: [
                        {required: true, validator: validatePass2, trigger: 'change'}
                    ],
                    name: [
                        {required: true, message: "姓名/企业名不能为空", trigger: 'blur'},
                    ],
                    mobile: [
                        {required: true, validator: checkPhone, trigger: 'blur'},
                    ],
                    captcha: [
                        {required: true, message: "验证码不能为空", trigger: 'blur'},
                    ],
                    remark: [
                        {required: true, message: "申请原因不能为空", trigger: 'blur'},
                    ],
                    info: [
                        {required: false, validator: checkPic, trigger: 'blur'}
                    ],
                },
                sendSmsCaptchaLoading: false,
                sendSmsCaptchaDisabled: false,
                sendSmsCaptchaText: "获取短信验证码",

                sendResetPasswordCaptchaLoading: false,
                sendResetPasswordCaptchaDisabled: false,
                sendResetPasswordCaptchaText: "获取短信验证码",
                user_list: [],
                resetPasswordLoading: false,
                text: "您的注册申请已提交",
            };
        },
        created() {
            if (getQuery('active') == 3) {
                this.active = 3;
            }
            if (getQuery('status') == 'forget') {
                this.forget = true;
                this.banner = _baseUrl + '/statics/img/admin/C.png'
            }
            if ("<?= $indSetting['is_required'] ?>" == 1) {
                this.rules.info[0].required = true;
            }
        },
        methods: {
            login() {
                navigateTo({
                    r: 'admin/passport/login',
                });
            },
            store(formName) {
                let self = this;
                self.$refs[formName].validate((valid) => {
                    if (valid) {
                        self.btnLoading = true;
                        request({
                            params: {
                                r: 'admin/passport/register'
                            },
                            method: 'post',
                            data: {
                                form: self.ruleForm
                            }
                        }).then(e => {
                            this.btnLoading = false;
                            if (e.data.code === 0) {
                                this.$message.success(e.data.msg);
                                this.text = e.data.msg;
                                this.active++;
                            } else {
                                this.$message.error(e.data.msg);
                            }
                        }).catch(e => {
                            this.btnLoading = false;
                            console.log(e);
                        });
                    } else {
                        this.$message.success("请完整填写表单");
                        return false;
                    }
                });
            },
            onUsernameChange() {
            },
            // 步骤条 下一步
            next(formName) {
                let self = this;
                let isValid = true;
                if (this.read) {
                    self.$refs[formName].validateField('username', function (error) {
                        if (error) {
                            isValid = false;
                        }
                    });
                    self.$refs[formName].validateField('pass', function (error) {
                        if (error) {
                            isValid = false;
                        }
                    });
                    self.$refs[formName].validateField('checkPass', function (error) {
                        if (error) {
                            isValid = false;
                        }
                    });
                    if (isValid) {
                        this.nextStepLoading = true;
                        this.$request({
                            params: {
                                r: 'admin/passport/check-user-exists',
                            },
                            data: {
                                username: this.ruleForm.username,
                            },
                            method: 'post',
                        }).then(e => {
                            this.nextStepLoading = false;
                            if (e.data.code === 0) {
                                if (e.data.data.is_exists == 0) {
                                    if (this.active++ > 2) this.active = 0;
                                } else {
                                    this.$message.error("该账户名已被使用");
                                }
                            }
                        }).catch(e => {
                            this.nextStepLoading = false;
                        });
                    }
                } else {
                    this.$message.error("请先阅读并同意");
                }

            },
            // 上一步
            prev() {
                this.active--;
            },
            // 获取验证码
            captcha() {
                let self = this;
                if (!self.ruleForm.mobile) {
                    self.$message.error("请先填写手机号");
                    return
                }
                this.sendSmsCaptchaLoading = true;
                this.$request({
                    params: {
                        r: 'admin/passport/sms-captcha'
                    },
                    method: 'post',
                    data: {
                        mobile: self.ruleForm.mobile
                    }
                }).then(e => {
                    this.sendSmsCaptchaLoading = false;
                    if (e.data.code === 0) {
                        this.$message.success(e.data.msg);
                        this.ruleForm.validate_code_id = e.data.data.validate_code_id;
                        this.sendSmsCaptchaDisabled = true;
                        let second = 60;
                        const timer = setInterval(() => {
                            if (second <= 0) {
                                this.sendSmsCaptchaDisabled = false;
                                this.sendSmsCaptchaText = "获取短信验证码";
                                clearInterval(timer);
                                return;
                            }
                            second = second - 1;
                            this.sendSmsCaptchaText = second + 's';
                        }, 1000);
                    } else {
                        this.$message.error(e.data.msg);
                    }
                }).catch(e => {
                    console.log(e);
                });
            },
            sendResetPasswordCaptcha() {
                this.$refs['form'].validateField('mobile', error => {
                    if (error) {
                        return;
                    }
                    this.sendResetPasswordCaptchaLoading = true;
                    this.$request({
                        params: {
                            r: 'admin/passport/send-reset-password-captcha',
                        },
                        data: {
                            mobile: this.form.mobile,
                        },
                        method: 'post',
                    }).then(e => {
                        this.sendResetPasswordCaptchaLoading = false;
                        if (e.data.code === 0) {
                            this.form.validate_code_id = e.data.data.validate_code_id;
                            this.user_list = e.data.data.user_list;
                            this.sendResetPasswordCaptchaDisabled = true;
                            let second = 60;
                            const timer = setInterval(() => {
                                if (second <= 0) {
                                    this.sendResetPasswordCaptchaDisabled = false;
                                    this.sendResetPasswordCaptchaText = "获取短信验证码";
                                    clearInterval(timer);
                                    return;
                                }
                                second = second - 1;
                                this.sendResetPasswordCaptchaText = second + 's';
                            }, 1000);
                        } else {
                            this.$message.error(e.data.msg);
                        }
                    }).catch(e => {
                    });
                });
            },
            resetPasswordSubmit(formName) {
                this.$refs[formName].validate(valid => {
                    if (!valid) {
                        return false;
                    }
                    this.resetPasswordLoading = true;
                    this.$request({
                        params: {
                            r: 'admin/passport/reset-password',
                        },
                        data: this.form,
                        method: 'post',
                    }).then(e => {
                        if (e.data.code === 0) {
                            this.$alert(e.data.msg, "提示").then(() => {
                                this.$navigate({r: 'admin/passport/login'});
                            });
                        } else {
                            this.resetPasswordLoading = false;
                            this.$message.error(e.data.msg);
                        }
                    }).catch(e => {
                        this.resetPasswordLoading = false;
                    });
                });
            },
        }
    });
</script>
