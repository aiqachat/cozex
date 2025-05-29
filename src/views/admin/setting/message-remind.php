<?php

/**
 * Created by IntelliJ IDEA.
 * author: wstianxia
 * Date: 2019/3/1
 * Time: 17:05
 */

/* @var $this \yii\web\View */
Yii::$app->loadViewComponent('app-rich-text');
?>
<style>
    #app .el-checkbox {
        margin-bottom: 0;
    }

    .button-item {
        margin-top: 20px;
        padding: 9px 25px;
    }
    .remind-text {
        margin-left: 10px;
        color: #606266;
    }
    .privew-box {
        border: 1px solid #DCDFE6;
        height: 300px;
        margin: 30px;
    }
    .privew-box .head {
        border-bottom: 1px solid #DCDFE6;
        height: 50px;
        font-size: 18px;
        margin: 0 20px;
    }
    .message-title {
        margin: 30px 20px;
    }
    .message-hint {
        margin: 30px 20px;
        border-radius: 5px;
        padding: 10px 20px;
        color: #ff4544;
        background-color: #ffefef;
        font-size: 14px;
    }
</style>
<div id="app" v-cloak>
    <el-card shadow="never" v-loading="cardLoading">
        <el-form @submit.native.prevent style="width: 75%;" :model="ruleForm" :rules="rules" ref="ruleForm" label-width="130px" size="small">
            <el-form-item label="到期提醒开关" prop="status">
                <el-switch
                        v-model="ruleForm.status"
                        :active-value=1
                        :inactive-value=0>
                </el-switch>
            </el-form-item>
            <el-form-item label="在子账户过期前" prop="day">
                <div flex="dir:left">
                    <div flex-box="1">
                        <el-input v-model="ruleForm.day" type="number" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            <el-button slot="append">天</el-button>
                        </el-input>
                    </div>
                    <span class="remind-text">进行提醒</span>
                </div>
            </el-form-item>
            <el-form-item label="内容" prop="message_text">
                <app-rich-text :is-show-insert-image="false" style="width: 750px" v-model="ruleForm.message_text"></app-rich-text>
            </el-form-item>
        </el-form>
    </el-card>

    <el-dialog
            title="预览"
            :visible.sync="dialogVisible"
            width="50%">
        <div class="privew-box">
            <div class="head" flex="dir:left cross:center box:last">
                <span>即将到期提醒</span>
                <span>X</span>
            </div>
            <div class="message-title" v-html="ruleForm.message_text"></div>
            <div class="message-hint">
                <div>您的账户将在{{ruleForm.day}}天后到期</div>
                <div>到期时间：{{newDate}}</div>
            </div>
        </div>
        <span slot="footer" class="dialog-footer">
        <el-button size="small" @click="dialogVisible = false">继续编辑</el-button>
        <el-button :loading="loading" size="small" type="primary" @click="submit">保存</el-button>
      </span>
    </el-dialog>

    <el-button class="button-item" type="primary" :loading="loading" @click="submit">保存</el-button>
    <el-button class="button-item" :loading="loading" @click="resetDefault">恢复默认</el-button>
    <el-button class="button-item" :loading="loading" @click="priview">预览</el-button>
</div>
<script>
    new Vue({
        el: '#app',
        data() {
            return {
                loading: false,
                ruleForm: {},
                rules: {
                    day: [
                        {required: true, message: "请输入天数",},
                    ],
                    message_text: [
                        {required: true, message: "请输入内容",},
                    ],
                },
                dialogVisible: false,
                newDate: '',
            };
        },
        created() {
            this.getSetting();
        },
        methods: {
            submit() {
                this.loading = true;
                this.$request({
                    params: {
                        r: 'admin/setting/message-remind',
                    },
                    method: 'post',
                    data: {
                        form: this.ruleForm
                    },
                }).then(e => {
                    this.loading = false;
                    this.dialogVisible = false;
                    if (e.data.code === 0) {
                        this.getSetting();
                        this.$message.success(e.data.msg);
                    } else {
                        this.$message.error(e.data.msg);
                    }
                }).catch(e => {
                });
            },
            getSetting() {
                this.cardLoading = true;
                this.$request({
                    params: {
                        r: 'admin/setting/message-remind',
                    },
                    method: 'get',
                }).then(e => {
                    this.cardLoading = false;
                    this.ruleForm = e.data.data.setting;
                    console.log(this.ruleForm);
                }).catch(e => {
                });
            },
            resetDefault() {
                this.loading = true;
                this.$request({
                    params: {
                        r: 'admin/setting/message-remind-reset',
                    },
                    method: 'post',
                    data: {},
                }).then(e => {
                    this.loading = false;
                    if (e.data.code === 0) {
                        this.ruleForm.message_text = '';
                        this.getSetting();
                        this.$message.success(e.data.msg);
                    } else {
                        this.$message.error(e.data.msg);
                    }
                }).catch(e => {
                });
            },
            priview() {
                this.newDate = this.getDateStr(parseInt(this.ruleForm.day));
                this.dialogVisible = true;
            },
            getDateStr(AddDayCount) {
                var dd = new Date();
                dd.setDate(dd.getDate() + AddDayCount); //获取AddDayCount天后的日期  
                var y = dd.getFullYear();
                var m = (dd.getMonth() + 1) < 10 ? "0" + (dd.getMonth() + 1) : (dd.getMonth() + 1); //获取当前月份的日期，不足10补0  
                var d = dd.getDate() < 10 ? "0" + dd.getDate() : dd.getDate(); //获取当前几号，不足10补0  
                var H = dd.getHours() < 10 ? "0" + dd.getHours() : dd.getHours();
                var i = dd.getMinutes() < 10 ? "0" + dd.getMinutes() : dd.getMinutes();
                var s = dd.getSeconds() < 10 ? "0" + dd.getSeconds() : dd.getSeconds();
                return y + "-" + m + "-" + d + "    " + H + ":" + i + ":" + s;
            },
            stop() {

            }
        },
    });
</script>
