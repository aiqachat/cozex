<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: chenzs
 */
?>
<style type="text/css">
    @import "<?= \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/css/table.css' ?>";
</style>
<style>
    .table-body {
        padding: 20px 20px 0 20px;
        background-color: #fff;
    }
</style>
<div id="app" v-cloak>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>
                    <span style="color: #409EFF;cursor: pointer" @click="$navigate({r:'mall/knowledge/index'})">资源库管理</span></el-breadcrumb-item>
                <el-breadcrumb-item>文件列表</el-breadcrumb-item>
            </el-breadcrumb>
            <div style="float: right;margin-top: -20px">
                <el-button type="primary" @click="$navigate({r:'mall/knowledge/add-file', id: id})"
                           size="small">添加上传内容(文件)</el-button>
<!--                <el-button type="primary" @click="$navigate({r:'mall/knowledge/add-local', id: id})"-->
<!--                           size="small">添加本地在线文件</el-button>-->
            </div>
        </div>
        <div slot="header" style="margin-top: 15px">
            <div style="color: #8E9190">
                <span v-if="format_type == 0">支持 PDF、TXT、DOC、DOCX、MD，最多可上传 10 个文件，每个文件不超过 100MB，PDF 最多 500 页</span>
                <span v-if="format_type == 1">上传一份Excel或CSV格式的文档，文件大小限制20MB以内。</span>
                <span v-if="format_type == 2">支持 JPG，JPEG，PNG，每个文件不超过20 MB</span>
            </div>
        </div>
        <div class="table-body">
            <div style="padding-bottom: 10px;font-weight: bold;">{{name}}</div>
            <el-table ref="multipleTable" :data="form" border style="width: 100%" v-loading="listLoading">
                <el-table-column
                  type="selection"
                  width="55">
                </el-table-column>
                <el-table-column label="资源文件">
                    <template slot-scope="scope">
                        <div flex="dir:left cross:center">
                            <app-image mode="aspectFill" style="margin-right: 8px;flex-shrink: 0" src="statics/img/mall/file.png"></app-image>
                            <div style="width: 100%;">
                                <div>{{scope.row.name}}</div>
                                <el-tooltip effect="dark" placement="bottom-start" :content="`${scope.row.desc}`">
                                    <div style="color: rgb(6 7 5 / 40%); font-size: 12px;">{{scope.row.desc}}</div>
                                </el-tooltip>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="hit_count" label="命中次数" width="95"></el-table-column>
                <el-table-column label="格式" width="80">
                    <template slot-scope="scope">
                        <el-tag v-if="scope.row.format_type == 0" size="mini" type="success">文本</el-tag>
                        <el-tag v-if="scope.row.format_type == 1" size="mini" type="success">表格</el-tag>
                        <el-tag v-if="scope.row.format_type == 2" size="mini" type="success">照片</el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="type" label="格式后缀" width="95"></el-table-column>
                <el-table-column prop="size" label="大小" width="90"></el-table-column>
                <el-table-column label="状态" width="95">
                    <template slot-scope="scope">
                        <el-tag v-if="scope.row.status == 0" size="mini">处理中</el-tag>
                        <el-tag v-else-if="scope.row.status == 1" size="mini" type="success">处理完毕</el-tag>
                        <el-tag v-else size="mini" type="danger">处理失败</el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="create_time" label="创建时间" width="160"></el-table-column>
                <el-table-column prop="update_time" label="编辑时间" width="160"></el-table-column>
                <el-table-column label="操作" width="80" fixed="right">
                    <template slot-scope="scope">
                        <el-tooltip class="item" effect="dark" content="删除" placement="top">
                            <el-button circle type="text" size="mini" @click="destroy(scope.row)">
                                <img src="statics/img/mall/del.png" alt="">
                            </el-button>
                        </el-tooltip>
                    </template>
                </el-table-column>
            </el-table>
            <div flex="dir:right" style="margin-top: 20px;">
                <el-pagination @current-change="pagination" hide-on-single-page background layout="total, prev, pager, next, jumper"
                               :total="pageCount" :page-size="pageSize" :current-page="currentPage"></el-pagination>
            </div>
        </div>
    </el-card>
</div>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                id: getQuery('id'),
                searchData: {keyword: ''},
                form: [],
                format_type: '',
                name: '',
                pageCount: 0,
                pageSize: 10,
                page: 1,
                currentPage: null,
                listLoading: false,
                btnLoading: false,
            };
        },
        methods: {
            edit(row){

            },
            destroy: function (column) {
                this.$confirm('确认删除该记录吗?', '提示', {
                    type: 'warning'
                }).then(() => {
                    this.listLoading = true;
                    request({
                        params: {r: 'mall/knowledge/file-destroy'},
                        data: {document_id: column.document_id, id: this.id},
                        method: 'post'
                    }).then(e => {
                        if (e.data.code !== 0) {
                            this.$message.error(e.data.msg);
                        }
                        this.getList()
                    }).catch(e => {
                        this.listLoading = false;
                    });
                });
            },
            pagination(currentPage) {
                this.page = currentPage;
                this.getList();
            },
            getList() {
                this.listLoading = true;
                let param = Object.assign({
                    r: 'mall/knowledge/file-list',
                    page: this.page,
                    id: this.id
                }, this.searchData);
                request({
                    params: param,
                }).then(e => {
                    if (e.data.code === 0) {
                        this.form = e.data.data.list;
                        this.name = e.data.data.name;
                        this.format_type = e.data.data.format_type;
                        this.pageCount = e.data.data.pagination.total_count;
                        this.pageSize = e.data.data.pagination.pageSize;
                        this.currentPage = e.data.data.pagination.current_page;
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
            this.page = getQuery('page') ? getQuery('page') : 1;
            this.getList();
        }
    });
</script>
