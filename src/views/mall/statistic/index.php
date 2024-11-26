<?php defined('YII_ENV') or exit('Access Denied');
$indSetting = (new \app\forms\mall\setting\ConfigForm())->config();
?>
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

    .version-item::after {
        content: " ";
        display: block;
        border-bottom: 1px dashed #c9c9c9;
        margin: 10px 0;
    }

    .version-list {
        max-height: 300px;
        overflow-y: auto;
        white-space: pre-wrap;
        margin-left: 10px;
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
                    <i class="el-icon-s-data"></i>
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
                        <?php echo $indSetting['version_text'] ?? '';?>
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
                        <span style="color: #9C9FA4;">当前版本：</span>{{version}}
                    </div>
                    <div class="num-info-item">
                        <span style="color: #9C9FA4;">下一个版本：</span>
                        <span v-if="version_list.next_version">
                            V{{version_list.next_version.version_number}}
                            <el-button v-loading="butLoading" type="text" size="mini" @click="update">立即更新</el-button>
                        </span>
                        <span v-else>暂无新版本</span>
                    </div>
                </div>
                <div class="version-list" v-if="version_list.list && version_list.list.length > 0">
                    <div style="color: #9C9FA4;font-size: 20px;">历史版本记录</div>
                    <div v-for="item in version_list.list" class="version-item">
                        <div style="margin-left: 40px;">
                            <div>版本号: {{item.version_number}}</div>
                            <div v-html="item.content"></div>
                        </div>
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
                butLoading: false,
                list: [],
                version: '',
                version_list: {},
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
                        this.version_list = e.data.data.version_list
                    } else {
                        this.$message.error(e.data.msg);
                    }
                }).catch(e => {
        			this.loading = false;
                });
        	},
            update() {
                this.$confirm("确认更新到版本 v" + this.version_list.next_version.version_number + ' ?', "警告", {
                    type: 'warning',
                }).then(() => {
                    this.doUpdate();
                }).catch(() => {
                    location.reload();
                });
        	},
            doUpdate() {
        		this.butLoading = true;
                request({
                    params: {
                        r: 'mall/update/index',
                    },
                    method: 'post',
                    data: {_csrf: this._csrf},
                }).then(e => {
        			this.butLoading = false;
                    if (e.data.code === 0) {
                        if(e.data.data.reply === 1){
                            this.doUpdate();
                        }else {
                            this.$alert(e.data.msg, "提示").then(() => {
                                location.reload();
                            }).catch(() => {
                                location.reload();
                            });
                        }
                    } else {
                        this.$message.error(e.data.msg);
                    }
                }).catch(e => {
        			this.butLoading = false;
                });
        	},
        },
        created() {
        	this.getList();
        }
    })
</script>