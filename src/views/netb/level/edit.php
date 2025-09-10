<?php
Yii::$app->loadViewComponent('app-rich-text');
?>
<style>
    .form-body {
        background-color: #fff;
        margin-bottom: 20px;
        padding: 20px 20% 20px 20px;
        min-width: 900px;
    }

    .form-body .el-form-item {
        padding-right: 50%;
        min-width: 850px;
    }

    .button-item {
        padding: 9px 25px;
    }
</style>
<div id="app" v-cloak>
    <el-card class="box-card" v-loading="cardLoading" shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>
                    <span style="color: #409EFF;cursor: pointer" @click="$navigate({r:'netb/level/index'})">用户等级</span>
                </el-breadcrumb-item>
                <el-breadcrumb-item>等级设置</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="form-body">
            <el-form :model="ruleForm" :rules="rules" size="small" ref="ruleForm" label-width="150px">
                <el-tabs v-model="activeName">
                    <el-tab-pane label="基础信息" name="basic">
                        <el-form-item label="等级名称" prop="name">
                            <el-input v-model="ruleForm.name" placeholder="请输入等级名称"></el-input>
                        </el-form-item>
                        <el-form-item label="启用状态" prop="status">
                            <el-switch
                                    v-model="ruleForm.status"
                                    :active-value="1"
                                    :inactive-value="0">
                            </el-switch>
                        </el-form-item>
                    </el-tab-pane>
                    <el-tab-pane label="推广配置" name="comm">
                        <el-form-item label="启用状态" prop="promotion_status">
                            <el-switch
                                    v-model="ruleForm.promotion_status"
                                    :active-value="1"
                                    :inactive-value="0">
                            </el-switch>
                        </el-form-item>
                        <el-form-item prop="promotion_commission_ratio">
                            <template slot='label'>
                                <span>推广佣金比例</span>
                                <el-tooltip effect="dark" content="请输入0.1~100之间的数字" placement="top">
                                    <i class="el-icon-info"></i>
                                </el-tooltip>
                            </template>
                            <el-input placeholder="请输入推广比例" min="0.1" max="100" type="number" v-model="ruleForm.promotion_commission_ratio">
                                <template slot="append">%</template>
                            </el-input>
                        </el-form-item>
                        <el-form-item label="推广说明" prop="promotion_desc">
                            中文
                            <app-rich-text v-model="ruleForm.promotion_desc"></app-rich-text>
                        </el-form-item>
                        <el-form-item label="推广说明" prop="promotion_desc">
                            英文
                            <app-rich-text v-model="ruleForm.language_data.en.promotion_desc"></app-rich-text>
                        </el-form-item>
                    </el-tab-pane>
                </el-tabs>
                <el-form-item label="">
                    <el-button class="button-item" :loading="btnLoading" type="primary" @click="store('ruleForm')" size="small">保存</el-button>
                </el-form-item>
            </el-form>
        </div>
    </el-card>
</div>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                activeName: 'basic',
                ruleForm: {
                    name: '',
                    promotion_commission_ratio: '0',
                    status: '1',
                    promotion_status: '1',
                    language_data: {
                        en: {}
                    }
                },
                rules: {
                    name: [
                        {required: true, message: '请输入会员名称', trigger: 'change'},
                    ],
                    promotion_commission_ratio: [
                        {required: true, message: '请输入推广佣金比例', trigger: 'change'},
                    ],
                    status: [
                        {required: true, message: '请选择会员状态', trigger: 'change'},
                    ],
                },
                btnLoading: false,
                cardLoading: false,
            };
        },
        methods: {
            store(formName) {
                this.$refs[formName].validate((valid) => {
                    let self = this;
                    if (valid) {
                        self.btnLoading = true;
                        request({
                            params: {
                                r: 'netb/level/edit'
                            },
                            method: 'post',
                            data: self.ruleForm
                        }).then(e => {
                            self.btnLoading = false;
                            if (e.data.code == 0) {
                                navigateTo({
                                    r: 'netb/level/index'
                                })
                            } else {
                                self.$message.error(e.data.msg);
                            }
                        }).catch(e => {
                            self.$message.error(e.data.msg);
                            self.btnLoading = false;
                        });
                    } else {
                        console.log('error submit!!');
                        return false;
                    }
                });
            },
            getDetail() {
                let self = this;
                self.cardLoading = true;
                request({
                    params: {
                        r: 'netb/level/edit',
                        id: getQuery('id')
                    },
                    method: 'get',
                }).then(e => {
                    self.cardLoading = false;
                    if (e.data.code == 0) {
                        self.ruleForm = e.data.data.detail;
                        if (!self.ruleForm.language_data || !self.ruleForm.language_data.en) {
                            self.ruleForm.language_data = {en : {}};
                        }
                    } else {
                        self.$message.error(e.data.msg);
                    }
                }).catch(e => {
                    console.log(e);
                });
            },
        },
        mounted: function () {
            if (getQuery('id')) {
                this.getDetail();
            }
        }
    });
</script>