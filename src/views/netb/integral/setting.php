<style>
    .form-body {
        background-color: #fff;
        padding: 20px 50% 20px 0;
    }

    .button-item {
        margin-top: 12px;
        padding: 9px 25px;
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
                            <el-form-item label="用户积分" prop="integral_rate">
                                <el-input v-model="ruleForm.integral_rate"
                                          oninput="this.value = this.value.match(/\d*/)"
                                          type="number">
                                    <template slot="append">积分抵扣1元</template>
                                </el-input>
                            </el-form-item>
                            <el-form-item label="新用户注册" prop="give_integral">
                                <el-input v-model="ruleForm.give_integral"
                                          oninput="this.value = this.value.match(/\d*/)"
                                          type="number">
                                    <template slot="prepend">赠送</template>
                                    <template slot="append">积分</template>
                                </el-input>
                            </el-form-item>
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
                ruleForm: {},
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
                        this.ruleForm = e.data.data.data;
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

