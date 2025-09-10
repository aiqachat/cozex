<?php

use app\forms\mall\setting\ConfigForm;

$data = (new ConfigForm())->config();
?>
<style>
    .form-body {
        background-color: #fff;
        padding: 20px 50% 20px 0;
    }

    .button-item {
        margin-top: 20px;
        padding: 12px 30px;
        font-size: 14px;
    }
    
    .form-section {
        margin-bottom: 25px;
        margin-left: 20px;
    }
    
    .form-section-title {
        font-size: 16px;
        font-weight: 600;
        color: #303133;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid #409EFF;
    }
    
    .field-group {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        border-left: 4px solid #409EFF;
    }
    
    .el-form-item {
        margin-bottom: 20px;
    }
    
    .el-form-item__label {
        font-weight: 500;
        color: #606266;
    }
    
    .el-input-group__prepend,
    .el-input-group__append {
        background-color: #f5f7fa;
        color: #606266;
        border-color: #dcdfe6;
    }
</style>
<div id="app" v-cloak>
    <el-card style="border:0" shadow="never" body-style="background-color: #f3f3f3;padding: 10px 0;">
        <div slot="header">
            <span>用户积分设置</span>
        </div>
        <el-row>
            <el-col :span="24">
                <el-card v-loading="loading" style="border:0" shadow="never" body-style="background-color: #f3f3f3;padding: 0 0;">
                    <el-form :model="ruleForm"
                             :rules="rules"
                             ref="ruleForm"
                             label-width="170px"
                             size="small">
                        <div class="form-body">
                            <div class="form-section">
                                <div class="form-section-title">基础积分设置</div>
                                <div class="field-group">
                                    <el-form-item label="用户积分" prop="integral_rate" label-width="120px">
                                        <el-input v-model="ruleForm.integral_rate"
                                                  oninput="this.value = this.value.match(/\d*/)"
                                                  type="number">
                                            <template slot="append">积分抵扣1<?=$data['currency_name'];?></template>
                                        </el-input>
                                    </el-form-item>
                                </div>
                            </div>
                            
                            <div class="form-section">
                                <div class="form-section-title">新用户设置</div>
                                <div class="field-group">
                                    <el-form-item label="新用户注册" prop="give_integral" label-width="120px">
                                        <el-input v-model="ruleForm.give_integral"
                                                  oninput="this.value = this.value.match(/\d*/)"
                                                  type="number">
                                            <template slot="prepend">赠送</template>
                                            <template slot="append">积分</template>
                                        </el-input>
                                    </el-form-item>
                                    <el-form-item label="积分有效期" prop="integral_validity" label-width="120px">
                                        <el-input v-model="ruleForm.integral_validity"
                                                  oninput="this.value = this.value.match(/\d*/)"
                                                  type="number">
                                            <template slot="prepend">有效期</template>
                                            <template slot="append">天</template>
                                        </el-input>
                                    </el-form-item>
                                </div>
                            </div>
                            
                            <div class="form-section">
                                <div class="form-section-title">每日积分设置</div>
                                <div class="field-group">
                                    <el-form-item label="每天赠送" prop="daily_gift" label-width="120px">
                                        <el-input v-model="ruleForm.daily_gift"
                                                  oninput="this.value = this.value.match(/\d*/)"
                                                  type="number">
                                            <template slot="append">积分</template>
                                        </el-input>
                                    </el-form-item>
                                    <el-form-item label="刷新间隔" prop="refresh_hours" label-width="120px">
                                        <el-input v-model="ruleForm.refresh_hours"
                                                  oninput="this.value = this.value.match(/\d*/)"
                                                  type="number">
                                            <template slot="prepend">每</template>
                                            <template slot="append">小时刷新</template>
                                        </el-input>
                                    </el-form-item>
                                </div>
                            </div>
                        </div>
                        <el-button :loading="submitLoading" class="button-item" size="small" type="primary"
                                   @click="submit('ruleForm')">保存
                        </el-button>
                    </el-form>
                </el-card>
            </el-col>
        </el-row>
    </el-card>
</div>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                loading: false,
                submitLoading: false,
                mall: null,
                ruleForm: {
                    integral_rate: 0,
                    give_integral: 0,
                    integral_validity: 0,
                    daily_gift: 0,
                    refresh_hours: 0
                },
                rules: {},
            };
        },
        created() {
            this.loadData();
        },
        methods: {
            loadData() {
                this.loading = true;
                request({
                    params: {
                        r: 'netb/setting/user',
                        tab: 'integral'
                    },
                }).then(e => {
                    this.loading = false;
                    if (e.data.code === 0) {
                        const serverData = e.data.data.data || {};
                        // 确保所有字段都有值，服务器没有的字段使用默认值
                        this.ruleForm = {
                            integral_rate: serverData.integral_rate || 0,
                            give_integral: serverData.give_integral || 0,
                            integral_validity: serverData.integral_validity || 365,
                            daily_gift: serverData.daily_gift || 10,
                            refresh_hours: serverData.refresh_hours || 24
                        };
                    } else {
                        this.$message.error(e.data.msg);
                    }
                }).catch(e => {
                });
            },
            submit(formName) {
                this.$refs[formName].validate(valid => {
                    if (valid) {
                        this.submitLoading = true;
                        request({
                            params: {
                                r: 'netb/setting/user',
                            },
                            method: 'post',
                            data: {
                                formData: this.ruleForm,
                                tab: 'integral',
                            },
                        }).then(e => {
                            this.submitLoading = false;
                            if (e.data.code === 0) {
                                this.$message.success(e.data.msg);
                            } else {
                                this.$message.error(e.data.msg);
                            }
                        }).catch(e => {
                        });
                    } else {
                        this.$message.error('部分参数验证不通过');
                    }
                });
            },
        },
    });
</script>

