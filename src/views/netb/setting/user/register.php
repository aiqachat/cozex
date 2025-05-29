<?php
Yii::$app->loadViewComponent('app-rich-text')
    ?>
<template id="register">
    <el-form @submit.native.prevent :model="form" :rules="rules" label-width="130px" :ref="formName">
        <el-form-item label="手机号登录注册">
            <el-radio-group v-model="form.mobile_register_login">
                <el-radio :label="1">开启</el-radio>
                <el-radio :label="0">关闭</el-radio>
            </el-radio-group>
            <div class="remark">
                开启后需要设置短信发送，点击前往 <a href="#" @click="$navigate({r:'netb/index/sms'}, true)">设置</a>
            </div>
        </el-form-item>
        <el-form-item label="邮箱登录注册">
            <el-radio-group v-model="form.email_register_login">
                <el-radio :label="1">开启</el-radio>
                <el-radio :label="0">关闭</el-radio>
            </el-radio-group>
            <div class="remark">
                开启后需要设置邮箱发送，点击前往 <a href="#" @click="$navigate({r:'netb/index/mail'}, true)">设置</a>
            </div>
        </el-form-item>
        <el-form-item label="谷歌免登录">
            <el-radio-group v-model="form.google_login">
                <el-radio :label="1">开启</el-radio>
                <el-radio :label="0">关闭</el-radio>
            </el-radio-group>
            <div class="remark">
                开启后需要设置谷歌配置，点击前往 <a href="#" @click="$navigate({r:'netb/setting/oauth'}, true)">设置</a>
            </div>
        </el-form-item>
        <el-form-item label="隐私协议默认勾选">
            <el-radio-group v-model="form.is_check">
                <el-radio :label="1">开启</el-radio>
                <el-radio :label="0">关闭</el-radio>
            </el-radio-group>
        </el-form-item>
        <el-form-item label="用户协议标题">
            <el-input v-model.trim="form.agreement_title" class="currency-width">
                <template slot="prepend">中文</template>
            </el-input>
            <el-input v-model.trim="form.agreement_title" class="currency-width">
                <template slot="prepend">英文</template>
            </el-input>
        </el-form-item>
        <el-form-item label="用户协议">
            <div>中文用户协议：</div>
            <app-rich-text v-model.trim="form.agreement" class="currency-width"></app-rich-text>
            <div style="margin-top: 10px;">英文用户协议：</div>
            <app-rich-text v-model.trim="form.agreement_en" class="currency-width"></app-rich-text>
        </el-form-item>
        <el-form-item label="隐私政策标题">
            <el-input v-model.trim="form.privacy_policy_title" class="currency-width">
                <template slot="prepend">中文</template>
            </el-input>
            <el-input v-model.trim="form.privacy_policy_title" class="currency-width">
                <template slot="prepend">英文</template>
            </el-input>
        </el-form-item>
        <el-form-item label="隐私政策">
            <div>中文隐私政策：</div>
            <app-rich-text v-model.trim="form.privacy_policy" class="currency-width"></app-rich-text>
            <div style="margin-top: 10px;">英文隐私政策：</div>
            <app-rich-text v-model.trim="form.privacy_policy_en" class="currency-width"></app-rich-text>
        </el-form-item>
    </el-form>
</template>
<script>
    Vue.component('register', {
        template: '#register',
        props: {
            formName: String,
        },
        data() {
            return {
                form: {},
                rules: {},
            };
        },
        created() { },
    });
</script>