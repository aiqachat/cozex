<?php
/**
 * author: chenzs
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/12/28 18:06
 */
?>
<template id="app-upload">
    <div class="app-upload" @click="handleClick" @dragover.prevent="handleDragOver" @drop="handleDrop" @dragenter.prevent @dragleave.prevent="handleDragLeave">
        <slot></slot>
        <input ref="input" type="file" :accept="accept" :multiple="multiple" style="display: none"
               @change="handleChange">
    </div>
</template>
<script>
    Vue.component('app-upload', {
        template: '#app-upload',
        props: {
            disabled: Boolean,
            multiple: Boolean,
            max: Number,
            size: Number,
            accept: String,
            params: Object,
            fields: Object,
            isDragging: Number,
        },
        data() {
            return {
                dialogVisible: false,
                loading: true,
                attachments: [],
                checkedAttachments: [],
                files: [],
            };
        },
        created() {
        },
        methods: {
            handleDragOver(event) {
                if(!this.isDragging){
                    return;
                }
                this.$emit('dragging', 1);
            },
            handleDragLeave(event) {
                this.$emit('dragging', 0);
            },
            handleDrop(event) {
                if(!this.isDragging){
                    return;
                }
                event.stopPropagation();
                event.preventDefault();
                this.$emit('dragging', 0);
                this.files = Array.from(event.dataTransfer.files);
                this.uploadFiles(this.files);
            },
            handleClick() {
                if (this.disabled) {
                    return;
                }
                this.$refs.input.value = null;
                this.$refs.input.click();
            },
            handleChange(e) {
                if (!e.target.files) return;
                this.uploadFiles(e.target.files);
            },
            uploadFiles(rawFiles) {
                if (this.max && rawFiles.length > this.max) {
                    this.$message.error('最多一次只能上传' + this.max + '个文件。')
                    return;
                }
                this.files = [];
                for (let i = 0; i < rawFiles.length; i++) {
                    if(this.isDragging) {
                        let fileType = rawFiles[i].type.toLowerCase();
                        console.log(fileType)
                        if(!fileType){
                            const parts = rawFiles[i].name.split('.');
                            fileType = parts[parts.length - 1].toLowerCase();
                        }   console.log(fileType)
                        console.log(this.accept)
                        if (this.accept !== '*/*' && !this.accept.includes(fileType)) {
                            this.$message.error('文件类型不正确，请重新上传')
                            return;
                        }
                    }
                    if(this.size && rawFiles[i].size > this.size){
                        this.$message.error('文件大小超过限制')
                        return;
                    }
                    const file = {
                        _complete: false,
                        response: null,
                        rawFile: rawFiles[i],
                    };
                    this.files.push(file);
                }
                this.$emit('start', this.files);
                for (let i in this.files) {
                    this.upload(this.files[i]);
                }
            },
            upload(file) {
                let formData = new FormData();
                const params = {};
                params['r'] = 'common/attachment/upload';
                for (let i in this.params) {
                    params[i] = this.params[i];
                }
                for (let i in this.fields) {
                    formData.append(i, this.fields[i]);
                }
                formData.append('file', file.rawFile, file.rawFile.name);
                this.$request({
                    headers: {'Content-Type': 'multipart/form-data'},
                    params: params,
                    method: 'post',
                    data: formData,
                }).then(e => {
                    if (e.data.code === 1) {
                        this.$message.error(e.data.msg);
                        return;
                    }
                    file.response = e;
                    file._complete = true;
                    this.onSuccess(file);
                }).catch(e => {
                    file._complete = true;
                });
            },
            onSuccess(file) {
                this.$emit('success', file);
                let allComplete = true;
                for (let i in this.files) {
                    if (!this.files[i]._complete) {
                        allComplete = false;
                        break;
                    }
                }
                if (allComplete) {
                    this.$emit('complete', this.files);
                }
            },
        },
    });
</script>