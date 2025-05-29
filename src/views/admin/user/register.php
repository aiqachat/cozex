<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */
?>
<style>
    .input-item {
        width: 300px;
        margin-top: 20px;
    }

    .input-item .el-input__inner {
        border-right: 0;
    }

    .input-item .el-input__inner:hover {
        border: 1px solid #dcdfe6;
        border-right: 0;
        outline: 0;
    }

    .input-item .el-input__inner:focus {
        border: 1px solid #dcdfe6;
        border-right: 0;
        outline: 0;
    }

    .input-item .el-input-group__append {
        background-color: #fff;
        border-left: 0;
        width: 10%;
        padding: 0;
    }

    .input-item .el-input-group__append .el-button {
        padding: 0;
    }

    .input-item .el-input-group__append .el-button {
        margin: 0;
    }
</style>
<div id="app" v-cloak>
    <el-card class="box-card" shadow="never">
        <div>
            <div class="input-item">
                <el-input @keyup.enter.native="getList" size="small" placeholder="请输入账号" v-model="keyword" clearable @clear='getList'>
                    <el-button slot="append" @click="getList" icon="el-icon-search"></el-button>
                </el-input>
            </div>
            <el-table
                    :data="list"
                    v-loading="listLoading"
                    style="width: 100%;font-size: 12px;">
                <el-table-column label="申请帐号" prop="username" width="150">
                </el-table-column>
                <el-table-column label="申请人信息" prop="name" width="220">
                    <template slot-scope="scope">
                        <div>{{scope.row.name}}</div>
                        <div>{{scope.row.mobile}}</div>
                        <div>微信号: {{scope.row.wechat_id}}</div>
                    </template>
                </el-table-column>
                <el-table-column
                        label="申请时间"
                        prop="created_at" width="90">
                </el-table-column>
                <el-table-column label="申请理由" prop="remark">
                    <template slot-scope="props">
                        <el-tooltip class="item" effect="dark" :content="props.row.remark" placement="top">
                            <span class="remark">{{ props.row.lessRemark }}</span>
                        </el-tooltip>
                    </template>
                </el-table-column>
                <el-table-column label="证件信息" prop="remark" width="95">
                    <template slot-scope="props">
                        <el-button type="text" size="mini" @click="showCardDialog(props.row)">点击查看</el-button>
                    </template>
                </el-table-column>
                <el-table-column label="操作" width="150">
                    <template slot-scope="scope">
                        <template v-if="scope.row.status == 0">
                            <el-button @click="audit(scope.row.id, 1)" plain size="mini" type="info">通过</el-button>
                            <el-button @click="audit(scope.row.id, 2)" plain size="mini" type="info">拒绝</el-button>
                        </template>
                        <el-button disabled v-if="scope.row.status == 1" plain type="info"
                                   size="mini">已通过
                        </el-button>
                        <el-button disabled v-if="scope.row.status == 2" plain type="info"
                                   size="mini">已拒绝
                        </el-button>
                        <el-button v-if="scope.row.status != 0" @click="destroy(scope.row.id)" plain type="info"
                                   size="mini">删除
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>

            <div style="text-align: center;margin: 20px 0;">
                <el-pagination
                        @current-change="pagination"
                        background
                        layout="prev, pager, next"
                        :page-count="pageCount">
                </el-pagination>
            </div>
        </div>
    </el-card>
    <el-dialog :visible.sync="dialogVisible" width="60%">
        <el-carousel v-if="currentUser" class='banner' height="600px" :autoplay="autoplay" indicator-position="outside">
            <el-carousel-item>
                <img :src="currentUser.id_card_front_pic" style="border: 1px solid #e2e2e2;min-height: 200px;min-width: 200px;">
                <div>身份证正面</div>
            </el-carousel-item>
            <el-carousel-item>
                <img :src="currentUser.id_card_back_pic" style="border: 1px solid #e2e2e2;min-height: 200px;min-width: 200px;">
                <div>身份证反面</div>
            </el-carousel-item>
            <el-carousel-item>
                <img :src="currentUser.business_pic" style="border: 1px solid #e2e2e2;min-height: 200px;min-width: 200px;">
                <div>营业执照</div>
            </el-carousel-item>
        </el-carousel>
    </el-dialog>
