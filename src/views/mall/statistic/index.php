<?php defined('YII_ENV') or exit('Access Denied'); ?>
<style>
    .num-info {
        display: flex;
        width: 100%;
        font-size: 20px;
    }

    .num-info .num-info-item {
        text-align: center;
        flex-grow:  1;
        background-color: #F8F9FA;
        margin: 0 10px;
        padding: 20px 0;
    }

    .info-item-name {
        font-size: 15px;
        margin-top: 10px;
        color: #92959B;
    }

    .el-card {
        margin-top: 10px;
    }
</style>
<div id="app" v-cloak>
    <el-card v-loading="loading" shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <span>系统概况</span>
        </div>
        <div class="table-area">
            <el-card shadow="never">
                <div slot="header">
                    <i class="el-icon-s-platform"></i>
                    <span>基本信息</span>
                </div>
                <div class="num-info">
                    <div class="num-info-item" v-for="item in list">
                        <div>{{item.name}}</div>
                        <div class="info-item-name">{{item.value}}</div>
                    </div>
                </div>
            </el-card>
            <el-card shadow="never">
                <div slot="header">
                    <i class="el-icon-platform-eleme"></i>
                    <span>版本授权</span>
                </div>
                <div class="num-info">
                    <div class="num-info-item">
                        开源版本完全免费开源使用
                    </div>
                </div>
            </el-card>
            <el-card shadow="never">
                <div slot="header">
                    <i class="el-icon-menu"></i>
                    <span>软件版本</span>
                </div>
                <div class="num-info">
                    <div class="num-info-item">
                        {{version}}
                    </div>
                </div>
            </el-card>
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
                version: '',
            };
        },
        methods: {
            // 获取数据
        	getList() {
        		this.loading = true;
                request({
                    params: {
                        r: 'mall/statistic/index',
                    },
                    method: 'get',
                }).then(e => {
        			this.loading = false;
                    if (e.data.code === 0) {
                        this.list = Object.assign({}, this.list, e.data.data.info);
                        this.version = e.data.data.version
                    } else {
                        this.$message.error(e.data.msg);
                    }
                }).catch(e => {
        			this.loading = false;
                });        		
        	},
        },
        created() {
        	this.getList();
        }
    })
</script>