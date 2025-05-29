<?php
/**
 * Created by PhpStorm
 * User: chenzs
 * Date: 2024/9/29
 * Time: 4:08 下午
 * @copyright: ©2024 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */
?>
<template id="contents">
    <el-form @submit.native.prevent :model="form" :rules="rules" label-width="120px" :ref="formName">
        <el-form-item label="语音技术默认词" prop="voice_text">
            <div>中文：</div>
            <el-input v-model="form.voice_text" type="textarea" rows="4" class="currency-width"></el-input>
            <div>英文：</div>
            <el-input v-model="form.voice_text_en" type="textarea" rows="4" class="currency-width"></el-input>
        </el-form-item>
    </el-form>
</template>
<script>
    Vue.component('contents', {
        template: '#contents',
        props: {
            formName: String,
        },
        data() {
            return {
                form: {},
                rules: {},
            };
        },
        created() {},
        methods: {},
    });
</script>