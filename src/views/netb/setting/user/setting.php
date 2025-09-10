<?php
?>
<template id="setting">
    <el-form @submit.native.prevent :model="form" :rules="rules" label-width="130px" :ref="formName">
        <el-form-item label="站点标题">
            <el-input v-model.trim="form.title" class="currency-width">
                <template slot="prepend">
                    中文
                </template>
            </el-input>
        </el-form-item>
        <el-form-item>
            <el-input v-model.trim="form.title_en" class="currency-width">
                <template slot="prepend">
                    英文
                </template>
            </el-input>
        </el-form-item>
        <el-form-item label="站点关键字">
            <el-input v-model.trim="form.keywords" class="currency-width">
                <template slot="prepend">
                    中文
                </template>
            </el-input>
        </el-form-item>
        <el-form-item>
            <el-input v-model.trim="form.keywords_en" class="currency-width">
                <template slot="prepend">
                    英文
                </template>
            </el-input>
        </el-form-item>
        <el-form-item label="站点描述（中文）">
            <el-input type="textarea" rows="5" v-model.trim="form.description" class="currency-width"></el-input>
        </el-form-item>
        <el-form-item label="站点描述（英文）">
            <el-input type="textarea" rows="5" v-model.trim="form.description_en" class="currency-width"></el-input>
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