<?php defined('YII_ENV') or exit('Access Denied'); ?>
<style>
    .form-body {
        background-color: #fff;
        padding: 20px 50% 20px 0;
    }

    .button-item {
        margin: 12px 0;
        padding: 9px 25px;
    }

    .tip {
        margin-left: 10px;
        display: inline-block;
        height: 30px;
        line-height: 30px;
        color: #ff4544;
        background-color: #FEF0F0;
        padding: 0 20px;
        border-radius: 5px;
    }
</style>
<section id="app" v-cloak>
    <el-card class="box-card" style="border:0" shadow="never" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header" class="clearfix">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><span style="color: #409EFF;cursor: pointer" @click="$navigate({r:'mall/user/index'})">用户管理</span></el-breadcrumb-item>
                <el-breadcrumb-item>用户编辑</el-breadcrumb-item>
            </el-breadcrumb>
            <span></span>
        </div>
        <div class="form-body">
            <el-form :model="form" label-width="100px" :rules="FormRules" ref="form" v-loading="listLoading">
                <el-form-item label="用户">
                    <app-image width="80px" height="80px" mode="aspectFill" :src="form.avatar"></app-image>
                </el-form-item>
                <!-- --- -->
                <el-form-item label="加入黑名单" prop="is_blacklist">
                    <el-switch v-model="form.is_blacklist" :active-value="1" :inactive-value="0"></el-switch>
                    <span class="tip">加入黑名单后，用户将无法下单</span>
                </el-form-item>
                <el-form-item label="联系方式" prop="contact_way">
                    <el-input size="small" v-model="form.contact_way" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item label="手机号" prop="mobile">
                    <el-input disabled size="small" v-model="form.mobile" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item label="备注" prop="remark">
                    <el-input size="small" v-model="form.remark" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item label="修改备注名" prop="remark_name">
                    <el-input size="small" v-model="form.remark_name" maxlength="8" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item label="注册时间">
                    <div>{{form.created_at}}</div>
                </el-form-item>
            </el-form>
        </div>
        <el-button class="button-item" :loading="btnLoading" type="primary" @click="onSubmit">提交</el-button>
    </el-card>
</section>
<script>
const app = new Vue({
    el: '#app',
    data() {
        return {
            form: {},
            keyword: '',
            listLoading: false,
            btnLoading: false,
            FormRules: {
                remark_name: [
                    { max: 8, message: '备注名最多输入8个汉字', trigger: 'blur' }
                ],
            },
        };
    },
    methods: {
        onSubmit() {
            this.$refs.form.validate((valid) => {
                if (valid) {
                    this.btnLoading = true;
                    let para = Object.assign({}, this.form);
                    if (this.form.contact_way) {
                        if (!(/^1[23456789]\d{9}$/.test(this.form.contact_way) || /^\d{3,4}-\d{7,8}(-\d{3,4})?$/.test(this.form.contact_way))) {
                            this.$message.warning('联系方式格式错误');
                            this.btnLoading = false;
                            return;
                        }
                    }
                    request({
                        params: {
                            r: 'mall/user/edit',
                        },
                        data: para,
                        method: 'post'
                    }).then(e => {
                        this.btnLoading = false;
                        if (e.data.code === 0) {
                            navigateTo({ r: 'mall/user/index', page: getQuery('page') });
                        } else {
                            this.$message.error(e.data.msg);
                        }
                    });
                }
            });
        },

        getList() {
            this.listLoading = true;
            request({
                params: {
                    r: 'mall/user/edit',
                    id: getQuery('id'),
                },
            }).then(e => {
                if (e.data.code === 0) {
                    this.form = e.data.data.list;
                } else {
                    this.$message.error(e.data.msg);
                }
                this.listLoading = false;
            }).catch(e => {
                this.listLoading = false;
            });
        },
    },
    mounted() {
        this.getList();
    },
})
</script>
