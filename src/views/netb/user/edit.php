<?php defined('YII_ENV') or exit('Access Denied'); ?>
<style>
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
                <el-breadcrumb-item><span style="color: #409EFF;cursor: pointer" @click="$navigate({r:'netb/user/index'})">用户管理</span></el-breadcrumb-item>
                <el-breadcrumb-item v-if="id">用户编辑</el-breadcrumb-item>
                <el-breadcrumb-item v-else>用户添加</el-breadcrumb-item>
            </el-breadcrumb>
            <span></span>
        </div>
        <el-form :model="form" label-width="150px" :rules="FormRules" ref="form" v-loading="listLoading">
            <el-tabs type="border-card" v-model="activeName">
                <el-tab-pane label="基础信息" name="one">
                    <el-form-item label="用户头像">
                        <app-image width="80px" height="80px" mode="aspectFill" :src="form.avatar" v-if="id"></app-image>
                        <div style="display:inline-block;position: relative;cursor: pointer;" v-else>
                            <app-attachment :multiple="false" :max="1" v-model="form.avatar">
                                <app-image mode="aspectFill"
                                           width="80px"
                                           height='80px'
                                           :src="form.avatar">
                                </app-image>
                            </app-attachment>
                        </div>
                    </el-form-item>
                    <el-form-item label="昵称" prop="nickname">
                        <el-input size="small" v-model="form.nickname" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="用户等级" prop="user_level">
                        <el-select size="small" v-model="form.user_level" placeholder="请选择等级">
                            <el-option
                                    v-for="item in levels"
                                    :key="item.name"
                                    :label="item.name"
                                    :value="item.id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="会员等级" prop="vip_level">
                        <el-select size="small" v-model="form.member_level" placeholder="请选择会员等级">
                            <el-option
                                    v-for="item in member_levels"
                                    :key="item.name"
                                    :label="item.name"
                                    :value="item.id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="备注" prop="remark">
                        <el-input size="small" v-model="form.remark" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="加入黑名单" prop="is_blacklist">
                        <el-switch v-model="form.is_blacklist" :active-value="1" :inactive-value="0"></el-switch>
                        <span class="tip">加入黑名单后，用户无法使用功能</span>
                    </el-form-item>
                    <el-form-item label="推荐人" prop="parent_id">
                        <el-select size="small" v-model="form.parent_id" placeholder="请选择推荐人">
                            <el-option
                                    v-for="item in recommend_list"
                                    :key="item.nickname"
                                    :label="item.nickname"
                                    :value="item.id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <template v-if="id">
                        <el-form-item label="手机号">
                            {{ form.mobile }}
                        </el-form-item>
                        <el-form-item label="邮箱">
                            <el-input size="small" v-model="form.email" autocomplete="off"></el-input>
                        </el-form-item>
                    </template>
                    <template v-else>
                        <el-form-item label="登录账号">
                            <el-radio-group v-model="form.account" size="small">
                                <el-radio label="mobile" border>手机号</el-radio>
                                <el-radio label="email" border>邮箱</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item label="手机号" :prop="form.account == 'mobile' ? 'mobile' : ''">
                            <el-input size="small" v-model="form.mobile" type="number" autocomplete="off"></el-input>
                            <div style="color: #92959B" v-if="form.account == 'mobile'">
                                此项为登录账号，请填写
                            </div>
                        </el-form-item>
                        <el-form-item label="邮箱" :prop="form.account == 'email' ? 'email' : ''">
                            <el-input size="small" v-model="form.email" autocomplete="off"></el-input>
                            <div style="color: #92959B" v-if="form.account == 'email'">
                                此项为登录账号，请填写
                            </div>
                        </el-form-item>
                        <el-form-item label="密码" prop="password">
                            <el-input size="small" v-model="form.password" autocomplete="off"></el-input>
                        </el-form-item>
                    </template>
                    <el-form-item label="注册时间" v-if="id">
                        <div>{{form.created_at}}</div>
                    </el-form-item>
                </el-tab-pane>
<!--                <el-tab-pane label="功能设置" name="two">-->
<!--                    <el-form-item label="资源管理器空间大小">-->
<!--                        <el-input v-model="form.set_data.attachment_size" type="number">-->
<!--                            <template slot="append">MB</template>-->
<!--                        </el-input>-->
<!--                    </el-form-item>-->
<!--                    <el-form-item label="不自动删除图片权限">-->
<!--                        <el-switch-->
<!--                                v-model="form.set_data.del_img_power"-->
<!--                                active-value="1"-->
<!--                                inactive-value="0"-->
<!--                                active-text="开启"-->
<!--                                inactive-text="关闭">-->
<!--                        </el-switch>-->
<!--                    </el-form-item>-->
<!--                    <el-form-item label="不自动删除视频权限">-->
<!--                        <el-switch-->
<!--                                v-model="form.set_data.del_video_power"-->
<!--                                active-value="1"-->
<!--                                inactive-value="0"-->
<!--                                active-text="开启"-->
<!--                                inactive-text="关闭">-->
<!--                        </el-switch>-->
<!--                    </el-form-item>-->
<!--                </el-tab-pane>-->
            </el-tabs>
        </el-form>
        <el-button class="button-item" :loading="btnLoading" type="primary" @click="onSubmit">提交</el-button>
    </el-card>
</section>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                activeName: 'one',
                form: {
                    account: 'email',
                },
                keyword: '',
                listLoading: false,
                btnLoading: false,
                FormRules: {
                    email: [
                        {required: true, message: '邮箱不能为空', trigger: 'blur'},
                    ],
                    mobile: [
                        {required: true, message: '手机号不能为空', trigger: 'blur'},
                    ],
                    password: [
                        {required: true, message: '密码不能为空', trigger: 'blur'},
                    ],
                },
                money: 0,
                id: getQuery('id'),

                levels: [],

                //推荐人列表
                recommend_list: [],
            };
        },
        methods: {
            onSubmit() {
                this.$refs.form.validate((valid) => {
                    if (valid) {
                        this.btnLoading = true;
                        request({
                            params: {
                                r: 'netb/user/edit',
                            },
                            data: this.form,
                            method: 'post'
                        }).then(e => {
                            this.btnLoading = false;
                            if (e.data.code === 0) {
                                navigateTo({ r: 'netb/user/index', page: getQuery('page') });
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
                        r: 'netb/user/edit',
                        id: this.id,
                    },
                }).then(e => {
                    if (e.data.code === 0) {
                        this.form = Object.assign({}, this.form, e.data.data.list)
                        this.levels = e.data.data.levels;
                        this.member_levels = e.data.data.member_levels;
                        this.recommend_list = e.data.data.recommend_list;
                        if (!this.form.member_level || this.form.member_level === 0) {
                            this.form.member_level = '';
                        }
                        
                        // 如果当前选中的推荐人ID为0，则清空选择
                        if (this.form.parent_id == 0) {
                            this.form.parent_id = '';
                        }
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
