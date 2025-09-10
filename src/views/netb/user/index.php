<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

use app\forms\common\CommonUser;

$url = CommonUser::userWebUrl();
?>
<style type="text/css">
    @import "<?= \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/css/table.css' ?>";
</style>
<style>
    .table-body {
        padding: 20px 20px 0 20px;
        background-color: #fff;
    }

    .table-info .el-button {
        padding: 0!important;
        border: 0;
        margin: 0 5px;
    }

    .input-item {
        display: inline-block;
        width: 360px;
        margin: 0 0 20px;
    }

    .remark {
        word-break: break-all;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 4;
        overflow: hidden;
        line-height: 25px;
        max-height: 100px;
    }

    .bi {
        font-size: 20px;
    }


    .el-alert .el-button {
        margin-left: 20px;
    }

    .el-alert__content {
        display: flex;
        align-items: center;
    }

    .table-body .el-alert__title {
        margin-top: 5px;
    }

    .el-select .el-input__inner {
        text-align: center;
    }
</style>
<div id="app" v-cloak>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <div flex="main:justify">
                <span style="line-height: 32px;">用户管理</span>
                <el-button type="primary" size="small" icon="el-icon-plus" @click="$navigate({r: 'netb/user/edit'})">添加用户</el-button>
            </div>
        </div>
        <div class="table-body">
<!--            <el-alert-->
<!--                    style="margin-bottom:20px;"-->
<!--                    type="info"-->
<!--                    title="用户端入口链接："-->
<!--                    :closable="false">-->
<!--                <template>-->
<!--                    <span>--><?php //=$url ?><!--</span>-->
<!--                    <el-button size="mini" @click="copy">复制链接</el-button>-->
<!--                </template>-->
<!--            </el-alert>-->
            <div class="input-item">
                <el-input @keyup.enter.native="search" size="small" placeholder="请输入关键词" v-model="searchData.keyword" clearable @clear="search">
                    <el-select v-model="searchData.field" slot="prepend" placeholder="请选择" style="width: 90px;">
                        <el-option label="uid" value="uid"></el-option>
                        <el-option label="昵称" value="nickname"></el-option>
                        <el-option label="手机号" value="mobile"></el-option>
                        <el-option label="邮箱" value="email"></el-option>
                    </el-select>
                    <el-button slot="append" icon="el-icon-search" @click="search"></el-button>
                </el-input>
            </div>
            <div class="input-item">
                <el-select v-model="searchData.status" size="small" slot="prepend" placeholder="请选择状态" @change="search" clearable style="width: 120px;">
                    <el-option label="正常" value="0"></el-option>
                    <el-option label="禁用" value="1"></el-option>
                </el-select>
            </div>
            <el-table
                    ref="multipleTable"
                    class="table-info"
                    :data="form"
                    border
                    style="width: 100%"
                    v-loading="listLoading"
                    @sort-change="changeSort">
                <el-table-column
                        type="selection"
                        width="55">
                </el-table-column>
                <el-table-column prop="uid" label="UID" min-width="110" sortable>
                    <template slot-scope="scope">
                        <div>{{scope.row.uid}}</div>
                        <div v-if="scope.row.is_blacklist == 1" style="color: #f56c6c; font-size: 12px; margin-top: 5px;text-align: center;">禁用</div>
                        <div style="color: #999999; font-size: 12px; text-align: center;">
                            <span>{{scope.row.remark || '--'}}</span>
                            <i class="bi bi-pencil-fill" style="font-size: 12px; cursor: pointer;" @click="editRemarkInUid(scope.row)"></i>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="头像昵称" width="230">
                    <template slot-scope="scope">
                        <div>
                            <div flex="dir:left cross:center">
                                <app-image mode="aspectFill" style="margin-right: 8px;flex-shrink: 0" :src="scope.row.avatar"></app-image>
                                <div style="width: 100%;">
                                    <div>{{scope.row.nickname}}</div>
                                    <div>推荐人: {{scope.row.parent_nickname}}</div>
                                </div>
                            </div>
                        </div>
                    </template>
                </el-table-column>
