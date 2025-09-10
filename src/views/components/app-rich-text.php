<?php
/**
 * Created by IntelliJ IDEA.
 * author: chenzs
 * Date: 2019/3/16
 * Time: 10:16
 */
?>
<style>
    .app-rich-text {
        line-height: normal;
    }

    .app-rich-text textarea,
    .app-rich-text .edui-editor {
        width: 100% !important;
    }
</style>
<template id="app-rich-text">
    <div class="app-rich-text">
        <textarea style="width: 100%" :id="id"></textarea>
        <app-attachment style="height: 0"
                        :simple="simpleAttachment"
                        :open-dialog="attachmentDialogVisible"
                        :multiple="!simpleAttachment"
                        @closed="attachmentClosed"
                        @selected="attachmentSelected">
        </app-attachment>
    </div>
</template>
<script src="<?= Yii::$app->request->baseUrl ?>/statics/ueditor/ueditor.config.js"></script>
<script src="<?= Yii::$app->request->baseUrl ?>/statics/ueditor/ueditor.all.js"></script>
<script>
    Vue.component('app-rich-text', {
        template: '#app-rich-text',
        props: {
            value: null,
            simpleAttachment: false,
            labelIcon: {
                type: String,
                default: '插入图片',
            },
            isShowInsertImage: {
                type: Boolean,
                default: true
            },
            isDark: {
                type: Boolean,
                default: true
            },
        },
        data() {
            return {
                attachmentDialogVisible: false,
                id: 'app-rich-text-' + (Math.floor((Math.random() * 10000) + 1)),
                ue: null,
                tempContent: this.value,
                isInputChange: false,
            };
        },
        watch: {
            value(newVal, oldVal) {
                if (!this.isInputChange && newVal) {
                    if (this.ue) {
                        if (this.ue.isReady !== 1) {
                            let self = this;
                            let time = setInterval(() => {
                                //循环查
                                if (self.ue.isReady === 1) {
                                    clearInterval(time);
                                    self.ue.setContent(newVal);
                                    self.isInputChange = false;
                                }
                            }, 100);
                            return;
                        }
                        this.ue.setContent(newVal);
                    } else {
                        this.tempContent = newVal;
                    }
                }
                this.isInputChange = false;
            },
        },
        mounted() {
            this.loadUe();
        },
        methods: {
            attachmentClosed() {
                this.attachmentDialogVisible = false;
            },
            attachmentSelected(e) {
                if (e.length) {
                    let html = '';
                    for (let i in e) {
                        html += '<img src="' + e[i].url + '" style="max-width: 100%;">';
                    }
                    this.ue.execCommand('inserthtml', html);
                }
            },
            loadUe() {
                let self = this;
                let init = {
                    autoHeightEnabled: false // 禁用自动高度
                };
                if(self.isDark){
                    init.initialStyle = 'body{background-color:#333333;color:#ffffff}' // 设置内容区背景颜色
                }
                this.ue = UE.getEditor(this.id, init);
                this.ue.addListener('ready', editor => {
                    if (this.tempContent) {
                        this.ue.setContent(this.tempContent);
                    }
                });
                this.ue.addListener('keyup', editor => {
                    this.isInputChange = true;
                    this.$emit('input', this.ue.getContent());
                });
                this.ue.addListener('contentChange', editor => {
                    this.isInputChange = true;
                    this.$emit('input', this.ue.getContent());
                });
                if (self.isShowInsertImage) {
                    UE.registerUI('appinsertimage', (editor, uiName) => {
                        return new UE.ui.Button({
                            name: uiName,
                            title: self.labelIcon,
                            //添加额外样式，指定icon图标，这里默认使用一个重复的icon
                            cssRules: 'background-position: -381px 0px;',
                            onclick() {
                                self.ue = editor
                                self.attachmentDialogVisible = true;
                            },
                        });
                    });
                }
            }
        },
    });
</script>

