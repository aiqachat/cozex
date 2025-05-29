<template id="basic">
    <el-form :model="form" :rules="formRules" label-width="150px" position-label="right" :ref="formName">
        <el-form-item label="平台锚定货币" prop="currency">
            <el-select size="small" v-model="form.currency" filterable class="out-max" @change='change'>
                <el-option v-for="item in form.currency_list" :key="item.id" :label="item.name" :value="item.id"></el-option>
            </el-select>
        </el-form-item>
        <el-form-item label="货币单位符号" prop="currency_symbol">
            <el-input class="out-max" size="small" v-model.trim="form.currency_symbol"></el-input>
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
                formRules: {},
            }
        },
        methods: {
            change(){
                // 从form.currency_list匹配currency_symbol的值
                this.form.currency_symbol = this.form.currency_list.find(item => item.id === this.form.currency).symbol;
            },
        },
    });
</script>
