<?php
defined('YII_ENV') or exit('Access Denied');
Yii::$app->loadViewComponent('app-rich-text');
?>
<style>
    .button-item {
        padding: 9px 25px;
    }

    .out-max {
        width: 50vh;
    }
</style>
<div id="app" v-cloak>
    <el-card shadow="never">
        <el-tabs v-model="activeName">
            <el-tab-pane label="中文版" name="basic"></el-tab-pane>
            <el-tab-pane label="英文版" name="customize"></el-tab-pane>
        </el-tabs>
        <el-form :model="editForm" :rules="rules" ref="form" size="small" label-width="150px" v-loading="listLoading">
            <template v-if="activeName === 'basic'">
                <el-form-item label="充值文字" prop="title">
                    <el-input type="input" size="small" v-model="editForm.title" class="out-max"></el-input>
                </el-form-item>
                <el-form-item label="充值说明" prop="agreement">
                    <div style="width: 458px; min-height: 458px;">
                        <app-rich-text v-model="editForm.agreement"></app-rich-text>
                    </div>
                </el-form-item>
            </template>
            <template v-if="activeName === 'customize'">
                <el-form-item label="充值文字" prop="title_en">
                    <el-input type="input" size="small" v-model="editForm.title_en"></el-input>
                </el-form-item>
                <el-form-item label="充值说明" prop="agreement_en">
                    <div style="width: 458px; min-height: 458px;">
                        <app-rich-text v-model="editForm.agreement_en"></app-rich-text>
                    </div>
                </el-form-item>
            </template>
            <el-button :loading="btnLoading" size="small" type="primary" class="button-item" @click="onSubmit">
                保存
            </el-button>
        </el-form>
    </el-card>
</div>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                rules: {},
                editForm: {},
                listLoading: false,
                btnLoading: false,
                activeName: 'basic',
            };
        },
        methods: {
            onSubmit() {
                this.$refs.form.validate((valid) => {
                    if (valid) {
                        this.btnLoading = true;
                        request({
                            params: {
                                r: 'netb/recharge/config'
                            },
                            data: this.editForm,
                            method: 'post',
                        }).then(e => {
                            if (e.data.code === 0) {
                                this.$message.success(e.data.msg);
                                setTimeout(function(){
                                    navigateTo({ r: 'netb/recharge/config' });
                                },300);
                            } else {
                                this.$message.error(e.data.msg);
                            }
                            this.btnLoading = false;
                        }).catch(e => {
                            this.btnLoading = false;
                        });
                    }
                });
            },
            getList() {
                this.listLoading = true;
                request({
                    params: {
                        r: 'netb/recharge/config'
                    },
                }).then(e => {
                    if (e.data.code === 0) {
                        this.editForm = e.data.data.data;
                    }
                    this.listLoading = false;
                }).catch(e => {
                    this.listLoading = false;
                });
            },
        },

        mounted() {
            this.getList();
        }
    })
</script>