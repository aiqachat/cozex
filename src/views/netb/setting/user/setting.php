<?php
?>
<template id="setting">
    <el-form @submit.native.prevent :model="form" :rules="rules" label-width="130px" :ref="formName">
        <el-form-item label="网站标题">
            <el-input v-model.trim="form.title" class="currency-width"></el-input>
        </el-form-item>
        <el-form-item label="网站关键词">
            <el-input v-model.trim="form.keywords" class="currency-width"></el-input>
        </el-form-item>
        <el-form-item label="网站描述">
            <el-input type="textarea" rows="5" v-model.trim="form.description" class="currency-width"></el-input>
        </el-form-item>
    </el-form>
</template>
<script>
    Vue.component('setting', {
        template: '#setting',
        props: {
            formName: String,
        },
        data() {
            return {
                form: {},
                rules: {},
            };
        },
        created(){},
    });
</script>