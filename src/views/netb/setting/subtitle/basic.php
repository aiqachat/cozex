<?php
$form = new \app\forms\common\volcengine\data\BaseForm();
?>
<template id="basic">
    <el-form @submit.native.prevent :model="form" :rules="rules" label-width="120px" :ref="formName">
        <el-alert type="info" v-if="form.integral"
            :description="'1<?= $data['currency_name']; ?> = ' + form.integral.integral_rate + '积分，积分可用于兑换各项语音合成服务'"
            show-icon :closable="false">
            <div slot="title" style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                <span>积分换算说明</span>
                <el-button type="primary" size="mini" @click="$navigate({r:'netb/integral/setting'}, true)">
                    <i class="el-icon-setting"></i> 前往积分设置
                </el-button>
            </div>
        </el-alert>

        <el-divider content-position="left">所属账号</el-divider>
        <el-form-item label="选择账号" prop="account_id">
            <el-select v-model="form.account_id" placeholder="请选择账号">
                <el-option v-for="account in accounts" :key="account.id" :label="account.name" :value="account.id">
                </el-option>
            </el-select>
            <el-button type="primary" @click="$navigate({r:'netb/setting/volcengine'}, true)">添加应用</el-button>
            <el-button type="default" @click="refreshAccounts">刷新账号数据</el-button>
        </el-form-item>

        <el-divider content-position="left"><?=$form->textName ($form->vc) ?></el-divider>
        <el-form-item label="计费" prop="vc_price">
            <el-input v-model="form.vc_price" class="currency-width" type="number" min="0">
                <template slot="prepend">1秒收费</template>
                <template slot="append">积分</template>
            </el-input>
        </el-form-item>

        <el-divider content-position="left"><?=$form->textName ($form->ata) ?></el-divider>
        <el-form-item label="计费" prop="ata_price">
            <el-input v-model="form.ata_price" class="currency-width" type="number" min="0">
                <template slot="prepend">1秒收费</template>
                <template slot="append">积分</template>
            </el-input>
        </el-form-item>

        <el-divider content-position="left"><?=$form->textName ($form->auc) ?></el-divider>
        <el-form-item label="计费" prop="auc_price">
            <el-input v-model="form.auc_price" class="currency-width" type="number" min="0">
                <template slot="prepend">1秒收费</template>
                <template slot="append">积分</template>
            </el-input>
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
                form: {
                    selectedAccount: null, // To hold the selected account
                },
                accounts: [], // To hold the list of accounts
                default: {},
                rules: {
                    unit_price: [
                        { required: true, message: "请填写" },
                    ],
                    renewal_unit_price: [
                        { required: true, message: "请填写" },
                    ],
                    tts_mega_bill: [
                        { required: true, message: "请填写" },
                    ],
                    tts_big_bill: [
                        { required: true, message: "请填写" },
                    ],
                    tts_long_bill: [
                        { required: true, message: "请填写" },
                    ],
                    tts_bill: [
                        { required: true, message: "请填写" },
                    ],
                },
            };
        },
        created() {
            this.fetchAccounts(); // Fetch accounts when the component is created
        },
        methods: {
            fetchAccounts(){
                request({
                    params: {
                        r: 'netb/index/volcengine-account',
                    },
                    method: 'get',
                }).then(e => {
                    this.accounts = e.data.data.account;
                })
            },
            refreshAccounts() {
                this.fetchAccounts(); // Refresh the account list
            },
        },
    });
</script>