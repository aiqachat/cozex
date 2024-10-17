<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: chenzs
 */
Yii::$app->loadViewComponent('app-attachment-dragging')
?>
<style type="text/css">
    @import "<?= \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/css/table.css' ?>";
</style>
<style>
    .table-body {
        padding: 20px 20px 0 20px;
        background-color: #fff;
    }

    .button-item {
        float: right;
        margin: 10px;
    }

    .el-card__body {
        padding: 10px 20px;
    }

    .rsd {
        margin-top: 10px;
        border: 1px solid #DADADA;
        border-radius: 10px;
        padding: 15px
    }

    .rsd:hover, .que {
        background-color: #F4F4F6;
        cursor: pointer;
        border-color: #4E40E5;
    }
</style>
<div id="app" v-cloak>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>
                    <span style="color: #409EFF;cursor: pointer" @click="$navigate({r:'mall/knowledge/file-list', id: id})">文件列表</span></el-breadcrumb-item>
                <el-breadcrumb-item>添加内容(文件)</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="table-body">
            <el-form :model="form" label-width="120px" :rules="rules" ref="form" v-loading="listLoading">
                <el-steps :active="active" :space="500" finish-status="success" simple v-if="is_set">
                    <el-step title="上传文件" icon="el-icon-upload"></el-step>
                    <el-step title="分段设置" icon="el-icon-set-up"></el-step>
                    <el-step title="数据处理" icon="el-icon-loading"></el-step>
                </el-steps>
                <el-steps :active="active" :space="500" finish-status="success" simple v-else>
                    <el-step title="上传文件" icon="el-icon-upload"></el-step>
                    <el-step title="数据处理" icon="el-icon-loading"></el-step>
                </el-steps>
                <template v-if="active == 0">
                    <app-attachment-dragging :notice="notice" :max="max" :type="data.format_type" @success="success"></app-attachment-dragging>

                    <el-card style="margin-top: 10px" v-for="(item, index) in form.files">
                        <div>{{item.name}}</div>
                        <div style="color: #CCCCCC; font-size: 12px">{{item.size}}</div>
                        <div style="float: right; margin-top: -40px;">
                            <el-button circle type="text" size="mini" @click="destroy(index)">
                                <img src="statics/img/mall/del.png" alt="">
                            </el-button>
                        </div>
                    </el-card>
                </template>
                <template v-if="active == 1 && is_set">
                    <div class="rsd" @click="choose('auto')" :class="{'que': form.type == 'auto'}">
                        <div>自动分段与清洗</div>
                        <div style="margin-top: 10px; color: #8E9190">自动分段与预处理规则</div>
                    </div>
                    <div class="rsd" @click="choose('custom')" :class="{'que': form.type == 'custom'}">
                        <div>自定义</div>
                        <div style="margin-top: 10px; color: #8E9190">自定义分段规则、分段长度及预处理规则</div>
                        <div v-if="form.type == 'custom'">
                            <el-divider content-position="left"></el-divider>
                            <el-form-item label="分段标识符">
                                <el-select v-model="form.separator" size="small">
                                    <el-option label="换行" value="\n"></el-option>
                                    <el-option label="2个换行" value="\n\n"></el-option>
                                    <el-option label="中文句号" value="。"></el-option>
                                    <el-option label="中文叹号" value="！"></el-option>
                                    <el-option label="英文句号" value="."></el-option>
                                    <el-option label="英文叹号" value="!"></el-option>
                                    <el-option label="中文问号" value="？"></el-option>
                                    <el-option label="英文问号" value="?"></el-option>
                                    <el-option label="自定义" value=""></el-option>
                                </el-select>
                                <el-input size="small" v-model="form.separator_custom" v-if="form.separator == ''"></el-input>
                            </el-form-item>
                            <el-form-item label="分段最大长度">
                                <el-input size="small" v-model="form.max_length" min="100" max="2000" type="number"></el-input>
                            </el-form-item>
                            <el-form-item label="文本预处理规则">
                                <el-checkbox-group v-model="form.handle_rule">
                                    <el-checkbox label="remove_extra_spaces">替换掉连续的空格、换行符和制表符</el-checkbox>
                                    <el-checkbox label="remove_urls_emails">删除所有 URL 和电子邮箱地址</el-checkbox>
                                </el-checkbox-group>
                            </el-form-item>
                        </div>
                    </div>
                </template>
                <div style="margin-top: 10px;">
                    <el-button class="button-item" size="mini" :loading="btnLoading" type="primary" @click="onSubmit">{{buttonText}}</el-button>
                    <el-button class="button-item" size="mini" v-if="active > 0" @click="quit">上一步</el-button>
                </div>
            </el-form>
        </div>
    </el-card>
</div>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                id: getQuery('id'),
                form: {
                    files: [],
                    handle_rule: [],
                    max_length: 800,
                    type: '',
                    separator: '\\n',
                    separator_custom: '###',
                },
                data: {},
                btnLoading: false,
                listLoading: false,
                uploading: false,
                rules: {},

                active: 0,
                buttonText: '下一步',
                max: 1,
                notice: '',

                dialog: false,
                progress: 0,

                is_set: null,
            };
        },
        methods: {
            choose(type) {
                this.form.type = type;
            },
            destroy(index) {
                this.$delete(this.form.files, index);
            },
            getData() {
                this.listLoading = true;
                request({
                    params: {
                        r: 'mall/knowledge/add-file',
                        id: this.id
                    },
                }).then(e => {
                    if (e.data.code === 0) {
                        this.data = e.data.data.data;
                        this.is_set = e.data.data.is_set;
                        if(this.data.format_type === 0){
                            this.notice = '支持 PDF、TXT、DOC、DOCX、MD，最多可上传 20 个文件，每个文件不超过 100MB，PDF 最多 500 页';
                            this.max = 20;
                        }else if(this.data.format_type === 1){
                            this.notice = '上传一份Excel或CSV格式的文档，文件大小限制20MB以内。';
                            this.max = 1;
                        }else if(this.data.format_type === 2){
                            this.notice = '支持 JPG，JPEG，PNG，每个文件不超过20 MB';
                            this.max = 20;
                        }
                    } else {
                        this.$message.error(e.data.msg);
                    }
                    this.listLoading = false;
                }).catch(e => {
                    this.listLoading = false;
                });
            },
            success(file){
                if (file.response.data && file.response.data.code === 0) {
                    this.form.files.push({
                        id: file.response.data.data.id,
                        name: file.response.data.data.name,
                        size: file.response.data.data.size,
                    });
                }
            },
            quit(){
                this.active--;
                this.buttonText = '下一步';
            },
            onSubmit(){
                let self = this;
                if(self.form.files.length === 0){
                    self.$message.error('请先上传文件');
                    return;
                }
                if(self.buttonText === '提交'){
                    self.btnLoading = true;
                    self.form.id = self.id;
                    request({
                        params: {r: 'mall/knowledge/add-file'},
                        data: self.form,
                        method: 'post'
                    }).then(e => {
                        if (e.data.code === 0) {
                            self.$message.success('文件提交成功，数据处理中');
                            setTimeout(function (){
                                navigateTo({r:'mall/knowledge/file-list', id: self.id})
                            }, 1000)
                        }else{
                            self.$message.error(e.data.msg);
                            self.btnLoading = false;
                        }
                    });
                }
                self.active++;
                if(self.active >= 2 && self.is_set){
                    self.buttonText = '提交';
                }
                if(self.active >= 1 && !self.is_set){
                    self.buttonText = '提交';
                }
            },
        },
        mounted: function () {
            this.getData();
        }
    });
</script>
