<?php
/**
 * Created by PhpStorm
 * User: chenzs
 * Date: 2024/9/29
 * Time: 4:08 下午
 * @copyright: ©2024 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */
$http = Yii::$app->request->isSecureConnection ? 'https://' : 'http://';
?>
<template id="basic">
    <el-form @submit.native.prevent :model="form" :rules="rules" label-width="120px" :ref="formName">
        <el-form-item label="系统名称" prop="name">
            <el-input v-model="form.name" maxlength="15" class="currency-width" placeholder="最多输入15个字符"></el-input>
        </el-form-item>

        <el-form-item label="系统logo" prop="mall_logo_pic">
            <app-attachment style="margin-bottom:10px" :multiple="false" :max="1"
                            v-model="form.mall_logo_pic">
                <el-tooltip effect="dark"
                            content="建议尺寸:40 * 40"
                            placement="top">
                    <el-button size="mini">选择图标</el-button>
                </el-tooltip>
            </app-attachment>
            <div style="margin-right: 20px;display:inline-block;position: relative;cursor: pointer;">
                <app-attachment :multiple="false" :max="1" v-model="form.mall_logo_pic">
                    <app-image mode="aspectFill"
                               width="45px"
                               height='45px'
                               :src="form.mall_logo_pic">
                    </app-image>
                </app-attachment>
            </div>
            <el-button size="mini" @click="resetImg('mall_logo_pic')" class="reset" type="primary">恢复默认</el-button>
        </el-form-item>

        <el-form-item label="用户端域名设置">
            <el-input v-model="form.user_domain" class="currency-width" :disabled="isDisabled">
                <template slot="prepend">
                    <?=$http?>
                </template>
            </el-input>
            <div style="color: #BBBBBB">
                <span>此域名设置后不可更改，保存前请确认，默认域名为:<?=Yii::$app->request->hostInfo . dirname(Yii::$app->request->baseUrl)?></span>
            </div>
        </el-form-item>
    </el-form>
</template>
<script>
    Vue.component('basic', {
        template: '#basic',
        props: {
            formName: String,
        },
        data() {
            return {
                form: {},
                default: {},
                rules: {
                    name: [
                        {required: true, message: "请填写名称"},
                    ],
                },
            };
        },
        computed: {
            isDisabled() {
                return !!this.form.is_user_domain;
            },
        },
        created() {},
        methods: {
            resetImg(type) {
                this.form[type] = this.default[type] || '';
            },
        },
    });
</script>