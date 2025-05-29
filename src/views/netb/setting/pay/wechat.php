<template id="wechat">
    <el-form :model="form" :rules="formRules" label-width="150px" position-label="right" :ref="formName">
        <el-form-item prop="is_wechat_pay" label="开启服务">
            <el-switch :active-value="1" :inactive-value="0" v-model="form.is_wechat_pay"></el-switch>
        </el-form-item>
        <template v-if="form.is_wechat_pay">
            <el-form-item prop="is_service" label="支付类型选择">
                <el-radio-group v-model="form.is_service" @change="changeType">
                    <el-radio :label="0">普通商户</el-radio>
                    <el-radio :label="1">服务商</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="微信APPID" prop="appid">
                <el-input class="out-max" size="small" v-model.trim="form.appid"></el-input>
            </el-form-item>
            <template v-if="form.is_service == 0">
                <el-form-item label="微信支付商户号" prop="mch_id">
                    <el-input class="out-max" size="small" v-model.trim="form.mch_id"></el-input>
                </el-form-item>
                <el-form-item label="微信支付Api密钥" prop="key">
                    <el-input @focus="hidden.key = false"
                              class="out-max" size="small"
                              v-if="hidden.key"
                              readonly
                              placeholder="已隐藏内容">
                    </el-input>
                    <el-input v-else class="out-max" size="small" v-model.trim="form.key"></el-input>
                </el-form-item>
                <el-form-item label="微信支付apiclient_cert.pem" prop="cert_pem">
                    <el-input @focus="hidden.cert_pem = false"
                              class="out-max" size="small"
                              v-if="hidden.cert_pem"
                              readonly
                              type="textarea"
                              :rows="5"
                              placeholder="已隐藏内容">
                    </el-input>
                    <el-input v-else class="out-max" size="small" type="textarea" :rows="5"
                              v-model="form.cert_pem"></el-input>
                    <div style="color: #CCCCCC;">注：退款请求需要证书，请先设置证书。</div>
                </el-form-item>
                <el-form-item label="微信支付apiclient_key.pem" prop="key_pem">
                    <el-input @focus="hidden.key_pem = false"
                              class="out-max" size="small"
                              v-if="hidden.key_pem"
                              readonly
                              type="textarea"
                              :rows="5"
                              placeholder="已隐藏内容">
                    </el-input>
                    <el-input v-else class="out-max" size="small" type="textarea" :rows="5"
                              v-model="form.key_pem"></el-input>
                    <div style="color: #CCCCCC;">注：退款请求需要证书，请先设置证书。</div>
                </el-form-item>
            </template>
            <template v-if="form.is_service == 1">
                <el-form-item label="服务商AppId" prop="service_appid">
                    <el-input class="out-max" size="small" v-model.trim="form.service_appid"></el-input>
                </el-form-item>
                <el-form-item label="服务商商户号" prop="service_mch_id">
                    <el-input class="out-max" size="small" v-model.trim="form.service_mch_id"></el-input>
                </el-form-item>
                <el-form-item label="服务商Api密钥" prop="service_key">
                    <el-input @focus="hidden.service_key = false"
                              class="out-max" size="small"
                              v-if="hidden.service_key"
                              readonly
                              placeholder="已隐藏内容">
                    </el-input>
                    <el-input v-else class="out-max" size="small" v-model.trim="form.service_key"></el-input>
                </el-form-item>
                <el-form-item label="特约商户商户号" prop="mch_id">
                    <el-input class="out-max" size="small" v-model.trim="form.mch_id"></el-input>
                </el-form-item>
                <el-form-item label="服务商apiclient_cert.pem" prop="cert_pem">
                    <el-input @focus="hidden.service_cert_pem = false"
                              class="out-max" size="small"
                              v-if="hidden.service_cert_pem"
                              readonly
                              type="textarea"
                              :rows="5"
                              placeholder="已隐藏内容">
                    </el-input>
                    <el-input v-else class="out-max" size="small" type="textarea" :rows="5"
                              v-model="form.service_cert_pem"></el-input>
                </el-form-item>
                <el-form-item label="服务商apiclient_key.pem" prop="key_pem">
                    <el-input @focus="hidden.service_key_pem = false"
                              class="out-max" size="small"
                              v-if="hidden.service_key_pem"
                              readonly
                              type="textarea"
                              :rows="5"
                              placeholder="已隐藏内容">
                    </el-input>
                    <el-input v-else class="out-max" size="small" type="textarea" :rows="5"
                              v-model="form.service_key_pem"></el-input>
                </el-form-item>
            </template>
            <el-form-item prop="is_v3" label="微信提现v3版本">
                <el-radio-group v-model="form.is_v3">
                    <el-radio :label="1">否</el-radio>
                    <el-radio :label="2">是</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="APIv3密钥" v-if="form.is_v3 == 2">
                <span>{{form.v3key}}</span>
                <el-button @click="copy" size="mini">复制密钥</el-button>
                <div style="color: #CCCCCC;">注：请复制后前往商户平台上设置APIv3密钥【账户中心—>API安全—>设置APIv3密钥】，否则无法处理提现回调数据</div>
            </el-form-item>
            <el-form-item label="公钥ID" v-if="form.is_v3 == 2">
                <el-input class="out-max" size="small" v-model="form.pub_key_id"></el-input>
                <div style="color: #CCCCCC;">注：有展示【账户中心—>API安全—>微信支付公钥】，则直接复制，没有留空</div>
            </el-form-item>
            <el-form-item label="公钥pem" v-if="form.is_v3 == 2">
                <el-input @focus="hidden.pub_key = false"
                          class="out-max" size="small"
                          v-if="hidden.pub_key"
                          readonly
                          type="textarea"
                          :rows="5"
                          placeholder="已隐藏内容">
                </el-input>
                <el-input v-else class="out-max" size="small" type="textarea" :rows="5" v-model.trim="form.pub_key"></el-input>
                <div style="color: #CCCCCC;">注：有展示【账户中心—>API安全—>微信支付公钥】，则直接复制，没有留空</div>
            </el-form-item>
        </template>
    </el-form>
</template>
<script>
    Vue.component('wechat', {
        template: '#wechat',
        props: {
            formName: String,
        },
        data() {
            return {
                hidden: {
                    service_key_pem: true,
                    service_cert_pem: true,
                    service_key: true,
                    key: true,
                    cert_pem: true,
                    key_pem: true,
                    pub_key: true,
                },
                form: {
                    is_service: 0, //支付类型选择
                    is_v3: 1, //v3提现
                },
                formRules: {
                    key: [
                        {required: true, message: '微信支付Api密钥不能为空', trigger: 'change'},
                    ],
                    appid: [
                        {required: true, message: '微信APPID不能为空', trigger: 'change'},
                    ],
                    mch_id: [
                        {required: true, message: '商户号不能为空', trigger: 'change'},
                    ],
                    service_appid: [
                        {required: true, message: '服务商AppId不能为空', trigger: 'change'},
                    ],
                    service_mch_id: [
                        {required: true, message: '服务商商户号不能为空', trigger: 'change'},
                    ],
                },
            }
        },
        methods: {
            changeType() {
                this.$refs[this.formName].clearValidate();
            },
            copy() {
                navigator.clipboard.writeText(this.form.v3key)
                    .then(() => this.$message.success('复制成功'))
                    .catch(() => this.$message.error('复制失败'))
            },
        },
    });
</script>