</div>

<style>
    .el-table thead {
        color: #555555;
        font-size: 13px;
    }

    .has-gutter {
        height: 35px;
        line-height: 35px;
    }

    .el-carousel__item img {
        height: 500px;
        text-align: center;
    }

    .banner {
        text-align: center;
    }

    .el-card__body {
        padding: 0 20px;
    }

    #app .el-table .el-button {
        border-radius: 16px;
        border: 0;
        padding: 8px 12px;
        color: #353535;
    }

    .el-tooltip__popper {
        max-width: 30%;
    }

</style>

<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                list: [],
                dialogVisible: false,
                listLoading: false,
                autoplay: false,
                page: 1,
                keyword: '',
                img: {
                    url: _baseUrl + '/statics/img/admin/id-card.png',
                    id_card_off: _baseUrl + '/statics/img/admin/id-card-off.png',
                    business: _baseUrl + '/statics/img/admin/business.png',
                },
                pageCount: 0,
                currentUser: null,
            };
        },
        methods: {
            pagination(currentPage) {
                let self = this;
                self.page = currentPage;
                self.getList();
            },
            getList() {
                let self = this;
                self.listLoading = true;
                request({
                    params: {
                        r: 'admin/user/register',
                        page: self.page,
                        keyword: self.keyword,
                    },
                    method: 'get',
                }).then(e => {
                    self.listLoading = false;
                    if (e.data.code === 0) {
                        self.list = e.data.data.list;
                        self.pageCount = e.data.data.pagination.page_count;
                        for (let i = 0; i < self.list.length; i++) {
                            console.log(self.list[i].remark.length)
                            self.list[i].created_at = self.list[i].created_at.slice(0, 10)
                            if(self.list[i].remark.length > 30) {
                                self.list[i].lessRemark = self.list[i].remark.slice(0,30) + '...'
                            }else {
                                self.list[i].lessRemark = self.list[i].remark
                            }
                        }

                    }
                }).catch(e => {
                    console.log(e);
                });
            },
            showCardDialog(e) {
                this.currentUser = e;
                this.dialogVisible = true;
            },
            audit(id, status) {
                let self = this;
                let text = status == 1 ? "审核通过" : "审核拒绝";
                self.$confirm(text + ", 是否继续 ?", "提示", {
                    confirmButtonText: "确定",
                    cancelButtonText: "取消",
                    type: 'warning'
                }).then(() => {
                    request({
                        params: {
                            r: 'admin/user/register-audit',
                        },
                        method: 'post',
                        data: {
                            id: id,
                            status: status
                        }
                    }).then(e => {
                        if (e.data.code === 0) {
                            self.$message.success(e.data.msg);
                            self.getList();
                        } else {
                            self.$message.error(e.data.msg);
                        }
                    }).catch(e => {
                        console.log(e);
                    });
                }).catch(() => {
                });
            },
            destroy(id) {
                let self = this;
                self.$confirm("删除该条记录", "提示", {
                    confirmButtonText: "确定",
                    cancelButtonText: "取消",
                    type: 'warning'
                }).then(() => {
                    request({
                        params: {
                            r: 'admin/user/register-destroy',
                        },
                        method: 'post',
                        data: {
                            id: id,
                        }
                    }).then(e => {
                        if (e.data.code === 0) {
                            self.$message.success(e.data.msg);
                            self.getList();
                        } else {
                            self.$message.error(e.data.msg);
                        }
                    }).catch(e => {
                        console.log(e);
                    });
                }).catch(() => {
                    self.$message.info("已取消删除")
                });
            },
        },
        mounted: function () {
            this.getList();
        }
    });
</script>
