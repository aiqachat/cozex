<style>
    .el-input__inner {
        min-width: 200px;
        max-width: 500px;
    }
</style>
<div id="app" v-cloak>
    <el-card shadow="never" style="border:0" body-style="padding: 10px 0 0;" v-loading="cardLoading">
        <div slot="header">
            <span>短信设置</span>
        </div>
        <div style="padding: 10px 20px">
            <el-form @submit.native.prevent :model="form" :rules="rules" label-width="150px" ref="form">
                <el-form-item label="AppID">
                    <el-input v-model="form.app_id"
                              placeholder="请填写 AppID"></el-input>
                </el-form-item>
                <el-form-item label="access_key_id">
                    <el-input v-model="form.access_key_id"
                              placeholder="请填写 access_key_id"></el-input>
                </el-form-item>
                <el-form-item label="access_key_secret">
                    <el-input v-model="form.access_key_secret"
                              placeholder="请填写 access_key_secret"></el-input>
                </el-form-item>
                <el-form-item label="模板签名">
                    <el-input v-model="form.template_name" placeholder="模板签名"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-alert style="width: 40%" title="" type="info" :closable=false
                            :description="template.code_template_id">
                    </el-alert>
                </el-form-item>
                <el-form-item label="验证码通知">
                    <el-input v-model="form.code_template_id"
                              placeholder="请输入模板ID"></el-input>
                </el-form-item>
                <el-form-item label="">
                    <el-button class='button-item' :loading="btnLoading" type="primary" @click="store('form')" size="small">保存</el-button>
                </el-form-item>
            </el-form>
        </div>
    </el-card>
</div>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                btnLoading: false,
                cardLoading: false,
                form: {},
                rules: {
                    key: [
                        {required: true, message: '输入支付密钥', trigger: 'change'},
                    ],
                },
                tab: 'sms',
                template: {
                    code_template_id: '例如：您的验证码为{code}。',
                }
            }
        },
        created() {
            this.getDetail();
        },
        methods: {
            store(formName) {
                let self = this;
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        self.btnLoading = true;
                        request({
                            params: {
                                r: 'netb/index/sms'
                            },
                            method: 'post',
                            data: {
                                formData: self.form,
                                tab: self.tab,
                            }
                        }).then(e => {
                            self.btnLoading = false;
                            if (e.data.code == 0) {
                                self.$message.success(e.data.msg);
                            } else {
                                self.$message.error(e.data.msg);
                            }
                        }).catch(e => {
                            self.$message.error(e.data.msg);
                            self.btnLoading = false;
                        });
                    }else{
                        self.$message.error('请完善必填项');
                    }
                });
            },
            getDetail() {
                let self = this;
                self.cardLoading = true;
                request({
                    params: {
                        r: 'netb/index/sms',
                        tab: self.tab
                    },
                    method: 'get',
                }).then(e => {
                    self.cardLoading = false;
                    if (e.data.code == 0) {
                        self.form = e.data.data.data
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
