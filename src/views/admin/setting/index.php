<?php
/**
 * Created by IntelliJ IDEA.
 * author: wstianxia
 * Date: 2019/3/22
 * Time: 16:23
 */
Yii::$app->loadViewComponent('app-rich-text');
?>
<style>
    .my-img {
        height: 50px;
        border: 1px solid #d7dae2;
        border-radius: 2px;
        margin-top: 10px;
        background-color: #e2e2e2;
        overflow: hidden;
    }

    .form-body {
        display: flex;
        justify-content: center;
    }

    .form-body .el-form {
        width: 450px;
        margin-top: 10px;
    }

    .currency-width {
        width: 300px;
    }

    .currency-width .el-input__inner {
        height: 35px;
        line-height: 35px;
        border-radius: 8px;
    }

    .isAppend .el-input__inner {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .form-body .currency-width .el-input-group__append {
        width: 80px;
        background-color: #2E9FFF;
        color: #fff;
        padding: 0;
        line-height: 35px;
        height: 35px;
        text-align: center;
        border-radius: 0 8px 8px 0;
        border: 0;
    }

    .preview {
        height: 75px;
        line-height: 75px;
        text-align: center;
        width: 200px;
        background-color: #F7F7F7;
        color: #BBBBBB;
        margin-top: 10px;
        font-size: 12px;
    }

    .title {
        margin-bottom: 20px;
    }

    .submit-btn {
        height: 32px;
        width: 65px;
        line-height: 32px;
        text-align: center;
        border-radius: 16px;
        padding: 0;
    }

    .check-title {
        background-color: #F3F5F6;
        width: 100%;
        padding: 0 20px;
    }

    .check-list {
        display: flex;
        flex-wrap: wrap;
        padding: 0 20px;
    }

    .check-list .el-checkbox {
        width: 145px;
    }

    .el-checkbox {
        height: 50px;
        line-height: 50px;
    }

    .window {
        border: 1px solid #EBEEF5;
    }

    .check-title .el-checkbox__label {
        font-size: 16px;
    }
</style>
<div id="app" v-cloak>
    <el-card shadow="never" v-loading="loading">
        <el-tabs v-model="activeName" @tab-click="handleClick">
            <el-tab-pane label="基础配置" name="first"></el-tab-pane>
            <el-tab-pane label="登录配置" name="second"></el-tab-pane>
            <el-tab-pane label="注册配置" name="third"></el-tab-pane>
            <el-tab-pane label="短信配置（阿里云）" name="four"></el-tab-pane>
            <el-tab-pane label="AI客服配置" name="five"></el-tab-pane>
        </el-tabs>

        <div class='form-body' ref="body">
            <el-form @submit.native.prevent label-position="left" label-width="150px">
                <template v-if="activeName == 'first'">
                    <el-form-item label="站点logo">
                        <el-input disabled class="currency-width isAppend">
                            <template slot="append">
                                <app-upload @complete="updateSuccess" accept="image/vnd.microsoft.icon" :params="params" :simple="true">
                                    <el-button size="small">上传logo</el-button>
                                </app-upload>
                            </template>
                        </el-input>
                        <div style="height: 40px;line-height: 40px" class="preview">仅支持上传</div>
                    </el-form-item>
                    <el-form-item label="网站名称">
                        <el-input class="currency-width" v-model="form.name"></el-input>
                    </el-form-item>
                    <el-form-item label="网站简称">
                        <el-input class="currency-width" v-model="form.description"></el-input>
                    </el-form-item>
                    <el-form-item label="网站关键字">
                        <el-input type="textarea" class="currency-width" v-model="form.keywords"></el-input>
                        <div style="color: #909399;font-size: 12px;line-height: 1;margin-top: 10px">
                            多个关键字用英文
                        </div>
                    </el-form-item>
                    <el-form-item label="管理页背景图">
                        <el-input class="currency-width isAppend" v-model="form.manage_bg">
                            <template slot="append">
                                <app-attachment v-model="form.manage_bg">
                                    <el-button>上传图片</el-button>
                                </app-attachment>
                            </template>
                        </el-input>
                        <img class="my-img" style="background-color: #100a46;border-color: #100a46; height: 100px;" v-if="form.manage_bg" :src="form.manage_bg">
                        <div v-else style="height: 40px;line-height: 40px" class="preview">建议尺寸：1920*200</div>
                    </el-form-item>
                    <el-form-item label="底部版权信息">
                        <el-input class="currency-width" v-model="form.copyright"></el-input>
                    </el-form-item>
                    <el-form-item label="底部版权url">
                        <el-input class="currency-width" v-model="form.copyright_url"
                                  placeholder="例如:https://www.baidu.com">
                        </el-input>
                    </el-form-item>
                </template>
                <template v-if="activeName == 'second'">
                    <el-form-item label="LOGO图片URL">
                        <el-input class="currency-width isAppend" v-model="form.logo">
                            <template slot="append">
                                <app-attachment v-model="form.logo">
                                    <el-button>上传图片</el-button>
                                </app-attachment>
                            </template>
                        </el-input>
                        <img class="my-img" style="background-color: #100a46;border-color: #100a46; height: 36px;" v-if="form.logo" :src="form.logo">
                        <div v-else class="preview">建议尺寸：98*50</div>
                    </el-form-item>
                    <el-form-item label="登录页背景图">
                        <el-input class="currency-width isAppend" v-model="form.passport_bg">
                            <template slot="append">
                                <app-attachment v-model="form.passport_bg">
                                    <el-button>上传图片</el-button>
                                </app-attachment>
                            </template>
                        </el-input>
                        <img class="my-img" style="background-color: #100a46;border-color: #100a46; height: 108px;" v-if="form.passport_bg" :src="form.passport_bg">
                        <div v-else class="preview">建议尺寸：1920*1080</div>
                    </el-form-item>
                </template>
                <template v-if="activeName == 'third'">
                    <el-form-item label="开启注册功能" :style="{'margin-bottom': form.open_register == 0 ? '20px' : 0}">
                        <el-radio v-model="form.open_register" :label="1">是</el-radio>
                        <el-radio v-model="form.open_register" :label="0">否</el-radio>
                    </el-form-item>
                    <template v-if="form.open_register == 1">
                        <el-form-item label="是否需要审核" style="margin-bottom: 0">
                            <el-radio v-model="form.open_verify" :label="1">是</el-radio>
                            <el-radio v-model="form.open_verify" :label="0">否</el-radio>
                        </el-form-item>
                        <template v-if="form.open_verify == 0">
                            <el-form-item label="设置默认体验套餐">
                                <div flex="dir:left cross:center" style="margin-bottom: 10px;">
                                    <div style="width: 85px;color: #606266;line-height: 0">可体验商城</div>
                                    <div style="line-height: 0;">
                                        <el-input type="number" size="small" placeholder="请输入天数" v-model="form.use_days">
                                            <template slot="append">天</template>
                                        </el-input>
                                    </div>
                                </div>
                                <div flex="dir:left cross:center" style="margin-bottom: 10px;">
                                    <div style="width: 85px;color: #606266;line-height: 0">可创建商城</div>
                                    <div style="line-height: 0;">
                                        <el-input type="number" size="small" placeholder="请输入个数" v-model="form.create_num">
                                            <template slot="append">个</template>
                                        </el-input>
                                    </div>
                                </div>
                                <div flex="dir:left cross:center" style="margin-bottom: 10px;">
                                    <div style="width: 85px;color: #606266;line-height: 0">可拥有插件</div>
                                    <div style="line-height: 0;">
                                        <el-button @click="edit" size="small" type="primary">{{permissionText()}}</el-button>
                                    </div>
                                </div>
                            </el-form-item>
                        </template>
                        <el-form-item label="是否开启短信通知">
                            <el-radio v-model="form.open_sms" :label="1">是</el-radio>
                            <el-radio v-model="form.open_sms" :label="0">否</el-radio>
                            <div style="color: #BBBBBB;font-size: 12px;line-height: 20px;">请填写短信配置</div>
                        </el-form-item>
                    </template>
                    <el-form-item label="证件信息是否必填">
                        <el-radio v-model="form.is_required" :label="1">是</el-radio>
                        <el-radio v-model="form.is_required" :label="0">否</el-radio>
                    </el-form-item>
                    <el-form-item label="注册页背景图">
                        <el-input class="currency-width isAppend" v-model="form.registered_bg">
                            <template slot="append">
                                <app-attachment v-model="form.registered_bg">
                                    <el-button>上传图片</el-button>
                                </app-attachment>
                            </template>
                        </el-input>
                        <img class="my-img" style="background-color: #100a46;border-color: #100a46; height: 100px;" v-if="form.registered_bg" :src="form.registered_bg">
                        <div v-else style="height: 40px;line-height: 40px" class="preview">建议尺寸：1920*200</div>
                    </el-form-item>
                    <el-form-item label="注册协议" style="width: 600px;">
                        <app-rich-text v-model="form.register_protocol"></app-rich-text>
                    </el-form-item>
                </template>
                <template v-if="activeName == 'four'">
                    <!-- 短信设置 -->
                    <div :style="line" class="title">
                        <span style="color: #909399;font-size: 12px;">用于发送（注册、重置密码）短信验证码、注册结果短信通知。</span>
                    </div>
                    <el-form-item label="AccessKeyId">
                        <el-input class="currency-width" v-model="form.ind_sms.aliyun.access_key_id"></el-input>
                    </el-form-item>
                    <el-form-item label="AccessKeySecret">
                        <el-input class="currency-width" v-model="form.ind_sms.aliyun.access_key_secret"></el-input>
                    </el-form-item>
                    <el-form-item label="短信签名">
                        <el-input class="currency-width" v-model="form.ind_sms.aliyun.sign"></el-input>
                    </el-form-item>
                    <el-form-item label="验证码模板ID">
                        <el-input class="currency-width" v-model="form.ind_sms.aliyun.tpl_id"></el-input>
                        <div style="color: #909399;font-size: 12px;line-height: 1;margin-top: 10px">模板示例${code}</div>
                    </el-form-item>
                    <el-form-item label="注册审核成功模板ID">
                        <el-input class="currency-width" v-model="form.ind_sms.aliyun.register_success_tpl_id"></el-input>
                        <div style="color: #909399;font-size: 12px;line-height: 1;margin-top: 10px">
                            用于用户注册审核成功的通知${name}审核已通过
                        </div>
                    </el-form-item>
                    <el-form-item label="注册审核失败模板ID">
                        <el-input class="currency-width" v-model="form.ind_sms.aliyun.register_fail_tpl_id"></el-input>
                        <div style="color: #909399;font-size: 12px;line-height: 1;margin-top: 10px">
                            用于用户注册审核成功的通知${name}审核未通过
                        </div>
                    </el-form-item>
                    <el-form-item v-if="form.open_sms == 1" label="平台注册申请通知">
                        <el-input class="currency-width" v-model="form.ind_sms.aliyun.register_apply_tpl_id"></el-input>
                        <div style="color: #909399;font-size: 12px;line-height: 1;margin-top: 10px">
                            用于管理员接收注册申请的通知
                        </div>
                    </el-form-item>
                </template>
                <template v-if="activeName == 'five'">
                    <el-form-item label="AI客服JS代码">
                        <el-input type="textarea" rows="16" class="currency-width" v-model="form.ai_code"></el-input>
                    </el-form-item>
                </template>
                <el-form-item>
                    <el-button class="submit-btn" type="primary" @click="submit" :loading="submitLoading">保存</el-button>
                </el-form-item>
            </el-form>
        </div>
    </el-card>
    <el-dialog
            @close="dialogClose"
            title="添加权限"
            :visible.sync="dialogVisible"
            width="70%"
            @click="dialogVisible = false">
        <div class="window">
            <el-checkbox class="check-title" :indeterminate="mallIndeterminate" v-model="checkMall"
                         @change="handleCheckMallChange">基础权限
            </el-checkbox>
            <el-checkbox-group class="check-list" v-model="checkedMallPermissions" @change="handleCheckedMallChange">
                <el-checkbox v-for="item in permissions.mall" :label="item.name" :key="item.id">
                    {{item.display_name}}
                </el-checkbox>
            </el-checkbox-group>
            <template v-if="storageShow()">
                <el-checkbox class="check-title" :indeterminate="storageIndeterminate()"
                             v-model="checkStorage"
                             @change="storageCheckAll">上传权限
                </el-checkbox>
                <el-checkbox-group class="check-list" v-model="secondary_permissions.attachment"
                                   @change="storageCheck">
                    <el-checkbox v-for="(item, key) in storage" :label="key" :key="item">
                        {{item}}
                    </el-checkbox>
                </el-checkbox-group>
            </template>
        </div>
        <span slot="footer" class="dialog-footer">
            <el-button size="small" @click="dialogVisible = false">取消</el-button>
            <el-button size="small" :loading="btnLoading" type="primary" @click="updatePermission">保存</el-button>
        </span>
    </el-dialog>
</div>
<script>
    new Vue({
        el: '#app',
        data() {
            return {
                activeName: 'first',
                loading: false,
                submitLoading: false,
                line: {
                    width: '450px',
                    marginLeft: '-150px'
                },
                form: {},
                params: {
                    r: 'admin/setting/upload-logo'
                },

                dialogVisible: false,
                mallIndeterminate: false,
                checkMall: false,
                checkedMallPermissions: [],
                permissions: {
                    mall: [],
                },
                btnLoading: false,
                secondary_permissions: {
                    attachment: ["1"],
                },
                storage: [],
                checkStorage: true,
            };
        },
        created() {
            this.loadData();
            this.getPermissions();
            this.$nextTick(function () {
                this.line.width = this.$refs.body.clientWidth + 'px';
                this.line.marginLeft = -(this.$refs.body.clientWidth - 450) / 2 + 'px';
            })
        },
        methods: {
            handleClick(tab, event) {
                console.log(tab, event);
            },
            loadData() {
                this.loading = true;
                this.$request({
                    params: {
                        r: 'admin/setting/index',
                    },
                }).then(e => {
                    this.loading = false;
                    if (e.data.code === 0) {
                        if (e.data.data.setting) {
                            this.form = Object.assign({}, this.form, e.data.data.setting);
                            this.checkedMallPermissions = this.form.mall_permissions ? this.form.mall_permissions : [];
                            this.secondary_permissions = this.form.secondary_permissions.attachment ? this.form.secondary_permissions : this.secondary_permissions;
                        }
                    } else {
                        this.$message.error(e.data.msg);
                    }
                }).catch(e => {
                });
            },
            submit() {
                this.submitLoading = true;
                this.$request({
                    params: {
                        r: 'admin/setting/index',
                    },
                    method: 'post',
                    data: {
                        setting: JSON.stringify(this.form),
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
            },
            updateSuccess(e) {
                this.$message.success("上传成功")
            },
            dialogClose() {

            },
            handleCheckMallChange(val) {
                let checkedArr = [];
                if (val) {
                    this.permissions.mall.forEach(function (item, index) {
                        checkedArr.push(item.name);
                    });
                }
                this.checkedMallPermissions = checkedArr;
                this.mallIndeterminate = false;
            },
            handleCheckedMallChange(value) {
                let checkedCount = value.length;
                this.checkMall = checkedCount === this.permissions.mall.length;
                this.mallIndeterminate = checkedCount > 0 && checkedCount < this.permissions.mall.length;
            },
            getPermissions() {
                let self = this;
                request({
                    params: {
                        r: 'admin/user/permissions',
                        id: getQuery('id'),
                    },
                    method: 'get',
                }).then(e => {
                    if (e.data.code === 0) {
                        self.permissions = e.data.data.permissions;
                        self.storage = e.data.data.storage;
                    } else {
                        self.$message.error(e.data.msg);
                    }
                });
            },
            // 更新权限
            updatePermission() {
                let self = this;
                self.dialogVisible = false;
                self.form.permissions_num = self.checkedMallPermissions.length;
                self.form.mall_permissions = self.checkedMallPermissions;
                self.form.secondary_permissions = self.secondary_permissions;
                self.permissionText();
            },
            permissionText() {
                let total = this.permissions.mall.length;
                let own = this.form.permissions_num ? this.form.permissions_num : 0;

                return `"权限管理"(` + own + `/` + total + `)`;
            },
            edit() {
                let self = this;
                self.dialogVisible = true;
                self.handleCheckedMallChange(self.checkedMallPermissions);
            },
            storageShow() {
                for (let i in this.checkedMallPermissions) {
                    if (this.checkedMallPermissions[i] == 'attachment') {
                        return true;
                    }
                }
                return false;
            },
            storageCheckAll() {
                let arr = [];
                for (let i in this.storage) {
                    arr.push(i);
                }
                if (arr.length == this.secondary_permissions.attachment.length) {
                    this.secondary_permissions.attachment = [];
                    this.checkStorage = false;
                } else {
                    this.secondary_permissions.attachment = arr;
                    this.checkStorage = true;
                }
            },
            storageCheck(value) {
                let arr = [];
                for (let i in this.storage) {
                    arr.push(i);
                }
                if (this.secondary_permissions.attachment.length == arr.length) {
                    this.checkStorage = true;
                }
                this.checkStorage = false;
            },
            storageIndeterminate() {
                let arr = [];
                for (let i in this.storage) {
                    arr.push(i);
                }
                if (this.secondary_permissions.attachment.length > 0 && this.secondary_permissions.attachment.length < arr.length) {
                    return true;
                }
                if (this.secondary_permissions.attachment.length == arr.length) {
                    return false;
                }
                return false;
            },
        }
    });
</script>