<!--                <el-table-column label="用户权限" min-width="150">-->
<!--                    <template slot-scope="scope">-->
<!--                        <div>保存图片：{{scope.row.set_data.del_img_power == '1' ? '开启' : '关闭'}}</div>-->
<!--                        <div>保存视频：{{scope.row.set_data.del_video_power == '1' ? '开启' : '关闭'}}</div>-->
<!--                        <div>资源空间：{{scope.row.set_data.attachment_size}}MB</div>-->
<!--                    </template>-->
<!--                </el-table-column>-->
                <el-table-column label="邮箱" min-width="160">
                    <template slot-scope="scope">
                        <div>{{scope.row.email}}</div>
                    </template>
                </el-table-column>
                <el-table-column label="等级" min-width="180">
                    <template slot-scope="scope">
                        <div>用户等级：{{scope.row.level}}</div>
                        <div>会员等级：{{scope.row.member_level ?? '--'}}</div>
                    </template>
                </el-table-column>
                <el-table-column label="消费统计" min-width="160">
                    <template slot-scope="scope">
                        <div>
                            <el-button type="text" @click="$navigate({r: 'netb/user/balance-log', user_id:scope.row.user_id})"
                                       v-text="'充值合计: ' + scope.row.total_balance"></el-button>
                        </div>
                        <div>
                            <el-button type="text" @click="$navigate({r: 'netb/integral/log', user_id:scope.row.user_id})"
                                       v-text="'积分消耗: ' + scope.row.pay_total_integral"></el-button>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="账户钱包" min-width="160">
                    <template slot-scope="scope">
                        <div>
                            <el-button type="text" @click="$navigate({r: 'netb/user/balance-log', user_id:scope.row.user_id})"
                                       v-text="'余额：' + scope.row.balance"></el-button>
                        </div>
                        <div>
                            <el-button type="text" @click="$navigate({r: 'netb/integral/log', user_id:scope.row.user_id})"
                                       v-text="'积分：' + scope.row.integral"></el-button>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="register_time" label="注册时间" width="160" sortable></el-table-column>
                <el-table-column label="操作" width="170" fixed="right">
                    <template slot-scope="scope">
                        <el-tooltip effect="dark" content="编辑" placement="top">
                            <el-button circle type="text" size="mini" @click="$navigate({r: 'netb/user/edit', id:scope.row.user_id, page: page})">
                                <i class="bi bi-pencil-square"></i>
                            </el-button>
                        </el-tooltip>
                        <el-tooltip effect="dark" content="删除" placement="top">
                            <el-button circle type="text" size="mini" @click="del(scope.row)">
                                <i class="bi bi-trash"></i>
                            </el-button>
                        </el-tooltip>
                        <el-tooltip effect="dark" content="编辑积分" placement="top">
                            <el-button circle type="text" size="mini" @click="handleIntegral(scope.row)">
                                <i class="bi bi-database-add"></i>
                            </el-button>
                        </el-tooltip>
                        <el-tooltip effect="dark" content="编辑余额" placement="top">
                            <el-button circle type="text" size="mini" @click="handleBalance(scope.row)">
                                <i class="bi bi-currency-exchange"></i>
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
        <!-- 编辑积分 -->
        <el-dialog title="编辑积分" :visible.sync="dialogIntegral" width="30%">
            <el-form :model="integralForm" label-width="80px" :rules="integralFormRules" ref="integralForm">
                <el-form-item label="操作" prop="type">
                    <el-radio v-model="integralForm.type" label="1">充值</el-radio>
                    <el-radio v-model="integralForm.type" label="2">扣除</el-radio>
                </el-form-item>
                <el-form-item label="积分数" prop="num" size="small">
                    <el-input oninput="this.value = this.value.replace(/[^0-9]/g, '');" v-model="integralForm.num" :max="999999999"></el-input>
                </el-form-item>
                <el-form-item label="充值图片" prop="pic_url">
                    <app-attachment :multiple="false" :max="1" @selected="integralPicUrl">
                        <el-button size="mini">选择文件</el-button>
                    </app-attachment>
                    <app-image width="80px" height="80px" mode="aspectFill" :src="integralForm.pic_url"></app-image>
                </el-form-item>
                <el-form-item label="备注" prop="remark" size="small">
                    <el-input v-model="integralForm.remark"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogIntegral = false">取消</el-button>
                <el-button :loading="btnLoading" type="primary" @click="integralSubmit">确认</el-button>
            </div>
        </el-dialog>
        <!-- 编辑余额 -->
        <el-dialog title="编辑余额" :visible.sync="dialogBalance" width="30%">
            <el-form :model="balanceForm" label-width="80px" :rules="balanceFormRules" ref="integralForm">
                <el-form-item label="操作" prop="type">
                    <el-radio v-model="balanceForm.type" label="1">充值</el-radio>
                    <el-radio v-model="balanceForm.type" label="2">扣除</el-radio>
                </el-form-item>
                <el-form-item label="金额" prop="price" size="small">
                    <el-input type="number" v-model="balanceForm.price" :max="99999999"></el-input>
                </el-form-item>
                <el-form-item label="充值图片" prop="pic_url">
                    <app-attachment :multiple="false" :max="1" @selected="balancePicUrl">
                        <el-button size="mini">选择文件</el-button>
                    </app-attachment>
                    <app-image width="80px" height="80px" mode="aspectFill" :src="balanceForm.pic_url"></app-image>
                </el-form-item>
                <el-form-item label="备注" prop="remark" size="small">
                    <el-input v-model="balanceForm.remark"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogBalance = false">取消</el-button>
                <el-button :loading="btnLoading" type="primary" @click="balanceSubmit">确认</el-button>
            </div>
        </el-dialog>
        
        <!-- 备注编辑对话框 -->
        <el-dialog title="编辑备注" :visible.sync="remarkDialogVisible" width="30%">
            <el-form :model="remarkForm">
                <el-form-item label="备注信息">
                    <el-input type="textarea" :rows="4" placeholder="请输入备注信息" v-model="remarkForm.remark"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="remarkDialogVisible = false">取 消</el-button>
                <el-button type="primary" @click="updateRemark">确 定</el-button>
            </div>
        </el-dialog>
    </el-card>
