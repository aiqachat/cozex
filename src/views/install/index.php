<?php

/**
 * Created by IntelliJ IDEA.
 * author: chenzs
 * Date: 2019/4/15
 * Time: 15:54
 */

/* @var $this \yii\web\View */
?>
<style>
    #app {
        padding: 40px 0 0;
    }

    .container {
        border: 1px solid #e2e2e2;
        max-width: 600px;
        margin: 0 auto 40px;
        color: #333;
    }

    .container .container-title {
        padding: 18px 20px;
        background: #F3F5F6;
    }

    .container .container-body {
        padding: 18px 20px;
    }

    .el-message-box {
        width: 500px;
    }

    .form-item-tap {
        font-size: 12px;
        color: #909399;
        line-height: normal;
    }
</style>
<div id="app">
    <el-form :model="form" :rules="rules" ref="form" label-width="120px">
        <div class="container">
            <div class="container-body" style="text-align: center;font-size: 16px;">
                <b>cozex安装</b>
            </div>
            <div class="container-title">MySQL数据库配置</div>
            <div class="container-body">
                <el-form-item label="数据库服务器" prop="db_host">
                    <el-input v-model="form.db_host"></el-input>
                    <div class="form-item-tap">请填写数据库服务器的IP或域名</div>
                </el-form-item>
                <el-form-item label="数据库端口" prop="db_port">
                    <el-input v-model.number="form.db_port"></el-input>
                    <div class="form-item-tap">MySQL的默认端口为3306，如果没改过就使用这里默认的配置</div>
                </el-form-item>
                <el-form-item label="数据库用户" prop="db_username">
                    <el-input v-model="form.db_username"></el-input>
                </el-form-item>
                <el-form-item label="数据库密码" prop="db_password">
                    <el-input v-model="form.db_password"></el-input>
                </el-form-item>
                <el-form-item label="数据库名称" prop="db_name">
                    <el-input v-model="form.db_name">
                        <template slot="append">
                            <i class="el-icon-check" style="padding-right: 10px;" v-if="is_check"></i>
                            <el-button size="mini" @click="check" type="primary" :loading="loading">检查</el-button>
                        </template>
                    </el-input>
                </el-form-item>
            </div>
            <div class="container-title">Redis配置</div>
            <div class="container-body">
                <el-form-item>
                    <div class="form-item-tap" style="font-size: 14px">需在PHP安装扩展里面开启redis服务</div>
                </el-form-item>
                <el-form-item label="Redis服务器" prop="redis_host">
                    <el-input v-model="form.redis_host"></el-input>
                    <div class="form-item-tap">请填写Redis服务器的IP或域名</div>
                </el-form-item>
                <el-form-item label="Redis端口" prop="redis_port">
                    <el-input v-model="form.redis_port"></el-input>
                    <div class="form-item-tap">Redis的默认端口为6379，如果没改过就使用这里默认的配置</div>
                </el-form-item>
                <el-form-item label="Redis密码" prop="redis_password">
                    <el-input v-model="form.redis_password"></el-input>
                    <div class="form-item-tap">Redis默认没有密码，如果您没配置过Redis密码则密码不需要填写</div>
                </el-form-item>
            </div>
            <div class="container-title">超级管理员</div>
            <div class="container-body">
                <el-form-item label="管理员账号" prop="admin_username">
                    <el-input v-model="form.admin_username"></el-input>
                </el-form-item>
                <el-form-item label="管理员密码" prop="admin_password">
                    <el-input v-model="form.admin_password"></el-input>
                </el-form-item>
            </div>
            <div style="border-top: 1px solid #e2e2e2;text-align: center;padding: 20px;">
                <el-button @click="submit('form')" type="primary" :loading="loading">立即安装</el-button>
            </div>
        </div>
    </el-form>
