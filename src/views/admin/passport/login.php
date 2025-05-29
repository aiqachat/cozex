<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

$indSetting = (new \app\forms\admin\ConfigForm())->config();
?>
<script>const passportBg = '<?=(!empty($indSetting['passport_bg'])) ? $indSetting['passport_bg'] : ''?>';</script>
<style>
    .login {
        width: 100%;
        min-height: 880px;
        height: 100%;
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .login .box-card {
        position: relative;
        border-radius: 15px;
        z-index: 99;
        border: 0;
        width: 470px;
        height: 510px;
        margin: 0 auto;
    }

    .el-card__body {
        padding: 0;
        display: flex;
        width: 1080px;
    }

    .login .box-card .right-box{
        padding-top: 40px;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 470px;
    }

    .msg-logo {
        height: 60px;
    }

    .login-form {
        position: relative;
        line-height: 20px;
        color: #101010;
        font-size: 14px;
    }

    .form-title {
        height: 42px;
        line-height: 42px;
        color: #00d900;
        font-size: 24px;
        font-weight: 700;
        text-align: center;
        margin-bottom: 20px;
    }

    .form-box{
        padding-top: 20px;
        width: 330px;
    }

    .el-input .el-input__inner {
        height: 55px;
        background-color: #f8f8f8;
        border: none;
        -webkit-box-shadow: 0 0 1000px #f8f8f8 inset;
    }

    .el-input-group__append, .el-input-group__prepend{
        border: none;
    }

    .login-btn {
        width: 100%;
        border-radius: 20px;
        height: 40px;
        line-height: 40px;
        font-size: 16px;
        color: white;
        text-align: center;
        cursor: pointer;
    }

    .opacity {
        background-color: rgba(0, 0, 0, 0.01);
        height: 100%;
        width: 100%;
        position: absolute;
        left: 0;
        top: 0;
        z-index: 1;
    }

    .foot {
        position: absolute;
        left: 0;
        right: 0;
        width: auto;
        color: #fff;
        text-align: center;
        font-size: 16px;
    }

    .foot a, .foot a:visited {
        color: #ffffff;
    }

    .pic-captcha {
        width: 100px;
        height: 55px;
        vertical-align: middle;
        cursor: pointer;
        margin-left: 20px;
    }

    .register_box {
        text-align: center;
        width: 330px;
        margin-top: 30px;
    }

    .register {
        display: inline-block;
        width: 48%;
        height: 15px;
        line-height: 15px;
        text-align: center;
        cursor: pointer;
        color: #00d900;
    }

    .bi {
        font-size: 16px;
    }
</style>
<script src="<?= Yii::$app->request->baseUrl ?>/statics/js/crypto-js.min.js"></script>
<div id="app" v-cloak>
    <div class="login" :style="{'background-image':'url('+login_bg+')'}">
        <div class="opacity">
            <div flex="main:center" style="margin: 18vh  0 20px 0;">
                <img class="msg-logo" :src="login_logo" alt="">
            </div>
            <el-card class="box-card" shadow="always">
                <div class="right-box">
                    <el-form :model="ruleForm" class="login-form" :rules="rules2" ref="ruleForm" label-width="0"
                             size="small" autocomplete="off">
                        <div class="form-title" :style="{'color': mainColor}">管理员登录</div>
                        <div class="form-box">
                            <el-form-item prop="username">
                                <el-input @keyup.enter.native="login('ruleForm')" placeholder="请输入用户名"
                                          v-model="ruleForm.username">
                                    <template slot="prepend">
                                        <i class="bi bi-person-fill"></i>
                                    </template>
                                </el-input>
                            </el-form-item>
                            <el-form-item prop="password">
                                <div class="ws-input2">
                                    <el-input  class="ws-input2" @keyup.enter.native="login('ruleForm')" :type="pwdType" placeholder="请输入密码"
                                               v-model="ruleForm.password">
                                        <template slot="prepend">
                                            <i class="bi bi-lock-fill"></i>
                                        </template>
                                        <template slot="append">
                                            <i :class="'bi ' + (pwdType == '' ? 'bi-eye-fill' : 'bi-eye-slash-fill')" style="font-size: 20px;cursor: pointer;" @click="showPwd"></i>
                                        </template>
                                    </el-input>
                                </div>
                            </el-form-item>
                            <el-form-item prop="pic_captcha">
                                <div class="ws-input3">
                                    <el-input class="ws-input3" @keyup.enter.native="login('ruleForm')" placeholder="验证码"
                                              style="width: 205px"
                                              v-model="ruleForm.pic_captcha">
                                        <template slot="prepend">
                                            <i class="bi bi-shield-lock-fill"></i>
                                        </template>
                                    </el-input>
                                    <img :src="pic_captcha_src" class="pic-captcha" @click="loadPicCaptcha">
                                </div>
                            </el-form-item>
                            <el-form-item>
                                <el-checkbox v-model="ruleForm.checked">记住我，以后自动登录</el-checkbox>
                            </el-form-item>
                            <el-form-item>
                                <div class="login-btn" :style="{'background-color': mainColor, 'box-shadow': '0 4px 5px '+mainColor+'55'}" @click="login('ruleForm')">登录</div>
                            </el-form-item>
                        </div>
                    </el-form>
                    <div class="register_box">
                        <span class="register" :style="{'color': mainColor}" @click="forget">忘记密码</span>
                        <?php if (isset($indSetting['open_register'])
                            && $indSetting['open_register'] == 1) : ?>
                            <span class="register" :style="{'color': mainColor}" style="border-left: 1px solid #a9a9a9;" @click="register">注册账号</span>
                        <?php endif; ?>
                    </div>
                </div>
            </el-card>

            <!--忘记密码-->
            <div class="foot" :style="{'bottom': footHeight}">
                <?php if (!empty($indSetting['copyright'])) : ?>
                    <a style="text-decoration: none" href="<?= $indSetting['copyright_url'] ?? '#' ?>"
                       target="_blank"><?= $indSetting['copyright'] ?></a><br />
                <?php endif; ?>
                <?php if ($indSetting && !empty($indSetting['edition'])) : ?>
                    <a style="text-decoration: none" href=""
                       target="_blank">版本：<?= $indSetting['edition'] ?></a>
               <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                login_bg: passportBg ? passportBg : _baseUrl + '/statics/img/admin/BG.png',
                login_logo: _siteLogo,
                mainColor: '#0062d9',
                footHeight: '5%',
                btnLoading: false,
                dialogFormVisible: false,
                user_type: '1',
                ruleForm: {
                    pic_captcha: '',
                    checked: false
                },
                rules2: {
                    username: [
                        {required: true, message: '请输入用户名', trigger: 'blur'},
                    ],
                    password: [
                        {required: true, message: '请输入密码', trigger: 'blur'},
                    ],
                    pic_captcha: [
                        {required: true, message: '请输入右侧图片上的文字', trigger: 'blur'},
                    ],
                },
                pic_captcha_src: null,
                desKey: '<?= !empty($key) ? $key : "123456"; ?>', // 加密key @czs
                pwdType: 'password',
            };
        },
        created() {
            this.loadPicCaptcha();
        },
        methods: {
            showPwd() {
                if (this.pwdType === 'password') {
                    this.pwdType = ''
                } else {
                    this.pwdType = 'password'
                }
            },
            login(formName) {
                let self = this;
                self.$refs[formName].validate((valid) => {
                    if (valid) {
                        self.btnLoading = true;
                        let data = JSON.parse(JSON.stringify(self.ruleForm));
                        data.password = self.encrypt(self.ruleForm.password, self.desKey, self.desKey); // 密码加密 @czs
                        request({
                            params: {
                                r: 'admin/passport/login'
                            },
                            method: 'post',
                            data: {
                                form: data,
                                user_type: self.user_type,
                                mall_id: getQuery('mall_id'),
                            }
                        }).then(e => {
                            self.btnLoading = false;
                            if (e.data.code === 0) {
                                this.$message.success(e.data.msg);
                                self.$navigate({
                                    r: e.data.data.url,
                                });
                            } else {
                                if (e.data.data && e.data.data.register) {
                                    this.$navigate({r: 'admin/passport/register', active: 3});
                                }
                                this.loadPicCaptcha();
                                this.$message.error(e.data.msg);
                            }
                        }).catch(e => {
                            console.log(e);
                        });
                    } else {
                        console.log('error submit!!');
                        return false;
                    }
                });
            },
            encrypt(str, key, iv) { // 密码加密传输 @czs
                let encode_str = '';
                key = CryptoJS.MD5(key).toString();
                iv = CryptoJS.MD5(iv).toString();
                let crypto_key = CryptoJS.enc.Utf8.parse(key);
                let crypto_iv = CryptoJS.enc.Utf8.parse(iv.substr(0, 8));
                if (typeof (str) == 'string') {
                    encode_str = CryptoJS.TripleDES.encrypt(str, crypto_key, {
                        iv: crypto_iv,
                        mode: CryptoJS.mode.CBC,
                        padding: CryptoJS.pad.Pkcs7
                    });
                } else {
                    encode_str = CryptoJS.TripleDES.encrypt(JSON.stringify(str), crypto_key, {
                        iv: crypto_iv,
                        mode: CryptoJS.mode.CBC,
                        padding: CryptoJS.pad.Pkcs7
                    });
                }
                return encode_str.toString();
            },
            forget() {
                navigateTo({
                    r: 'admin/passport/register',
                    status: 'forget'
                });
            },
            register() {
                navigateTo({
                    r: 'admin/passport/register',
                });
            },
            loadPicCaptcha() {
                this.$request({
                    noHandleError: true,
                    params: {
                        r: 'site/pic-captcha',
                        refresh: true,
                    },
                }).then(response => {
                }).catch(response => {
                    if (response.data.url) {
                        this.pic_captcha_src = response.data.url;
                    }
                });
            },
        },
        mounted: function () {
            let height = document.body.clientHeight;
            this.footHeight = height < 600 ? '1%' : '5%'
        }
    });
</script>