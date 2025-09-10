<?php
defined('YII_ENV') or exit('Access Denied');
?>
<style>
    .form-body {
        background-color: #fff;
        margin-bottom: 20px;
        padding: 20px 50% 20px 0;
    }

    .form-input {
        width: 50%;
    }

    .button-item {
        padding: 9px 25px;
    }
</style>
<section id="app" v-cloak>
    <el-card class="box-card" shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header" class="clearfix">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><span style="color: #409EFF;cursor: pointer"
                                          @click="$navigate({r:'netb/integral/exchange'})">兑换管理</span></el-breadcrumb-item>
                <el-breadcrumb-item>编辑</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="form-body">
            <el-form :model="form" v-loading="loading" label-width="10rem" :rules="FormRules" ref="form">
                <el-form-item prop="name">
                    <template slot='label'>
                        <span>名称</span>
                    </template>
                    <el-input size="small" v-model="form.name" autocomplete="off">
                        <template slot="prepend">中文</template>
                    </el-input>
                    <el-input size="small" v-model="form.language_data.en.name" autocomplete="off">
                        <template slot="prepend">英文</template>
                    </el-input>
                </el-form-item>
                <el-form-item prop="pay_price">
                    <template slot='label'>
                        <span>支付金额</span>
                    </template>
                    <el-input size="small" type="text"
                              @input="handlePriceInput"
                              v-model="form.pay_price" autocomplete="off" placeholder="请输入金额"></el-input>
                </el-form-item>
                <el-form-item prop="serial_num">
                    <template slot='label'>
                        <span>排序</span>
                    </template>
                    <el-input size="small" type="number" v-model="form.serial_num"></el-input>
                </el-form-item>
                <el-form-item prop="send_integral">
                    <template slot='label'>
                        <span>兑换积分数</span>
                    </template>
                    <el-input size="small" type="number" v-model="form.send_integral" placeholder="请输入兑换积分数"></el-input>
                </el-form-item>
                <el-form-item prop="give_integral">
                    <template slot='label'>
                        <span>赠送积分数</span>
                    </template>
                    <el-input size="small" type="number" v-model="form.give_integral" placeholder="请输入赠送积分数"></el-input>
                </el-form-item>
                <el-form-item prop="buy_num">
                    <template slot='label'>
                        <span>可购买次数</span>
                    </template>
                    <el-input size="small" type="number" v-model="form.buy_num" placeholder="请输入可购买次数"></el-input>
                </el-form-item>
                <el-form-item prop="period">
                    <template slot='label'>
                        <span>有效期</span>
                    </template>
                    <el-input size="small" type="number" v-model="form.period" placeholder="请输入有效期">
                        <template slot="append">天</template>
                    </el-input>
                </el-form-item>
            </el-form>
        </div>
        <el-button class="button-item" type="primary" size='mini' :loading=btnLoading @click="onSubmit">保存</el-button>
    </el-card>
</section>

<script>
    const app = new Vue({
        el: '#app',
        data() {
            let checkPrice = (rule, value, callback) => {
                if (!value) {
                    return callback(new Error('不能为空'));
                }
                if (value <= 0) {
                    callback(new Error('必须大于0'));
                } else {
                    callback();
                }
            };
            let checkAge = (rule, value, callback) => {
                if (!value) {
                    return callback(new Error('不能为空'));
                }
                if (value < 0) {
                    callback(new Error('不能小于0'));
                } else {
                    callback();
                }
            };
            return {
                form: {
                    name: '',
                    pay_price: '',
                    send_integral: '',
                    serial_num: 9999,
                    give_integral: '',
                    buy_num: '',
                    period: '',
                    language_data: {en : {}}
                },
                loading: false,
                btnLoading: false,
                FormRules: {
                    name: [
                        {required: true, message: '名称不能为空', trigger: 'blur'},
                    ],
                    pay_price: [
                        {required: true, message: '支付金额不能为空', trigger: 'change'},
                        { validator: checkPrice, trigger: 'change' }
                    ],
                    send_integral: [
                        {required: true, message: '兑换积分不能为空', trigger: 'change'},
                        { validator: checkAge, trigger: 'change' }
                    ],
                    give_integral: [
                        {required: true, message: '赠送积分不能为空', trigger: 'change'},
                        { validator: checkAge, trigger: 'change' }
                    ],
                    buy_num: [
                        {required: true, message: '可购买次数不能为空', trigger: 'change'},
                        { validator: checkAge, trigger: 'change' }
                    ],
                    period: [
                        {required: true, message: '有效期不能为空', trigger: 'change'},
                        { validator: checkAge, trigger: 'change' }
                    ],
                },
            };
        },
        methods: {
            // 处理金额输入
            handlePriceInput(value) {
                // 只允许输入数字和小数点
                let newValue = value.replace(/[^\d.]/g, '');
                // 确保只有一个小数点
                let parts = newValue.split('.');
                if (parts.length > 2) {
                    newValue = parts[0] + '.' + parts.slice(1).join('');
                }
                // 限制小数位数为2位
                if (parts.length === 2 && parts[1].length > 2) {
                    newValue = parts[0] + '.' + parts[1].substring(0, 2);
                }
                // 更新输入框的值
                this.form.pay_price = newValue;
            },
            
            // 提交数据
            onSubmit() {
                this.$refs.form.validate((valid) => {
                    if (valid) {
                        this.btnLoading = true;
                        let para = Object.assign(this.form);
                        request({
                            params: {
                                r: 'netb/integral/edit',
                            },
                            data: para,
                            method: 'post'
                        }).then(e => {
                            this.btnLoading = false;
                            if (e.data.code === 0) {
                                this.$message({
                                    message: e.data.msg,
                                    type: 'success'
                                });
                                setTimeout(function(){
                                    navigateTo({ r: 'netb/integral/exchange' });
                                },300);
                            } else {
                                this.$message.error(e.data.msg);
                            }
                        }).catch(e => {
                            this.btnLoading = false;
                        });
                    }
                });
            },

            //获取列表
            getList() {
                this.loading = true;
                request({
                    params: {
                        r: 'netb/integral/edit',
                        id: getQuery('id'),
                    },
                }).then(e => {
                    this.loading = false;
                    if (e.data.code === 0) {
                        this.form = Object.assign({}, this.form, e.data.data);
                        if (!this.form.language_data.en) {
                            this.form.language_data = {en : {}};
                        }
                    }
                }).catch(e => {

                });
            },
        },

        created() {
            if (getQuery('id')) {
                this.getList();
            }
        }
    })
</script>