</div>
<script>
new Vue({
    el: '#app',
    data() {
        const validatePort = (rule, value, callback) => {
            if (!value) {
                return callback();
            }
            if (!Number.isInteger(value)) {
                return callback(new Error('端口号必须是数字'));
            }
            if (value > 65535 || value < 0) {
                return callback(new Error('端口号范围是0~65535'));
            }
            return callback();
        };
        return {
            loading: false,
            form: {
                db_host: '127.0.0.1',
                db_port: 3306,
                db_username: '',
                db_password: '',
                db_name: '',
                redis_host: '127.0.0.1',
                redis_port: 6379,
                redis_password: '',
                admin_username: '',
                admin_password: '',
            },
            rules: {
                db_host: [
                    {required: true, message: '不能为空'},
                ],
                db_port: [
                    {required: true, message: '不能为空'},
                    {validator: validatePort},
                ],
                db_username: [
                    {required: true, message: '不能为空'},
                ],
                db_password: [
                    {required: true, message: '不能为空'},
                ],
                db_name: [
                    {required: true, message: '不能为空'},
                ],
                redis_host: [
                    {required: true, message: '不能为空'},
                ],
                redis_port: [
                    {required: true, message: '不能为空'},
                ],
                admin_username: [
                    {required: true, message: '不能为空'},
                ],
                admin_password: [
                    {required: true, message: '不能为空'},
                ],
            },
            is_check: false
        };
    },
    created() {
    },
    methods: {
        check() {
            this.is_check = false;
            let is_check = true;
            this.$refs.form.validateField('db_name', (errorMessage) => {
                if (errorMessage) {
                    is_check = false;
                }
            });
            this.$refs.form.validateField('db_username', (errorMessage) => {
                if (errorMessage) {
                    is_check = false;
                }
            });
            this.$refs.form.validateField('db_password', (errorMessage) => {
                if (errorMessage) {
                    is_check = false;
                }
            });
            if(!is_check){
                return;
            }
            this.loading = true;
            this.$request({
                params: {
                    r: 'install/check',
                },
                method: 'post',
                data: {
                    db_host: this.form.db_host,
                    db_port: this.form.db_port,
                    db_username: this.form.db_username,
                    db_password: this.form.db_password,
                    db_name: this.form.db_name,
                },
            }).then(response => {
                this.loading = false;
                if(response.data.code === 0){
                    this.is_check = true;
                }else {
                    this.$alert(response.data.msg, '提示').then(() => {});
                }
            }).catch(e => {
                this.loading = false;
            });
        },
        submit(formName, is_clear = 0) {
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    this.loading = true;
                    this.$request({
                        params: {
                            r: 'install/index',
                        },
                        method: 'post',
                        data: Object.assign(this.form, {is_clear: is_clear}),
                    }).then(response => {
                        this.loading = false;
                        if (response.data.code === 0) {
                            this.$alert(response.data.msg, '提示').then(() => {
                                this.$navigate({});
                            });
                        } else {
                            if(response.data.data && response.data.data.code === 220099321){
                                const h = this.$createElement;
                                this.$msgbox({
                                    title: '提示',
                                    message: h('p', null, [
                                        h('span', null, response.data.msg),
                                        h('div', { style: 'color: teal' }, '方法一：环境面板配置如BT在PHP管理《禁用函数》里面删除禁用。'),
                                        h('div', { style: 'color: teal' }, '方法二：php.ini配置文件就行了搜索：disable_functions把后面proc_open删了。')
                                    ])
                                });
                            }else if(response.data.data && response.data.data.code === 220099322){
                                const h = this.$createElement;
                                this.$msgbox({
                                    title: '提示',
                                    message: h('p', null, [
                                        h('span', null, response.data.msg),
                                        h('div', { style: 'color: teal' }, '方法一：环境面板配置如BT在PHP管理《安装扩展》里面安装fileinfo。'),
                                    ])
                                });
                            }else if(response.data.data && response.data.data.code === 2){
                                const h = this.$createElement;
                                this.$msgbox({
                                    title: '提示',
                                    showCancelButton: true,
                                    confirmButtonText: '确定清空并立即安装',
                                    message: h('p', null, [
                                        h('span', null, response.data.msg),
                                        h('div', { style: 'color: red;font-size: 20px;' }, '是否清空数据库'),
                                    ]),
                                    beforeClose: (action, instance, done) => {
                                        if (action === 'confirm') {
                                            this.submit(formName, 1);
                                        }
                                        done();
                                    }
                                });
                            }else {
                                this.$alert(response.data.msg, '提示').then(() => {});
                            }
                        }
                    }).catch(e => {
                        this.loading = false;
                    });
                }
            });
        },
    },
});
</script>
