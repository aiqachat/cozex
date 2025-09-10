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
    <el-form @submit.native.prevent :model="form" :rules="rules" label-width="140px" :ref="formName">
        <el-divider content-position="left">资源保存时间</el-divider>

        <el-form-item label="图片保存时间">
            <el-input v-model="form.image_storage_time" class="currency-width" type="number">
                <template slot="append">小时</template>
            </el-input>
        </el-form-item>
        <el-form-item label="视频保存时间">
            <el-input v-model="form.video_storage_time" class="currency-width" type="number">
                <template slot="append">小时</template>
            </el-input>
        </el-form-item>
        <el-form-item label="资源管理器保存时间">
            <el-input v-model="form.attachment_storage_time" class="currency-width" type="number">
                <template slot="append">小时</template>
            </el-input>
        </el-form-item>
        <el-form-item label="图片自动审核">
            <el-switch v-model="form.is_img_audit" :active-value="1" :inactive-value="0"></el-switch>
        </el-form-item>
        <template v-if="form.is_img_audit">
            <el-form-item label="审核方式">
                <el-radio-group v-model="form.img_audit_type">
                    <el-radio :label="1">全天</el-radio>
                    <el-radio :label="2">时间段</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="选择时间范围" v-if="form.img_audit_type === 2">
                <el-time-picker
                        is-range
                        v-model="form.img_audit_time"
                        range-separator="至"
                        format="HH:mm"
                        value-format="HH:mm"
                        start-placeholder="开始时间"
                        end-placeholder="结束时间"
                        placeholder="选择时间范围">
                </el-time-picker>
            </el-form-item>
        </template>

        <el-form-item label="视频自动审核">
            <el-switch v-model="form.is_video_audit" :active-value="1" :inactive-value="0"></el-switch>
        </el-form-item>
        <template v-if="form.is_video_audit">
            <el-form-item label="审核方式">
                <el-radio-group v-model="form.video_audit_type">
                    <el-radio :label="1">全天</el-radio>
                    <el-radio :label="2">时间段</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="选择时间范围" v-if="form.video_audit_type === 2">
                <el-time-picker
                        is-range
                        v-model="form.video_audit_time"
                        range-separator="至"
                        format="HH:mm"
                        value-format="HH:mm"
                        start-placeholder="开始时间"
                        end-placeholder="结束时间"
                        placeholder="选择时间范围">
                </el-time-picker>
            </el-form-item>
        </template>
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
                rules: {},
            };
        },
        created() {},
        methods: {},
    });
</script>