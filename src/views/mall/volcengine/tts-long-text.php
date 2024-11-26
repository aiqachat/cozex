<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: chenzs
 */
Yii::$app->loadViewComponent('app-volcengine-choose');

use app\forms\mall\setting\ConfigForm;
use app\forms\mall\volcengine\SpeechForm;

$voiceType = (new SpeechForm())->voiceType(SpeechForm::TYPE_TTS_2);
$indSetting = (new ConfigForm())->config();
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

    .choose {
        border-color: #734CF8;
        background-color: #F1EFFD;
    }

    .tags {
        background: #E4F0FF;
    }

    .tags .el-radio-group, .tags .el-radio-button {
        margin: 5px;
    }

    .voice_text {
        border: 1px solid #1664ff55;
        border-radius: 12px;
        position: relative;
        height: 250px;
        background-color: #FFFFFF;
    }

    .voice_input {
        height: 65%;
    }

    .ss {
        height: 35%;
    }

    .ss .el-form-item__content, .ss .el-form-item {
        display: inline-block;
    }

    .voice_input .el-form-item__content, .voice_input .el-textarea {
        position: unset;
    }

    .voice_input .el-textarea__inner {
        min-height: 50px;
        line-height: 1.5715;
        border: 0;
        padding: 0 0 0 12px;
        resize: initial;
    }

    .voice_input .csd {
        background: linear-gradient(77.86deg, #0093ff -3.23%, #0060ff 51.11%, #ce63ff 98.65%);
        padding: 5px;
        border-radius: 20px;
        position: relative;
        top: 16px;
    }

    .voice_input img {
        background-color: white;
        border-radius: 40px;
    }

    .voice_input .item {
        background-color: #F3F3F3;
        border-radius: 10px;
        padding: 6px;
    }
</style>
<div id="app" v-cloak>
    <app-volcengine-choose @account="changeAccount" :dialog="dialog" @close="closeDialog" title="<?=SpeechForm::text_name[SpeechForm::TYPE_TTS_2]?>"></app-volcengine-choose>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <div style="color: #a4a4a4;">
                <?=SpeechForm::text[SpeechForm::TYPE_TTS_2]?>
            </div>
        </div>
        <div class="table-body">
            <div flex="box:last" style="margin-bottom: 5px;">
                <div style="line-height: 30px;">选择音色</div>
                <div flex class="search-group">
                    <el-input style="width: 250px" size="small"
                              placeholder="请输入想搜索的音色"
                              v-model="searchData.name"
                              clearable
                              @clear="change"
                              @keyup.enter.native="change"></el-input>
                    <el-button type="primary" size="small" @click="change">搜索</el-button>
                </div>
            </div>
            <el-tabs v-model="activeName" type="card" @tab-click="chooseScene">
                <el-tab-pane label="普通版" name="two"></el-tab-pane>
                <el-tab-pane label="情感预测版" name="one"></el-tab-pane>
            </el-tabs>
            <div class="tags" v-if="scene.length > 1">
                <el-radio-group v-model="searchData.tag" size="mini" @change="change">
                    <el-radio-button label="">全部场景</el-radio-button>
                    <el-radio-button v-for="item in scene" :label="item.id">{{item.name}}</el-radio-button>
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
                            <el-button @click.stop="playMusic(item.audition)"
                                       class="model-btn" size="mini" type="info" round>试听</el-button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
        <el-form :model="data" label-width="0px" :rules="rules" ref="data" v-loading="formLoading">
            <div class="voice_text">
                <div class="voice_input" style="margin-top: 10px;">
                    <el-form-item label="" prop="text">
                        <el-input size="small" type="textarea" v-model="data.text" rows="8" maxlength="100000" show-word-limit></el-input>
                    </el-form-item>
                </div>
                <div class="voice_input ss">
                    <el-form-item label="" prop="data.speed" class="item" style="margin-left: 10px;">
                        语速（0.2-3 默认为 1）
                        <el-input-number size="small" v-model="data.data.speed" :min="0.2" :max="3" :step="0.1"></el-input-number>
                    </el-form-item>
                    <el-form-item label="" prop="data.style" class="item" v-if="emotion.length > 0">
                        情感
                        <el-select size="small" v-model="data.data.style" filterable style="width: 10vw">
                            <el-option v-for="item in emotion" :key="item.value" :label="item.label" :value="item.value"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="" prop="data.language" class="item" v-if="language.length > 0">
                        语言
                        <el-select size="small" v-model="data.data.language" filterable style="width: 10vw">
                            <el-option v-for="item in language" :key="item.value" :label="item.label" :value="item.value"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="" prop="data.voice_type">
                        <div flex="cross:center" class="csd">
                            <template v-if="data.data.voice_type">
                                <img :src="rsd.pic" style="width: 45px;height: 45px;"/>
                                <div>
                                    <span style="margin-left: 10px;color: white;">{{rsd.name}}</span>
                                    <span style="margin: 0 10px;color: white;">|</span>
                                    <el-button :loading="btnLoading" size="small" type="text" @click="submit" style="color: white;font-weight: bold">立即合成</el-button>
                                </div>
                            </template>
                        </div>
                    </el-form-item>
                    <el-form-item label="">
                        <el-upload action="" accept="text/plain" :show-file-list="false" :on-progress="handleProgress">
                            <el-button style="margin-left: 10px;" size="small" type="success">上传文本</el-button>
                        </el-upload>
                    </el-form-item>
                </div>
            </div>
        </el-form>
        <div class="table-body" style="margin-top: 10px;">
            <div flex="box:last cross:center" style="margin-bottom: 5px;">
                <div>最近合成记录列表</div>
                <el-button type="primary" size="small" @click="$navigate({r:'mall/volcengine/one', type: <?=SpeechForm::TYPE_TTS_2;?>})">更多</el-button>
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
                <el-table-column label="状态" width="80">
                    <template slot-scope="scope">
                        <span v-if="scope.row.status == 1">处理中</span>
                        <span v-if="scope.row.status == 2">成功</span>
                        <span v-if="scope.row.status == 3">
                            <el-tooltip class="item" effect="dark" :content="scope.row.err_msg" placement="top">
                                <el-button type="text">失败</el-button>
                            </el-tooltip>
                        </span>
                    </template>
                </el-table-column>
                <el-table-column prop="data.voice_name" label="使用音色" width="150"></el-table-column>
                <el-table-column prop="created_at" label="创建时间" width="180"></el-table-column>
                <el-table-column label="操作" width="220" fixed="right">
                    <template slot-scope="scope">
                        <el-tooltip class="item" effect="dark" content="播放" placement="top" v-if="scope.row.status == 2">
                            <el-button circle type="text" size="mini" @click="playMusic(scope.row.result)">
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
                },
                form: [],
                listLoading: false,
                formLoading: false,
                btnLoading: false,

                data: {
                    data: {
                        style: '',
                        language: '',
                        voice_type: '',
                        speed: 1
                    },
                    text: `<?= $indSetting['voice_text'] ?? ''?>`,
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
                scene: [],
                voices: [],
                emotion: [],
                language: [],
                rsd: {},
                dialog: false,
                audio: null,
                activeName: 'two',
            };
        },
        watch: {
            'searchData.account_id': function (val, oldValue){
                this.getList();
            },
        },
        methods: {
            chooseScene() {
                this.init();
                this.scene = [];
                if(this.activeName === 'one') { // 情感预测版
                    this.options.forEach(item => {
                        if (item.id === 'yousheng') {
                            this.scene.push(item)
                        }
                    });
                    this.data.data.version = 2; //情感预测版
                } else {
                    this.scene = this.options
                    this.data.data.version = 1; //普通版
                }
                this.change();
            },
            change() {
                this.voices = [];
                this.scene.forEach(item => {
                    if(!this.searchData.tag || item.id === this.searchData.tag){
                        if(item.children.length > 0){
                            item.children.forEach(its => {
                                its.desc = item.name;
                                this.voices.push(its)
                            });
                        }
                    }
                });
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
                    if(its.id === this.data.data.voice_type){
                        check = true;
                    }
                    its.pic = "statics/img/voice/default.png"
                });
                if(!check){
                    this.choose(this.voices[0] || {})
                }
            },
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
            init() {
                this.language = [];
                this.emotion = [];
            },
            choose(row) {
                this.data.data.voice_type = row.id;
                this.data.data.voice_name = row.name;
                this.rsd = row;
                this.voices.forEach(its => {
                    if(its.id === row.id){
                        if(its.language){
                            this.language = its.language;
                            this.data.data.language = its.language[0].value;
                        }
                        if(its.emotion){
                            this.emotion = its.emotion;
                            this.data.data.style = its.emotion[0].value;
                        }
                    }
                });
            },
            playMusic(row) {
                if (this.audio) {
                    this.audio.pause();
                    this.audio = null;
                }
                this.audio = new Audio(row);
                this.audio.play();
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
                            params: {r: 'mall/volcengine/tts-long-text'},
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
                    params: Object.assign({r: 'mall/volcengine/tts-long-text'}, this.searchData),
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
            setInterval(() => {
                let s = false;
                for(let item of this.form) {
                    if(item.status === 1) {
                        s = true;
                    }
                }
                if(s){
                    this.getList(0);
                }
            }, 5000)
            this.chooseScene();
        }
    });
</script>
