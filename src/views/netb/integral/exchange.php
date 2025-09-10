<?php defined('YII_ENV') or exit('Access Denied'); ?>
<style>
    .set-el-button {
        padding: 0 !important;
        border: 0;
        margin: 0 5px;
    }

    .table-body {
        padding: 20px;
        background-color: #fff;
    }

    .table-info .el-button {
        padding: 0 !important;
        border: 0;
        margin: 0 5px;
    }

    .input-item {
        display: inline-block;
        width: 250px;
        margin: 0 0 20px;
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

    .table-body {
        padding: 20px;
        background-color: #fff;
    }

    .bi {
        font-size: 20px;
    }
</style>
<div id="app" v-cloak>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <span>兑换管理</span>
            <el-button style="float: right; margin: -5px 10px" type="primary" size="small"
                       @click="$navigate({r:'netb/integral/edit'})">添加兑换方案
            </el-button>
        </div>
        <div class="table-body">
            <div class="input-item">
                <el-input @keyup.enter.native="search" size="small" placeholder="请输入搜索内容" v-model="keyword" clearable @clear="search">
                    <el-button slot="append" icon="el-icon-search" @click="search"></el-button>
                </el-input>
            </div>
            <el-table v-loading="loading" border :data="list" style="width: 100%;margin-bottom: 15px">
                <el-table-column prop="id" label="ID" width="50"></el-table-column>
                <el-table-column prop="name" label="兑换方案" min-width="100"></el-table-column>
                <el-table-column prop="pay_price" label="支付金额" width="80"></el-table-column>
                <el-table-column prop="send_integral" label="兑换积分" width="120"></el-table-column>
                <el-table-column prop="give_integral" label="赠送积分" width="120"></el-table-column>
                <el-table-column prop="buy_num" label="可购买次数" width="100"></el-table-column>
                <el-table-column prop="period" label="有效期" width="100"></el-table-column>
                <el-table-column prop="serial_num" label="排序" width="100"></el-table-column>
                <el-table-column prop="created_at" label="创建时间" width="150"></el-table-column>
                <el-table-column label="操作" width="150">
                    <template slot-scope="scope">
                        <el-button class="set-el-button" type="text" size="mini" circle
                                   @click="handleEdit(scope.$index, scope.row,list.id)">
                            <el-tooltip class="item" effect="dark" content="编辑" placement="top">
                                <i class="bi bi-pencil-square"></i>
                            </el-tooltip>
                        </el-button>
                        <el-button type="text" class="set-el-button" size="mini" circle
                                   @click="handleDel(scope.$index, scope.row,list.id)">
                            <el-tooltip class="item" effect="dark" content="删除" placement="top">
                                <i class="bi bi-trash"></i>
                            </el-tooltip>
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
<!--            <div flex="box:last cross:center">-->
<!--                <div></div>-->
<!--                <el-pagination-->
<!--                        v-if="pagination"-->
<!--                        style="display: inline-block;float: right;"-->
<!--                        background-->
<!--                        :page-size="pagination.pageSize"-->
<!--                        @current-change="pageChange"-->
<!--                        layout="prev, pager, next, jumper"-->
<!--                        :total="pagination.total_count">-->
<!--                </el-pagination>-->
<!--            </div>-->
        </div>
    </el-card>
</div>


<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                loading: false,
                list: [],
                keyword: '',
                pagination: null,
            };
        },
        methods: {
            search() {
                this.getList();
            },
            // 新增功能
            handleAdd: function() {
                navigateTo({r: 'netb/integral/edit'});
            },

            //带着ID前往编辑页面
            handleEdit: function(row, column)
            {
                navigateTo({r: 'netb/integral/edit',id:column.id});
            },

            // 前往设置页
            handleSetting: function(row, column)
            {
                navigateTo({r: 'netb/integral/setting'});
            },

            getList() {
                this.loading = true;
                request({
                    params: {
                        r: 'netb/integral/exchange',
                        keyword: this.keyword,
                    },
                }).then(e => {
                    if (e.data.code === 0) {
                        this.list = e.data.data.list;
                    }
                    this.loading = false;
                }).catch(e => {
                });
            },

            //删除
            handleDel: function(row, column) {
                this.$confirm('确认删除该记录吗?', '提示', {
                    type: 'warning'
                }).then(() => {
                    let para = { id: column.id};
                    request({
                        params: {
                            r: 'netb/integral/destroy'
                        },
                        data: para,
                        method: 'post'
                    }).then(e => {
                        if (e.data.code === 0) {
                            const h = this.$createElement;
                            this.$message({
                                message: '删除成功',
                                type: 'success'
                            });
                            setTimeout(function(){
                                location.reload();
                            },300);
                        }else{
                            this.$alert(e.data.msg, '提示', {
                                confirmButtonText: '确定'
                            })
                        }
                    }).catch(e => {
                        this.$alert(e.data.msg, '提示', {
                            confirmButtonText: '确定'
                        })
                    });
                })
            }
        },
        mounted() {
            this.getList();
        }
    })
</script>
