<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: chenzs
 */
Yii::$app->loadViewComponent('app-volcengine-choose');

use app\forms\mall\volcengine\SpeechForm;

$voiceType = (new SpeechForm())->voiceType(SpeechForm::TYPE_TTS_1);
?>
<style type="text/css">
    @import "<?= \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/css/table.css' ?>";
</style>
<style>
    .table-body {
        padding: 10px;
        background-color: #fff;
    }

    .model-list {
        flex-wrap: wrap;
        margin: 10px 0 10px -16px;
        height: 240px;
        overflow: auto;
        overflow-x: hidden;
    }

    .model-item {
        background: #fff;
        border: 1px solid #ebebeb;
        width: 306px;
        height: 112px;
        overflow: hidden;
        margin: 0 0 16px 16px;
        padding: 16px;
        cursor: pointer;
        position: relative;
    }

    .model-icon-bg {
        display: inline-block;
        margin-right: 16px;
    }

    .model-icon {
        width: 80px;
        height: 80px;
    }

    .model-name, .model-desc {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .model-name {
        font-size: 14px;
        margin-bottom: 4px;
    }

    .model-desc {
        color: #999999;
        font-size: 12px;
    }

    .model-btn {
        color: #545454;
        background: #f2f6fc;
        border: none;
    }

    .search-input .el-input__inner {
        border-radius: 4px 0 0 4px;
    }

    .search-btn {
        border-radius: 0 4px 4px 0;
    }

    .choose {
        border-color: #734CF8;
        background-color: #F1EFFD;
    }

    .tags {
        background: #E4F0FF;
    }

    .tags .el-radio-group {
        margin: 5px;
    }

    .tags .el-radio-button {
        margin: 5px;
    }
</style>
<div id="app" v-cloak>
    <app-volcengine-choose @account="changeAccount" :dialog="dialog" @close="closeDialog"></app-volcengine-choose>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <span style="font-weight: bold;font-size: 16px;">大模型语音合成</span>
            <div style="color: #a4a4a4;margin-top: 10px;">
                依托新一代大模型能力，火山语音模型能够根据上下文，智能预测文本的情绪、语调等信息。并生成超自然、高保真、个性化的语音，以满足不同用户的个性化需求。相较于传统语音合成技术，语音大模型能输出在自然度、音质、韵律、气口、情感、语气词表达等方面更像真人。
            </div>
        </div>
        <div class="table-body">
            <div flex="box:last" style="margin-bottom: 5px;">
                <div>选择音色</div>
                <div flex class="search-group">
                    <el-input style="width: 250px" size="small"
                              class="search-input"
                              placeholder="请输入想搜索的音色"
                              v-model="searchData.name"
                              clearable
                              @clear="change"
                              @keyup.enter.native="change"></el-input>
                    <el-button class="search-btn" type="primary" size="small" @click="change">搜索</el-button>
                </div>
            </div>
            <div class="tags">
                <el-radio-group v-model="searchData.tag" size="mini" @change="change">
                    <el-radio-button label="">全部场景</el-radio-button>
                    <el-radio-button v-for="item in options" :label="item.id">{{item.name}}</el-radio-button>
                </el-radio-group>
                <span style="border-left: 2px solid #d2d2d2;margin-right: 10px"></span>
                <el-radio-group v-model="searchData.sex" size="mini" @change="change">
                    <el-radio-button label="">全部性别</el-radio-button>
                    <el-radio-button label="2">女声</el-radio-button>
                    <el-radio-button label="1">男声</el-radio-button>
                </el-radio-group>
                <span style="border-left: 2px solid #d2d2d2;margin-right: 10px"></span>
                <el-radio-group v-model="searchData.age" size="mini" @change="change">
                    <el-radio-button label="">全部年龄</el-radio-button>
                    <el-radio-button label="1">青年</el-radio-button>
                    <el-radio-button label="2">少年/少女</el-radio-button>
                    <el-radio-button label="3">中年</el-radio-button>
                    <el-radio-button label="4">老年</el-radio-button>
                </el-radio-group>
            </div>
        </div>
        <div class="model-list" flex="dir:left">
            <div class="model-item" flex="dir:left box:first" @click.stop="choose(item)"
                 v-for="item in voices" :class="{'choose': data.data.voice_type == item.id}">
                <div style="z-index: 1">
                    <div class="model-icon-bg">
                        <img class="model-icon" :src="item.pic">
                    </div>
                </div>
                <div flex="dir:top box:last" style="z-index: 1">
                    <div>
                        <div class="model-name">{{item.name}}</div>
                        <div class="model-desc">{{item.desc}}</div>
                    </div>
                    <div style="text-align: right;">
                        <template v-if="item.audition">
                            <audio :ref="'audio' + item.id" >
                                <source :src="item.audition" type="audio/mpeg">
                            </audio>
                            <el-tooltip effect="dark" content="点击试听音色" placement="top">
                                <el-button @click.stop="playMusic(item)"
                                           class="model-btn" size="mini" type="info" round>试听</el-button>
                            </el-tooltip>
                        </template>
                    </div>
                </div>
            </div>
        </div>
        <el-form :model="data" label-width="80px" :rules="rules" ref="data" v-loading="formLoading">
            <el-form-item label="当前选择" prop="data.voice_type">
                <div flex="cross:center">
                    <template v-if="data.data.voice_type">
                        <img :src="rsd.pic" style="width: 40px;height: 40px;"/>
                        <span style="margin-left: 10px">{{rsd.name}}</span>
                    </template>
                </div>
            </el-form-item>
            <el-form-item label="输入文本" prop="text">
                <el-input size="small" type="textarea" v-model.trim="data.text" rows="10" maxlength="20000" show-word-limit></el-input>
            </el-form-item>
            <div flex="main:center">
                <el-button :loading="btnLoading" size="small" type="primary" @click="submit">立即合成</el-button>
                <el-upload action="" accept="text/plain" :show-file-list="false" :on-progress="handleProgress">
                    <el-button style="margin-left: 10px;" size="small" type="success">上传文本</el-button>
                </el-upload>
            </div>
        </el-form>
        <div class="table-body" style="margin-top: 10px;">
            <div flex="box:last cross:center" style="margin-bottom: 5px;">
                <div>最近合成记录列表</div>
                <el-button type="primary" size="small" @click="$navigate({r:'mall/volcengine/tts'})">更多</el-button>
            </div>
            <el-table :data="form" border style="width: 100%" v-loading="listLoading">
                <el-table-column type="selection" width="55"></el-table-column>
                <el-table-column prop="text" label="文本">
                    <template slot-scope="scope">
                        <span style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">
                            {{scope.row.text}}
                        </span>
                    </template>
                </el-table-column>
                <el-table-column label="状态" width="90">
                    <template slot-scope="scope">
                        <span v-if="scope.row.status == 1">处理中</span>
                        <span v-if="scope.row.status == 2">成功</span>
                        <span v-if="scope.row.status == 3"
                              @mouseenter="scope.row.showPopover = true"
                              @mouseleave="scope.row.showPopover = false">失败</span>
                        <el-popover v-model="scope.row.showPopover">
                            {{scope.row.err_msg}}
                        </el-popover>
                    </template>
                </el-table-column>
                <el-table-column prop="created_at" label="创建时间" width="180" sortable="false"></el-table-column>
                <el-table-column label="操作" width="220" fixed="right">
                    <template slot-scope="scope">
                        <audio :ref="'audio' + scope.row.id" >
                            <source :src="scope.row.result" type="audio/mpeg">
                        </audio>
                        <el-tooltip class="item" effect="dark" content="播放" placement="top" v-if="scope.row.status == 2">
                            <el-button circle type="text" size="mini" @click="playMusic(scope.row)">
                                <img src="statics/img/mall/music.png" alt="">
                            </el-button>
                        </el-tooltip>
                        <el-tooltip class="item" effect="dark" content="下载" placement="top" v-if="scope.row.status == 2">
                            <el-button circle type="text" size="mini" @click="down(scope.row)">
                                <img src="statics/img/mall/download.png" alt="">
                            </el-button>
                        </el-tooltip>
                        <el-tooltip class="item" effect="dark" content="重试" placement="top" v-if="scope.row.status == 3">
                            <el-button circle type="text" size="mini" @click="refresh(scope.row)">
                                <img src="statics/img/mall/refresh.png" alt="">
                            </el-button>
                        </el-tooltip>
                        <el-tooltip class="item" effect="dark" content="删除" placement="top">
                            <el-button circle type="text" size="mini" @click="destroy(scope.row)">
                                <img src="statics/img/mall/del.png" alt="">
                            </el-button>
                        </el-tooltip>
                    </template>
                </el-table-column>
            </el-table>
        </div>
    </el-card>
</div>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                searchData: {
                    account_id: '',
                    tag: '',
                    sex: '',
                    age: '',
                },
                form: [],
                listLoading: false,
                formLoading: false,
                btnLoading: false,

                data: {
                    data: {
                        voice_type: '',
                    },
                    text: '',
                },
                rules: {
                    text: [
                        {required: true, message: '文本不能为空', trigger: 'blur'},
                    ],
                    'data.voice_type': [
                        {required: true, message: '请选择音色', trigger: 'blur'},
                    ],
                },
                options: <?= $voiceType ?>,
                voices: [],
                rsd: {},
                dialog: false,
            };
        },
        watch: {
            'searchData.account_id': function (val, oldValue){
                this.getList();
            },
        },
        methods: {
            closeDialog() {
                this.dialog = false;
            },
            handleProgress(event, file) {
                this.formLoading = true;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.data.text = e.target.result;
                    this.formLoading = false;
                };
                reader.readAsText(file.raw);
            },
            changeAccount(val){
                this.searchData.account_id = val;
            },
            choose(row) {
                this.data.data.voice_type = row.id;
                this.rsd = row;
            },
            playMusic(row) {
                let text = "audio" + row.id;
                this.$refs[text].length > 0  ? this.$refs[text][0].play() : this.$refs[text].play();
            },
            change() {
                this.voices = [];
                this.options.forEach(item => {
                    if(!this.searchData.tag || item.id === this.searchData.tag){
                        if(item.children.length > 0){
                            item.children.forEach(its => {
                                its.parent_name = item.name;
                                its.desc = '';
                                this.voices.push(its)
                            });
                        }
                    }
                });
                if(this.searchData.sex){
                    let temp = [];
                    this.voices.forEach(its => {
                        if(its.sex === this.searchData.sex){
                            temp.push(its)
                        }
                    });
                    this.voices = temp;
                }
                if(this.searchData.age){
                    let temp = [];
                    this.voices.forEach(its => {
                        if(its.age === this.searchData.age){
                            temp.push(its)
                        }
                    });
                    this.voices = temp;
                }
                if(this.searchData.name){
                    let temp = [];
                    this.voices.forEach(its => {
                        if(its.name.includes(this.searchData.name)){
                            temp.push(its)
                        }
                    });
                    this.voices = temp;
                }
                let check = false;
                this.voices.forEach(its => {
                    if(its.age === '1'){
                        its.desc = '青年';
                    }else if(its.age === '2'){
                        its.desc = '少年/少女';
                    }else if(its.age === '3'){
                        its.desc = '中年';
                    }else if(its.age === '4'){
                        its.desc = '老年';
                    }
                    if(its.sex === '1'){
                        its.desc += ' 男声';
                    }else if(its.sex === '2'){
                        its.desc += ' 女声';
                    }
                    its.desc += " " + its.parent_name;
                    if(its.id === this.data.data.voice_type){
                        check = true;
                    }
                });
                if(!check){
                    this.choose(this.voices[0] || {})
                }
            },
            submit() {
                if(!this.searchData.account_id){
                    this.dialog = true;
                    return;
                }
                this.$refs.data.validate((valid) => {
                    if (valid) {
                        this.btnLoading = true;
                        request({
                            params: {r: 'mall/volcengine/tts-model'},
                            method: 'post',
                            data: Object.assign(this.data, {account_id: this.searchData.account_id}),
                        }).then(e => {
                            if (e.data.code === 0) {
                                this.getList()
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
            down(row) {
                const url = row.result;//这里替换为实际文件的URL
                let urlObject = new URL(url);
                let pathname = urlObject.pathname;
                let fileName = pathname.split('/').pop();
                const a = document.createElement('a');
                a.href = url;
                a.download = fileName;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            },
            getList(type = 1) {
                if(!this.searchData.account_id){
                    return;
                }
                if(type === 1) {
                    this.listLoading = true;
                }
                request({
                    params: Object.assign({r: 'mall/volcengine/tts-model'}, this.searchData),
                }).then(e => {
                    if (e.data.code === 0) {
                        this.form = e.data.data.list;
                    } else {
                        this.$message.error(e.data.msg);
                    }
                    this.listLoading = false;
                }).catch(e => {
                    this.listLoading = false;
                });
            },
            destroy: function (column) {
                this.$confirm('确认删除该记录吗?', '提示', {
                    type: 'warning'
                }).then(() => {
                    this.listLoading = true;
                    request({
                        params: {r: 'mall/volcengine/destroy'},
                        data: {id: column.id, account_id: this.searchData.account_id},
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
            refresh: function (column) {
                this.$confirm('确认重试该记录吗?', '提示', {
                    type: 'warning'
                }).then(() => {
                    this.listLoading = true;
                    request({
                        params: {r: 'mall/volcengine/refresh'},
                        data: {id: column.id, account_id: this.searchData.account_id},
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
        },
        mounted: function () {
            this.getList();
            this.timer = setInterval(() => {
                this.getList(0);
            }, 5000)
            this.change();
        }
    });
</script>
