<?php
/**
 * author: chenzs
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 */
?>
<style scoped>
    .app-attachment-simple-upload {
        width: 100% !important;
        height: 150px;
        border: 1px dashed #e3e3e3;
        cursor: pointer;
        margin-top: 10px;
    }

    .app-attachment-simple-upload:hover, .hover {
        background: rgba(0, 0, 0, .05);
    }

    .app-attachment-simple-upload i {
        font-size: 32px;
    }
</style>
<template id="app-attachment-dragging">
    <app-upload
            class="app-attachment-simple-upload"
            :class="{'hover': isDragging}"
            v-loading="uploading"
            :disabled="uploading"
            @start="handleStart"
            @dragging="dragging"
            @complete="handleComplete"
            @success="handleSuccess"
            :multiple="true"
            :max="max"
            :params="params"
            :fields="fields"
            :accept="accept"
            :is-dragging="1"
            flex="main:center cross:center">
        <i class="el-icon-upload" style="margin-top: -40px;"></i>
        <div style="position: absolute;" v-if="!isDragging">点击上传或拖拽文件到这里</div>
        <div style="position: absolute;" v-else>松手开始上传</div>
        <div style="position: absolute; margin-top: 50px; color: #CCCCCC;" v-if="notice">
            {{notice}}
        </div>
    </app-upload>
</template>
<script>
    Vue.component('app-attachment-dragging', {
        template: '#app-attachment-dragging',
        props: {
            params: Object,
            fields: Object,
            max: {
                type: Number,
                default: 1,
            },
            notice: {
                type: String,
                default: '',
            },
            type: {
                type: Number,
                default: 0,
            },
        },
        computed: {
            accept: {
                get() {
                    if (this.type === 0) {
                        return 'text/plain,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword,.md';
                    }
                    if (this.type === 1) {
                        return 'text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                    }
                    if (this.type === 2) {
                        return 'image/png,image/jpeg,image/jpeg';
                    }
                    return '*/*';
                },
            },
        },
        data() {
            return {
                uploading: false,
                isDragging: 0,
            };
        },
        created() {},
        methods: {
            dragging(res){
                this.isDragging = res;
            },
            handleStart(files) {
                this.uploading = true;
                this.$emit('start', files);
            },
            handleComplete(files) {
                this.uploading = false;
                let urls = [];
                let attachments = [];
                for (let i in files) {
                    if (files[i].response.data && files[i].response.data.code === 0) {
                        urls.push(files[i].response.data.data.url);
                        attachments.push(files[i].response.data.data);
                    }
                }
                if (!urls.length) {
                    return;
                }
                console.log('handleComplete')
                console.log(attachments)
                console.log(urls)
            },
            handleSuccess(files) {
                this.$emit('success', files);
            },
        },
    });
</script>
