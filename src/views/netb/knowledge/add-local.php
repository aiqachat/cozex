<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: chenzs
 */
Yii::$app->loadViewComponent('app-rich-text')
?>
<style>
    .table-body {
        padding: 20px 20px 0 20px;
        background-color: #fff;
    }

    .button-item {
        margin-bottom: 10px;
        margin-left: 100px;
    }
</style>
<div id="app" v-cloak>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>
                    <span style="color: #409EFF;cursor: pointer" @click="$navigate({r:'netb/knowledge/file-list', id: knowledge_id})">文件列表</span></el-breadcrumb-item>
                <el-breadcrumb-item>添加本地在线文件</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="table-body">
            <el-form :model="form" label-width="100px" :rules="rules" ref="form" v-loading="listLoading">
                <el-form-item label="文本标题" prop="name">
                    <el-input size="small" placeholder="请输入标题" v-model.trim="form.name"></el-input>
                </el-form-item>
                <el-form-item label="文本内容" prop="content">
                    <app-rich-text v-model.trim="form.content"></app-rich-text>
                </el-form-item>
                <el-button class="button-item" size="mini" :loading="listLoading" type="primary" @click="onSubmit">提交并上传</el-button>
            </el-form>
        </div>
    </el-card>
</div>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                knowledge_id: getQuery('knowledge_id'),
                form: {},
                listLoading: false,
                rules: {
                    name: [
                        {required: true, message: '标题不能为空', trigger: 'blur'},
                    ],
                    content: [
                        {required: true, message: '内容不能为空', trigger: 'blur'},
                    ],
                },
            };
        },
        methods: {
            getData() {
                this.listLoading = true;
                request({
                    params: {
                        r: 'netb/knowledge/add-local',
                        id: getQuery('id'),
                        knowledge_id: this.knowledge_id
                    },
                }).then(e => {
                    if (e.data.code === 0) {
                        this.form = e.data.data;
                    } else {
                        this.$message.error(e.data.msg);
                    }
                    this.listLoading = false;
                }).catch(e => {
                    this.listLoading = false;
                });
            },
            onSubmit(){
                let self = this;
                self.listLoading = true;
                self.form.knowledge_id = this.knowledge_id;
                request({
                    params: {r: 'netb/knowledge/add-local'},
                    data: self.form,
                    method: 'post'
                }).then(e => {
                    if (e.data.code === 0) {
                        self.$message.success('文件提交成功，数据处理中');
                        setTimeout(function (){
                            navigateTo({r:'netb/knowledge/file-list', id: self.knowledge_id})
                        }, 1000)
                    }else{
                        self.$message.error(e.data.msg);
                        self.listLoading = false;
                    }
                });
            },
        },
        mounted: function () {
            if(getQuery('id')) {
                this.getData();
            }
        }
    });
</script>
