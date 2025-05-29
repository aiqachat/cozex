<?php
$url = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . "/notify/google.php";
?>
<template id="google">
    <el-form @submit.native.prevent :model="form" :rules="rules" label-width="200px" :ref="formName">
        <el-form-item label="">
            <div style="color: #BBBBBB">
                谷歌授权请查看：<a href="https://developers.google.com/identity/protocols/oauth2?hl=zh-cn" target="_blank">文档地址</a>
            </div>
        </el-form-item>
        <el-form-item label="客户端ID（Client ID）" prop="client_id">
            <el-input v-model="form.client_id" class="currency-width"></el-input>
        </el-form-item>
        <el-form-item label="客户端密钥（Client secret）" prop="client_secret">
            <el-input v-model="form.client_secret" class="currency-width"></el-input>
        </el-form-item>
        <el-form-item label="回调地址url">
            <span style="margin-right: 10px;"><?=$url ?></span>
            <el-button size="mini" @click="copy" type="success" plain>复制链接</el-button>
        </el-form-item>
    </el-form>
</template>
<script>
    Vue.component('google', {
        template: '#google',
        props: {
            formName: String,
        },
        data() {
            return {
                form: {},
                rules: {
                    client_id: [
                        {required: true, message: "请填写客户端ID"},
                    ],
                    client_secret: [
                        {required: true, message: "请填写客户端密钥"},
                    ],
                },
            };
        },
        created() {},
        methods: {
            copy() {
                navigator.clipboard.writeText('<?=$url ?>')
                    .then(() => this.$message.success('复制成功'))
                    .catch(() => this.$message.error('复制失败'))
            },
        },
    });
</script>