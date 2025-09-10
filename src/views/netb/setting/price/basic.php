<?php
$form = new \app\forms\common\volcengine\data\BaseForm();
?>
<template id="basic">
    <el-form @submit.native.prevent :model="form" :rules="rules" label-width="120px" :ref="formName">
        <el-alert
            type="info"
            v-if="form.integral"
            :description="'1<?=$data['currency_name'];?> = ' + form.integral.integral_rate + '积分，积分可用于兑换各项语音合成服务'"
            show-icon
            :closable="false">
            <div slot="title" style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                <span>积分换算说明</span>
                <el-button type="primary" size="mini" @click="$navigate({r:'netb/integral/setting'}, true)">
                    <i class="el-icon-setting"></i> 前往积分设置
                </el-button>
            </div>
        </el-alert>
        
        <el-divider content-position="left"><?=$form->textName ($form->ttsMega) ?></el-divider>
        <el-form-item label="音色单价" prop="unit_price">
            <el-input v-model="form.unit_price" class="currency-width" type="number">
                <template slot="prepend">第一次收费</template>
                <template slot="append">元</template>
            </el-input>
        </el-form-item>
        <el-form-item label="音色单价" prop="renewal_unit_price">
            <el-input v-model="form.renewal_unit_price" class="currency-width" type="number">
                <template slot="prepend">续费收费</template>
                <template slot="append">元</template>
            </el-input>
        </el-form-item>
        <el-form-item label="计费" prop="tts_mega_exchange">
            <el-input v-model="form.tts_mega_exchange" class="currency-width" type="number" min="0" step="0.5">
                <template slot="prepend">1积分可以转换</template>
                <template slot="append">个字</template>
            </el-input>
        </el-form-item>
        <el-divider content-position="left"><?=$form->textName ($form->ttsBig) ?></el-divider>
        <el-form-item label="计费" prop="tts_big_exchange">
            <el-input v-model="form.tts_big_exchange" class="currency-width" type="number" min="0" step="0.5">
                <template slot="prepend">1积分可以转换</template>
                <template slot="append">个字</template>
            </el-input>
        </el-form-item>
        <el-divider content-position="left"><?=$form->textName ($form->ttsLong) ?></el-divider>
        <el-form-item label="计费" prop="tts_long_exchange">
            <el-input v-model="form.tts_long_exchange" class="currency-width" type="number" min="0" step="0.5">
                <template slot="prepend">1积分可以转换</template>
                <template slot="append">个字</template>
            </el-input>
        </el-form-item>
        <el-divider content-position="left"><?=$form->textName ($form->tts) ?></el-divider>
        <el-form-item label="计费" prop="tts_exchange">
            <el-input v-model="form.tts_exchange" class="currency-width" type="number" min="0" step="0.5">
                <template slot="prepend">1积分可以转换</template>
                <template slot="append">个字</template>
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
                form: {},
                default: {},
                rules: {
                    unit_price: [
                        {required: true, message: "请填写"},
                    ],
                    renewal_unit_price: [
                        {required: true, message: "请填写"},
                    ],
                    tts_mega_bill: [
                        {required: true, message: "请填写"},
                    ],
                    tts_big_bill: [
                        {required: true, message: "请填写"},
                    ],
                    tts_long_bill: [
                        {required: true, message: "请填写"},
                    ],
                    tts_bill: [
                        {required: true, message: "请填写"},
                    ],
                },
            };
        },
        created() {},
        methods: {},
    });
</script>