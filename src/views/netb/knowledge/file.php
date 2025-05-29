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

    .bi {
        font-size: 20px;
    }
</style>
<div id="app" v-cloak>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>
                    <span style="color: #409EFF;cursor: pointer" @click="$navigate({r:'netb/knowledge/index'})">资源库管理</span></el-breadcrumb-item>
                <el-breadcrumb-item>文件列表</el-breadcrumb-item>
            </el-breadcrumb>
            <div style="float: right;margin-top: -20px">
                <el-button type="primary" @click="$navigate({r:'netb/knowledge/add-file', id: id})"
                           size="small">添加上传内容(文件)</el-button>
                <el-button type="primary" @click="$navigate({r:'netb/knowledge/add-local', knowledge_id: id})"
                           size="small" v-if="format_type == 0">添加本地在线文件</el-button>
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
                <el-table-column label="资源文件" min-width="300">
                    <template slot-scope="scope">
                        <div flex="dir:left cross:center">
                            <app-image mode="aspectFill" style="margin-right: 8px;flex-shrink: 0" :src="img"></app-image>
                            <div style="width: 100%;" v-if="document_id != scope.row.document_id" flex="dir:left cross:center">
                                <span>{{scope.row.name}}</span>
                                <el-button type="text" @click="editName(scope.row)">
                                    <i class="bi bi-pencil-fill" style="margin-left: 5px;font-size: 13px;"></i>
                                </el-button>
                            </div>
                            <div style="display: flex;align-items: center" v-else v-loading="btnLoading">
                                <el-input style="min-width: 70px" type="text" size="mini" v-model="dName" autocomplete="off"></el-input>
                                <el-button type="text" style="color: #F56C6C;padding: 0 5px" icon="el-icon-error"
                                           circle @click="quit()"></el-button>
                                <el-button type="text" style="margin-left: 0;color: #67C23A;padding: 0 5px"
                                           icon="el-icon-success" circle @click="changeSubmit(scope.row)">
                                </el-button>
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
                <el-table-column label="操作" width="130" fixed="right">
                    <template slot-scope="scope">
                        <el-tooltip effect="dark" content="删除" placement="top">
                            <el-button circle type="text" size="mini" @click="destroy(scope.row)">
                                <i class="bi bi-trash"></i>
                            </el-button>
                        </el-tooltip>
                        <el-tooltip effect="dark" content="编辑" placement="top" v-if="scope.row.file_id">
                            <el-button circle type="text" size="mini" @click="edit(scope.row)">
                                <i class="bi bi-pencil-square"></i>
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
                img: '',
                document_id: '',
                dName: '',
            };
        },
        methods: {
            edit(row){
                navigateTo({r:'netb/knowledge/add-local', knowledge_id: this.id, id: row.file_id})
            },
            changeSubmit(row) {
                let self = this;
                request({
                    params: {
                        r: 'netb/knowledge/update-file'
                    },
                    method: 'post',
                    data: {
                        id: self.id,
                        document_id: row.document_id,
                        name: self.dName,
                    }
                }).then(e => {
                    self.btnLoading = false;
                    if (e.data.code === 0) {
                        row.name = self.dName;
                        self.$message.success(e.data.msg);
                        this.quit();
                    } else {
                        self.$message.error(e.data.msg);
                    }
                }).catch(e => {
                    self.btnLoading = false;
                });
            },
            editName(row) {
                this.document_id = row.document_id;
                this.dName = row.name;
            },
            quit() {
                this.document_id = null;
                this.dName = null;
            },
            destroy: function (column) {
                this.$confirm('确认删除该记录吗?', '提示', {
                    type: 'warning'
                }).then(() => {
                    this.listLoading = true;
                    request({
                        params: {r: 'netb/knowledge/file-destroy'},
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
                    r: 'netb/knowledge/file-list',
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
                        this.img = 'statics/img/mall/file.png';
                        if (this.format_type === '1') {
                            this.img = 'statics/img/mall/table.png';
                        }
                        if (this.format_type === '2') {
                            this.img = 'statics/img/mall/image.png';
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
        mounted: function () {
            this.page = getQuery('page') ? getQuery('page') : 1;
            this.getList();
        }
    });
</script>
