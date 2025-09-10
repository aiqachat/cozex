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
<template id="square">
    <el-form @submit.native.prevent :model="form" :rules="rules" label-width="140px" :ref="formName">
        <el-form-item label="广场标题">
            <el-input v-model="form.square_title" class="currency-width" type="text">
                <template slot="prepend">
                    中文
                </template>
            </el-input>
        </el-form-item>
        <el-form-item label="广场标题">
            <el-input v-model="form.square_title_en" class="currency-width" type="text">
                <template slot="prepend">
                    英文
                </template>
            </el-input>
        </el-form-item>
        <el-form-item label="广场副标题">
            <el-input v-model="form.square_subtitle" class="currency-width" type="text">
                <template slot="prepend">
                    中文
                </template>
            </el-input>
        </el-form-item>
        <el-form-item label="广场副标题">
            <el-input v-model="form.square_subtitle_en" class="currency-width" type="text">
                <template slot="prepend">
                    英文
                </template>
            </el-input>
        </el-form-item>
        <el-form-item label="按钮文字">
            <el-input v-model="form.square_button_text" class="currency-width" type="text">
                <template slot="prepend">
                    中文
                </template>
            </el-input>
        </el-form-item>
        <el-form-item label="按钮文字">
            <el-input v-model="form.square_button_text_en" class="currency-width" type="text">
                <template slot="prepend">
                    英文
                </template>
            </el-input>
        </el-form-item>
        <el-form-item label="背景图片">
            <app-attachment @selected="picUrl" :multiple="true">
                <el-button size="mini">选择图片</el-button>
            </app-attachment>
            <div v-if="form.square_bg_list && form.square_bg_list.length > 0" style="margin-top: 10px;">
                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                    <div v-for="(item, index) in form.square_bg_list" style="width: 150px; height: 150px; position: relative;">
                        <el-image
                                style="width: 100%; height: 100%;"
                                :src="item"
                                fit="cover"
                                :preview-src-list="form.square_bg_list"
                                :initial-index="index">
                        </el-image>
                        <el-button 
                            type="danger" 
                            icon="el-icon-delete" 
                            circle 
                            size="mini" 
                            @click="deleteImage(index)"
                            style="position: absolute; top: 5px; right: 5px; opacity: 0.8;">
                        </el-button>
                    </div>
                </div>
            </div>
        </el-form-item>
    </el-form>
</template>
<script>
    Vue.component('square', {
        template: '#square',
        props: {
            formName: String,
        },
        data() {
            return {
                form: {},
                default: {},
                rules: {},
            };
        },
        created() {},
        methods: {
            picUrl(e) {
                if (e.length) {
                    let self = this;
                    self.form.square_bg_list = [];
                    e.forEach(function(item, index) {
                        self.form.square_bg_list.push(item.url);
                    });
                }
            },
            deleteImage(index) {
                this.form.square_bg_list.splice(index, 1);
            }
        },
    });
</script>
