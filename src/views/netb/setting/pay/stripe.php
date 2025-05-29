<template id="stripe">
    <el-form :model="form" :rules="formRules" label-width="150px" position-label="right" :ref="formName">
        <el-form-item prop="is_stripe_pay" label="开启服务">
            <el-switch :active-value="1" :inactive-value="0" v-model="form.is_stripe_pay"></el-switch>
        </el-form-item>
        <template v-if="form.is_stripe_pay">
            <el-form-item label="API私钥" prop="api_key">
                <el-input class="out-max" size="small" v-model.trim="form.api_key"></el-input>
            </el-form-item>
            <el-form-item label="API公钥" prop="api_public_key">
                <el-input class="out-max" size="small" v-model.trim="form.api_public_key"></el-input>
            </el-form-item>
            <el-form-item label="Webhook接受端地址" prop="api_public_key">
                <span>{{ form.webhook_url }}</span>
                <el-button @click="copy" size="mini">复制密钥</el-button>
            </el-form-item>
        </template>
    </el-form>
</template>
<script>
    Vue.component('stripe', {
        template: '#stripe',
        props: {
            formName: String,
        },
        data() {
            return {
                form: {},
                formRules: {
                    api_key: [
                        {required: true, message: 'api私钥不能为空', trigger: 'change'},
                    ],
                },
            }
        },
        methods: {
            copy() {
                navigator.clipboard.writeText(this.form.webhook_url)
                    .then(() => this.$message.success('复制成功'))
                    .catch(() => this.$message.error('复制失败'))
            },
        },
    });
</script>