</div>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                searchData: {
                    keyword: '',
                    field: 'uid'
                },
                form: [],
                pageCount: 0,
                pageSize: 10,
                page: 1,
                currentPage: null,
                listLoading: false,
                btnLoading: false,
                
                // 备注编辑相关
                remarkDialogVisible: false,
                currentEditUser: null,
                remarkForm: {
                    remark: ''
                },

                //积分
                dialogIntegral: false,
                integralForm: {
                    type: '1',
                    num: '',
                    pic_url: '',
                    remark: '',
                },
                integralFormRules: {
                    type: [
                        {required: true, message: '操作不能为空', trigger: 'blur'},
                    ],
                    num: [
                        {required: true, message: '积分数不能为空', trigger: 'blur'},
                    ],
                },

                //余额
                dialogBalance: false,
                balanceForm: {
                    type: '1',
                    price: '',
                    pic_url: '',
                    remark: '',
                },
                balanceFormRules: {
                    type: [
                        {required: true, message: '操作不能为空', trigger: 'blur'},
                    ],
                    num: [
                        {required: true, message: '金额不能为空', trigger: 'blur'},
                    ],
                },
            };
        },
        methods: {
            copy() {
                navigator.clipboard.writeText('<?=$url ?>')
                    .then(() => this.$message.success('复制成功'))
                    .catch(() => this.$message.error('复制失败'))
            },
            
            // 在UID栏目中编辑备注
            editRemarkInUid(row) {
                this.currentEditUser = row;
                this.remarkForm.remark = row.remark || '';
                this.remarkDialogVisible = true;
            },
            
            // 更新备注
            updateRemark() {
                let self = this;
                request({
                    params: {
                        r: 'netb/user/edit-remark',
                    },
                    method: 'post',
                    data: {
                        id: self.currentEditUser.user_id,
                        remark: self.remarkForm.remark
                    }
                }).then(e => {
                    if (e.data.code === 0) {
                        self.$message.success(e.data.msg);
                        // 更新列表中的备注信息
                        self.form.forEach(item => {
                            if (item.user_id === self.currentEditUser.user_id) {
                                item.remark = self.remarkForm.remark;
                            }
                        });
                        self.remarkDialogVisible = false;
                    } else {
                        self.$message.error(e.data.msg);
                    }
                }).catch(e => {
                    console.log(e);
                });
            },
            //积分
            integralPicUrl(e) {
                if (e.length) {
                    this.integralForm.pic_url = e[0].url;
                }
            },
            handleIntegral(row) {
                this.integralForm = Object.assign(this.integralForm, {user_id: row.user_id});
                this.dialogIntegral = true;
            },
            integralSubmit() {
                this.$refs.integralForm.validate((valid) => {
                    if (valid) {
                        let para = Object.assign({}, this.integralForm);
                        this.btnLoading = true;
                        this.dialogIntegral = false;
                        request({
                            params: {
                                r: 'netb/user/integral',
                            },
                            method: 'post',
                            data: para,
                        }).then(e => {
                            if (e.data.code === 0) {
                                location.reload();
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
            del(row) {
                this.$confirm('删除该用户, 是否继续?', '提示').then(() => {
                    request({
                        params: {
                            r: 'netb/user/delete',
                        },
                        method: 'post',
                        data: {id: row.user_id},
                    }).then(e => {
                        if (e.data.code === 0) {
                            this.getList();
                        } else {
                            this.$message.error(e.data.msg);
                        }
                        this.btnLoading = false;
                    }).catch(e => {
                        this.btnLoading = false;
                    });
                })
            },

            //余额
            balancePicUrl(e) {
                if (e.length) {
                    this.balanceForm.pic_url = e[0].url;
                }
            },
            handleBalance(row) {
                this.balanceForm = Object.assign(this.balanceForm, {user_id: row.user_id});
                this.dialogBalance = true;
            },
            balanceSubmit() {
                this.$refs.integralForm.validate((valid) => {
                    if (valid) {
                        let para = Object.assign({}, this.balanceForm);
                        this.btnLoading = true;
                        this.dialogBalance = false;
                        request({
                            params: {
                                r: 'netb/user/balance',
                            },
                            method: 'post',
                            data: para,
                        }).then(e => {
                            if (e.data.code === 0) {
                                location.reload();
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
            search() {
                this.page = 1;
                this.getList();
            },
            pagination(currentPage) {
                this.page = currentPage;
                this.getList();
            },
            getList() {
                this.listLoading = true;
                request({
                    params: {
                        r: 'netb/user/index',
                        page: this.page,
                        keyword: this.searchData.keyword,
                        field: this.searchData.field,
                        status: this.searchData.status,
                        sort: this.searchData.order,
                    },
                }).then(e => {
                    if (e.data.code === 0) {
                        this.form = e.data.data.list;
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
            changeSort(column) {
                this.loading = true;
                if(column.order == "descending") {
                    this.searchData.order = column.prop + '_desc'
                }else if (column.order == "ascending") {
                    this.searchData.order = column.prop + '_asc'
                }else {
                    this.searchData.order = null
                }
                this.getList();
            },
        },
        mounted: function () {
            this.page = getQuery('page') ? getQuery('page') : 1;
            this.getList();
        }
    });
</script>
