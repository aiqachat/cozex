<?php

/**
 * Created by IntelliJ IDEA.
 * author: wstianxia
 * Date: 2019/3/1
 * Time: 17:05
 */

/* @var $this \yii\web\View */
?>
<style>
    #app .el-checkbox {
        margin-bottom: 0;
    }

    .button-item {
        margin-top: 20px;
        padding: 9px 25px;
    }
</style>
<div id="app" v-cloak>
    <el-card shadow="never" v-loading="cardLoading">
        <el-row>
            <el-alert :closable="false" type="error">注：为了服务器性能和用户体验考虑做了如下限制，请谨慎考虑！！！</el-alert>
            <el-col :span="12">
                <el-form @submit.native.prevent :model="ruleForm" :rules="rules" ref="ruleForm">
                    <el-form-item label="上传图片限制" prop="img_overrun">
                        <el-input :disabled="ruleForm.is_img_overrun"
                                  size="small"
                                  type="number"
                                  placeholder="图片大小限制"
                                  v-model="ruleForm.img_overrun">
                            <template slot="append">
                                <div>MB</div>
                            </template>
                        </el-input>
                        <el-checkbox v-model="ruleForm.is_img_overrun">无限制</el-checkbox>
                    </el-form-item>
                    <el-form-item label="上传文件限制" prop="video_overrun">
                        <el-input :disabled="ruleForm.is_video_overrun"
                                  size="small"
                                  type="number"
                                  placeholder="文件大小限制"
                                  v-model="ruleForm.video_overrun">
                            <template slot="append">
                                <div>MB</div>
                            </template>
                        </el-input>
                        <el-checkbox v-model="ruleForm.is_video_overrun">无限制</el-checkbox>
                    </el-form-item>
                </el-form>
            </el-col>
        </el-row>
    </el-card>
    <el-button class="button-item" type="primary" :loading="loading" @click="submit">保存</el-button>
</div>
<script>
    new Vue({
        el: '#app',
        data() {
            return {
                loading: false,
                ruleForm: {},
                rules: {
                    img_overrun: [
                        {required: true, message: "请输入图片大小限制",},
                    ],
                    video_overrun: [
                        {required: true, message: "请输入视频大小限制",},
                    ],
                }
            };
        },
        created() {
            this.getSetting();
        },
        methods: {
            submit() {
                this.loading = true;
                this.$request({
                    params: {
                        r: 'admin/setting/overrun',
                    },
                    method: 'post',
                    data: {
                        form: this.ruleForm
                    },
                }).then(e => {
                    this.loading = false;
                    if (e.data.code === 0) {
                        this.$message.success(e.data.msg);
                    } else {
                        this.$message.error(e.data.msg);
                    }
                }).catch(e => {
                });
            },
            getSetting() {
                this.cardLoading = true;
                this.$request({
                    params: {
                        r: 'admin/setting/overrun',
                    },
                    method: 'get',
                }).then(e => {
                    this.cardLoading = false;
                    this.ruleForm = e.data.data.setting;
                }).catch(e => {
                });
            }
        },
    });
</script>
