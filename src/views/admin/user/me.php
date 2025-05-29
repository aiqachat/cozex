<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 */
?>
<div id="app" v-cloak>
    <el-card shadow="never">
        <el-row>
            <el-col class="user-info" :span='12'>
                <div class="user-text">
                    <div class="user-label">用户名</div>
                    <div>{{userinfo.nickname}}</div>
                </div>
                <img class="info-bg" :src="user" alt="">
            </el-col>
            <el-col class="user-info" :span='12'>
                <div class="user-text">
                    <div class="user-label">手机号</div>
                    <div>
                        <span>{{userinfo.mobile}}</span>
                        <img style="height: 16px;width: 16px;margin-left: 5px;cursor: pointer" @click="changeMobile = true" src="statics/img/admin/change.png" alt="">
                    </div>
                </div>
                <img class="info-bg" :src="mobile" alt="">
            </el-col>
            <el-col class="user-info" :span='12'>
                <div class="user-text">
                    <div class="user-label">可创建数量</div>
                    <div>{{userinfo.app_max_count}}</div>
                </div>
                <img class="info-bg" :src="number" alt="">
            </el-col>
            <el-col class="user-info" :span='12'>
                <div class="user-text">
                    <div class="user-label">帐号有效期</div>
                    <div>{{userinfo.expired_at}}</div>
                </div>
                <img class="info-bg" :src="end_time" alt="">
            </el-col>
        </el-row>
    </el-card>
    <el-dialog title="修改手机号" :visible.sync="changeMobile" width="30%">
        <el-form :model='newList' :rules="rules" size="small" ref="form" label-width='100px'>
            <!-- <el-form-item :label="check ? '新手机号':'手机号'" prop="mobile"> -->
            <el-form-item label="新手机号" prop="mobile">
                <!-- <div v-if="check"> -->
                <el-input type="number" style="width: 90%" v-model="newList.mobile" placeholder="填写新的手机号码"></el-input>
                <!-- </div> -->
                <!-- <div v-else>{{userinfo.mobile}}</div> -->
            </el-form-item>
            <el-form-item label="验证码" prop="code">
                <el-input type="number" v-model="newList.code" style="width: 58%" placeholder="填写收到的验证码"></el-input>
                <el-button v-if="beGet" type="info" disabled style="margin-left: 2%">{{sec}}s后重新获取</el-button>
                <el-button type="primary" v-else style="margin-left: 2%" @click="getCode">获取验证码</el-button>
            </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
        <el-button size="small" @click="changeMobile = false;check = false">取消</el-button>
        <el-button size="small" type="primary" :loading="btnloading" @click="change('form')">确定</el-button>
            <!-- <el-button size="small" type="primary" v-else @click="checkCode">下一步</el-button> -->
      </span>
    </el-dialog>

</div>

<style>
    .el-dialog {
        min-width: 400px;
    }

    .user-info {
        height: 210px;
        padding: 40px;
        position: relative;
    }

    .user-info .info-bg {
        position: absolute;
        right: 40px;
        top: 40px;
        height: 130px;
        width: 174px;
    }

    .user-text {
        padding: 40px 20px 20px;
        font-size: 24px;
    }

    .user-label {
        font-size: 16px;
        color: #999999;
        margin-bottom: 5px;
    }
</style>

<script>
    new Vue({
        el: '#app',
        data() {
            var checkPhone = (rule, value, callback) => {
                if (!value) {
                    return callback(new Error("手机号不能为空"));
                } else {
                    const reg = /^.{11}$/
                    if (reg.test(value)) {
                        callback();
                    } else {
                        return callback(new Error("请输入正确的手机号"));
                    }
                }
            };
            return {
                newList: {
                    mobile: '',
                    code: '',
                },
                changeMobile: false,
                sec: 60,
                check: false,
                beGet: false,
                btnloading: false,
                user: _baseUrl + '/statics/img/admin/user.png',
                mobile: _baseUrl + '/statics/img/admin/mobile.png',
                number: _baseUrl + '/statics/img/admin/number.png',
                end_time: _baseUrl + '/statics/img/admin/end_time.png',
                userinfo: {},
                rules: {
                    mobile: [
                        {required: true, validator: checkPhone, trigger: 'blur'},
                    ],
                    code: [
                        {required: true, message: "验证码不能为空", trigger: 'blur'},
                    ]
                },
            };
        },
        created() {
            let self = this;
            self.btnLoading = true;
            request({
                params: {
                    r: 'admin/user/user'
                },
                method: 'get',
                data: {}
            }).then(e => {
                self.btnLoading = false;
                self.userinfo = e.data.data.user;
                if (e.data.data.admin_info.expired_at === '0000-00-00 00:00:00') {
                    self.userinfo.expired_at = "永久";
                } else {
                    self.userinfo.expired_at = e.data.data.admin_info.expired_at.slice(0,10) + "到期"
                }
            }).catch(e => {
                console.log(e);
            });
        },
        methods: {
            getCode() {
                let self = this;
                if (!self.newList.mobile) {
                    self.$message.error('请先填写手机号');
                    return
                }
                // if(this.check) {
                //     mobile = this.newMobile;
                // }else {
                //     mobile = this.userinfo.mobile;
                // }
                self.$request({
                    params: {
                        r: 'admin/passport/sms-captcha'
                    },
                    method: 'post',
                    data: {
                        mobile: self.newList.mobile
                    }
                }).then(e => {
                    if (e.data.code === 0) {
                        self.$message.success(e.data.msg);
                        self.beGet = true;
                        const timer = setInterval(() => {
                            if (self.sec <= 0) {
                                self.beGet = false;
                                clearInterval(timer);
                                return;
                            }
                            self.sec = this.sec - 1;
                        }, 1000);
                    } else {
                        self.$message.error(e.data.msg);
                    }
                }).catch(e => {
                    console.log(e);
                });
            },
            change(formName) {
                let self = this;
                self.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.btnloading = true;
                        request({
                            params: {
                                r: 'admin/user/update-mobile',
                            },
                            data: {
                                mobile: this.newList.mobile,
                                captcha: this.newList.code
                            },
                            method: 'post',
                        }).then(e => {
                            self.btnloading = false;
                            if(e.data.code == 0) {
                                self.$message.success(e.data.msg);
                                self.changeMobile = false;
                                setTimeout(function(){
                                    location.reload();
                                },300)
                            }else {
                                self.$message.error(e.data.msg);
                            }
                        }).catch(e => {
                            self.btnloading = false;
                        });
                    }
                })
            }
            // checkCode() {
            //     this.check = true;
            //     this.code = '';
            //     this.beGet = false;
            //     this.sec = 60;
            // }
        },
    });
</script>