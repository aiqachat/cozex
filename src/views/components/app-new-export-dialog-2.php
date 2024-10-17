<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: chenzs
 */
$url = Yii::$app->urlManager->createUrl(Yii::$app->controller->route);
?>
<style>
    .app-new-export-dialog-2 .el-dialog__body .el-checkbox + .el-checkbox {
        margin-left: 0;
    }

    .app-new-export-dialog-2 .el-date-editor .el-range-separator {
        padding: 0;
    }

    .app-new-export-dialog-2 .modal-body {
        border: 1px solid #F0F2F7;
    }

    .app-new-export-dialog-2 .all-choose {
        height: 50px;
        line-height: 50px;
        background-color: #F3F5F6;
        width: 100%;
        padding-left: 20px;
    }

    .app-new-export-dialog-2 .choose-list {
        padding: 10px 25px 20px;
    }

    .app-new-export-dialog-2 .choose-list .export-checkbox {
        width: 135px;
        height: 30px;
        line-height: 30px;
    }
    .app-new-export-dialog-2 .dialog-box .el-dialog {
        min-width: 300px;
    }

    .app-new-export-dialog-2 .image-dialog {
        width: 294px;
        height: 212px;
        top: calc(50% - 212px / 2);
        left: calc(50% - 294px / 2);
        background-image: url('statics/img/mall/export-loading.gif');
        position: fixed;
        z-index: 99;
        background-size: cover;
        visibility: hidden;
    }

    .app-new-export-dialog-2 .start-animation {
        visibility: visible;
        animation:myfirst 1.5s ease;
        animation-delay:1.5s;
        animation-fill-mode:forwards;
    }

    /*@keyframes myfirst
    {
        0%   {}
        25%  {}
        50%  {border-radius: 25%;}
        75%  {
            border-radius: 100%;
            width: 17px;
            height: 17px;
            top: calc(50% - 8.5px / 2);
            left: calc(50% - 8.5px / 2);
            background-image: url('statics/img/mall/download-2.png');
        }
        99.9% {
            width: 17px;
            height: 17px;
            top: 22.875px;
            left: 1054px;
            background-image: url('statics/img/mall/download-2.png');
        }
        100% {
            visibility: hidden;
        }
    }*/
</style>
<template id="app-new-export-dialog-2">
    <div class="app-new-export-dialog-2" style="display: inline-block">
        <el-button :loading="loading" @click="confirm" type="primary" size="small">{{text}}</el-button>
        <el-dialog
                title="选择导出信息"
                :visible.sync="dialogVisible"
                width="50%">
            <el-form>
                <div class="modal-body">
                    <el-checkbox class="all-choose" :indeterminate="isIndeterminate" v-model="checkAll"
                                 @change="exportCheckAll">全选
                    </el-checkbox>
                    <el-checkbox-group class="choose-list" v-model="checkedFields" @change="exportCheck">
                        <el-checkbox class="export-checkbox" v-for="item in field_list" :key="item.key"
                                     :label="item.key">{{item.value}}
                        </el-checkbox>
                    </el-checkbox-group>
                </div>
                <div flex="dir:right" style="margin-top: 20px;">
                    <el-button :loading="loading" size="small" type="primary" @click="submitRequest">导出</el-button>
                </div>
            </el-form>
        </el-dialog>

        <div class='image-dialog' :class="{'start-animation': isStart}" ref="image_dialog"></div>
    </div>
</template>
<script>
    Vue.component('app-new-export-dialog-2', {
        template: '#app-new-export-dialog-2',
        props: {
            text: {
                type: String,
                default: '批量导出'
            },
            directly: {
                type: Boolean,
                default: false
            },
            field_list: Array,
            params: Object,
            action_url: {
                type: String,
                default: '<?=$url?>'
            },//跳转路由
            is_next: {
                type: Boolean,
                default: true
            }
        },
        data() {
            return {
                dialogVisible: false,
                isIndeterminate: false,
                checkAll: false,
                checkedFields: [],

                loading: false,
                isStart: false
            }
        },
        computed: {},
        methods: {
            exportCheckAll(val) {
                if (val) {
                    let field_list = this.field_list;
                    let array = [];
                    field_list.forEach((item, index) => {
                        array.push(item['key']);
                    });
                    this.checkedFields = array;
                } else {
                    this.checkedFields = [];
                }
                this.isIndeterminate = false;
            },
            exportCheck(value) {
                let checkedCount = value.length;
                this.checkAll = checkedCount === this.field_list.length;
                this.isIndeterminate = checkedCount > 0 && checkedCount < this.field_list.length;
            },
            confirm() {
                // 点击导出时可通过此方法添加或修改参数
                this.$emit('selected');

                // 控制弹框显示  为了自定义验证
                if (!this.is_next) {
                    return false
                }

                if(this.directly) {
                    this.submitRequest();
                }else {
                    this.dialogVisible = true;
                }
                
            },
            submitRequest() {
                let self = this;
                self.loading = true;
                let data = [];
                if (self.params) {
                    data = JSON.parse(JSON.stringify(self.params));
                } else {
                    data = [];
                }
                data.fields = self.checkedFields;
                data.flag = 'EXPORT';
                
                request({
                    params: {
                        r: self.action_url
                    },
                    data: data,
                    method: 'post'
                }).then(e => {
                    // 导出完成事件
                    self.$emit('finish');

                    self.loading = false;
                    if (e.data.code == 0) {
                        self.dialogVisible = false;
                        self.isStart = true;

                        setTimeout(function() {
                            self.isStart = false
                        }, 3000);
                    } else {
                     self.$message.error(e.data.msg)
                    }
                }).catch(e => {
                });
            }
        },
        created() {
            console.log(self.action_url)
        },
        mounted() {
            setTimeout(function() {
                let dwonload_params = localStorage.getItem('_SYSTEM_DOWNLOAD_PARAMS');
                dwonload_params = JSON.parse(dwonload_params);

                let left = dwonload_params.left;
                let top = dwonload_params.top;

                if (dwonload_params.width == 0) {
                    top = top - 8.5;
                }

                if (dwonload_params.height == 0) {
                    left = left - 17;
                }

                console.log(dwonload_params)

                var style = document.styleSheets[4];
                style.insertRule("@keyframes myfirst{25% { border-radius: 100%; width: 17px; height: 17px; top: calc(50% - 8.5px / 2); left: calc(50% - 8.5px / 2); background-image: url('statics/img/mall/download-2.png'); } 100% { width: 17px; height: 17px; top: "+top+"px; left: "+left+"px; background-image: url('statics/img/mall/download-2.png'); visibility: hidden;}");//写入样式
            }, 500);
        }
    });
</script>
