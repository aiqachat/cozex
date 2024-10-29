<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: chenzs
 */
?>
<style>
    .table-body {
        padding: 20px;
        background-color: #fff;
    }

    .code-block {
        max-height: 280px;
        overflow-y: auto;
        white-space: pre-wrap;
        padding: 20px;
        background-color: #EFEFEF;
    }
</style>
<div id="app" v-cloak>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <span>智能体配置</span>
        </div>
        <div class="table-body" v-loading="listLoading">
            <el-card class="box-card">
                <div slot="header" class="clearfix">
                    <span>第三方平台安装部署代码</span>
                    <span style="color: #a4a4a4;">（直接将代码粘贴到网页的 &lt;body&gt; 区域中即可。你也可以按需修改各种属性配置）</span>
                </div>
                <span style="color: red;">注：*图标显示不正常请把站点配置SSL证书，需要https访问</span>
                <div flex>
                    <pre class="code-block" id="target">{{ code }}</pre>
                </div>
                <el-button style="margin-top: 10px;" type="primary" size="mini" id="copy_btn"
                           data-clipboard-action="copy" data-clipboard-target="#target">复制代码</el-button>
            </el-card>
            <el-card class="box-card" style="margin-top: 10px;">
                <div slot="header" class="clearfix">
                    <span>配置项</span>
                </div>
                <el-form :model="data" label-width="120px" :rules="dataRules" ref="data">
                    <el-form-item label="SDK版本号" prop="version" size="small">
                        <el-input v-model.trim="data.version" style="width: 400px"></el-input>
                        <div style="color: #a4a4a4;">注：发布智能体到Web SDK后，发布页面的安装代码中获取最新版本的Web SDK版本号，例如 0.1.0-beta.6</div>
                    </el-form-item>
                    <el-form-item label="智能体名字" prop="title" size="small">
                        <el-input v-model.trim="data.title" style="width: 400px"></el-input>
                    </el-form-item>
                    <el-form-item label="智能体图标" prop="icon" size="small">
                        <app-attachment style="margin-bottom:10px" :multiple="false" :max="1"
                                        v-model="data.icon">
                            <el-tooltip effect="dark"
                                        content="建议尺寸:56 * 56"
                                        placement="top">
                                <el-button size="mini">选择图标</el-button>
                            </el-tooltip>
                        </app-attachment>
                        <div style="cursor: move;">
                            <app-attachment :multiple="false" :max="1" v-model="data.icon">
                                <app-image mode="aspectFill" width="45px" height='45px' :src="data.icon"></app-image>
                            </app-attachment>
                        </div>
                    </el-form-item>
                    <el-form-item label="窗口语言" prop="lang" size="small">
                        <el-radio v-model="data.lang" label="en">英文</el-radio>
                        <el-radio v-model="data.lang" label="zh-CN">中文</el-radio>
                    </el-form-item>
                    <el-form-item label="布局风格" prop="layout" size="small">
                        <el-radio v-model="data.layout" label="">自动风格</el-radio>
                        <el-radio v-model="data.layout" label="mobile">手机端风格</el-radio>
                        <el-radio v-model="data.layout" label="pc">PC端风格</el-radio>
                    </el-form-item>
                    <el-form-item label="窗口宽度" prop="is_width" size="small">
                        <el-radio-group v-model="data.is_width">
                            <el-radio :label="1">默认460</el-radio>
                            <el-radio :label="2">
                                <el-input type="number" v-model.trim="data.width"></el-input>
                            </el-radio>
                        </el-radio-group>
                    </el-form-item>
                </el-form>
                <el-button v-loading="btnLoading" type="primary" size="mini" @click="save">保存</el-button>
            </el-card>
        </div>
    </el-card>
</div>
<script src="<?= Yii::$app->request->baseUrl ?>/statics/js/clipboard.min.js"></script>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                form: [],
                listLoading: false,
                btnLoading: false,
                code: '',

                data: {},
                dataRules: {
                    title: [
                        {required: true, message: '名称不能为空', trigger: 'blur'},
                    ],
                },
            };
        },
        methods: {
            save() {
                this.$refs.data.validate((valid) => {
                    if (valid) {
                        this.btnLoading = true;
                        request({
                            params: {r: 'mall/bot/set'},
                            method: 'post',
                            data: {data: this.data},
                        }).then(e => {
                            if (e.data.code === 0) {
                                this.$message.success(e.data.msg);
                                setTimeout(() => {
                                    navigateTo({ r: 'mall/bot/index'});
                                }, 1000);
                            } else {
                                this.$message.error(e.data.msg);
                            }
                            this.btnLoading = false;
                        }).catch(e => {
                            this.btnLoading = false;
                        });
                    }
                });
            },
            getList() {
                this.listLoading = true;
                request({
                    params: {r: 'mall/bot/set', bot_id: getQuery('bot_id')},
                }).then(e => {
                    if (e.data.code === 0) {
                        this.code = e.data.data.code;
                        this.data = e.data.data.data;
                    } else {
                        this.$message.error(e.data.msg);
                    }
                    this.listLoading = false;
                }).catch(e => {
                    this.listLoading = false;
                });
            },
        },
        mounted: function () {
            this.getList();
        }
    });

    var clipboard = new Clipboard('#copy_btn');
    var self = this;
    clipboard.on('success', function (e) {
        self.ELEMENT.Message.success('复制成功');
        e.clearSelection();
    });
    clipboard.on('error', function (e) {
        self.ELEMENT.Message.success('复制失败');
    });
</script>
