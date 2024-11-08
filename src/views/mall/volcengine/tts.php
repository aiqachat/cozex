<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: chenzs
 */
Yii::$app->loadViewComponent('app-volcengine-choose');

use app\forms\mall\volcengine\SpeechForm;

$voiceType = (new SpeechForm())->voiceType();
?>
<style type="text/css">
    @import "<?= \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/css/table.css' ?>";
</style>
<style>
    .table-body {
        padding: 20px 20px 0 20px;
        background-color: #fff;
    }

    .input-item {
        display: inline-block;
        width: 290px;
        margin: 0 0 20px;
    }
</style>
<div id="app" v-cloak>
    <app-volcengine-choose @account="changeAccount" :dialog="newDialog" @close="closeDialog"></app-volcengine-choose>
    <el-alert style="margin-bottom: 10px;" :closable="false"
              type="success">
        语音合成(TTS, Text to Speech)，能将文本转换成人类声音。它运用了语音合成领域突破性的端到端合成方案，能提供高保真、个性化的音频
    </el-alert>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <span>语音合成列表</span>
            <div style="float: right;margin-top: -5px">
                <el-button type="primary" @click="open" size="small">添加</el-button>
            </div>
        </div>
        <div class="table-body">
            <div class="input-item">
                <el-input @keyup.enter.native="search" size="small" placeholder="请输入名称" v-model="searchData.keyword" clearable @clear="search">
                    <el-button slot="append" icon="el-icon-search" @click="search"></el-button>
                </el-input>
            </div>
            <div class="input-item">
                <el-select size="small" v-model="searchData.type" @change='search'>
                    <el-option key="" label="全部" value=""></el-option>
                    <el-option key="4" label="大模型语音" value="4"></el-option>
                    <el-option key="5" label="精品长文本语音" value="5"></el-option>
                </el-select>
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
                <el-table-column label="版本" width="110">
                    <template slot-scope="scope">
                        <el-tag v-if="scope.row.type == 4" size="mini" type="success">大模型语音</el-tag>
                        <el-tag v-else-if="scope.row.data.version == 1" size="mini" type="success">普通版</el-tag>
                        <el-tag v-else-if="scope.row.data.version == 2" size="mini" type="success">情感预测版</el-tag>
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
            <div flex="dir:right" style="margin-top: 20px;">
                <el-pagination @current-change="pagination" hide-on-single-page background layout="total, prev, pager, next, jumper"
                               :total="pageCount" :page-size="pageSize" :current-page="currentPage"></el-pagination>
            </div>
        </div>
        <el-dialog title="操作" :visible.sync="dialog" width="30%">
            <el-form :model="data" label-width="110px" :rules="rules" ref="data">
                <el-form-item label="AppId" prop="data.app_id">
                    <el-input size="small" placeholder="请输入APPID" v-model.trim="data.data.app_id"></el-input>
                    <div style="color: #a4a4a4;">注：空则默认用全局配置</div>
                </el-form-item>
                <el-form-item label="AccessToken" prop="data.access_token">
                    <el-input size="small" placeholder="请输入TOKEN" v-model.trim="data.data.access_token"></el-input>
                    <div style="color: #a4a4a4;">注：空则默认用全局配置</div>
                </el-form-item>
                <el-form-item label="使用Api" prop="type">
                    <el-radio-group size="small" v-model.trim="data.type" @change="change">
                        <el-radio :label="4">大模型语音</el-radio>
                        <el-radio :label="5">精品长文本语音</el-radio>
                    </el-radio-group>
                    <div style="color: #a4a4a4;" v-if="data.type == 4">注：目前该能力只对企业客户开放，如需测试或接入须先进行企业认证。</div>
                </el-form-item>
                <el-form-item label="使用版本" prop="data.version" v-if="data.type == 5">
                    <el-radio-group size="small" v-model.trim="data.data.version" @change="change">
                        <el-radio :label="1">普通版</el-radio>
                        <el-radio :label="2">情感预测版</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="语音文本" prop="text">
                    <el-input size="small" type="textarea" v-model.trim="data.text" rows="10" show-word-limit></el-input>
                </el-form-item>
                <el-form-item label="音色列表" prop="data.voice_type" v-if="voices.length > 0">
                    <el-select size="small" v-model="data.data.voice_type" @change="changeVoice" filterable>
                        <el-option v-for="(item, ind) in voices" :key="ind" :label="item.name" :value="item.id"></el-option>
                    </el-select>
                    <div style="color: #a4a4a4;">注：音色说明请点击查看<a href="https://www.volcengine.com/docs/6561/97465" target="_blank">官方文档</a></div>
                </el-form-item>
                <el-form-item label="感情选择" prop="data.style" v-if="emotion.length > 0">
                    <el-select size="small" v-model="data.data.style" filterable>
                        <el-option v-for="item in emotion" :key="item.value" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="语言选择" prop="data.language" v-if="language.length > 0">
                    <el-select size="small" v-model="data.data.language" filterable>
                        <el-option v-for="item in language" :key="item.value" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialog = false">取消</el-button>
                <el-button :loading="btnLoading" type="primary" @click="submit">确认</el-button>
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
                    type: '',
                    account_id: '',
                },
                form: [],
                pageCount: 0,
                pageSize: 10,
                page: 1,
                currentPage: null,
                listLoading: false,
                btnLoading: false,

                newDialog: false,
                dialog: false,
                data: {
                    type: 5,
                    data: {
                        version: 1,
                        language: '',
                        style: '',
                        voice_type: '',
                    }
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
                language: [],
                emotion: [],
            };
        },
        watch: {
            'searchData.account_id': function (val, oldValue){
                this.getList();
            },
        },
        methods: {
            closeDialog() {
                this.newDialog = false;
            },
            open(){
                if(!this.searchData.account_id){
                    this.newDialog = true;
                    return;
                }
                this.dialog = true
            },
            changeAccount(val){
                this.searchData.account_id = val;
            },
            playMusic(row) {
                let text = "audio" + row.id;
                this.$refs[text].play();
            },
            init() {
                this.language = [];
                this.emotion = [];
            },
            changeVoice() {
                this.init();
                this.voices.forEach(its => {
                    if(its.id === this.data.data.voice_type){
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
            change() {
                let list = [];
                this.data.data.voice_type = '';
                this.init();
                this.voices = [];
                if (this.data.type === 5 && this.data.data.version === 2) { // 情感预测版
                    this.options[this.data.type].forEach(item => {
                        if (item.id === 'yousheng') {
                            list.push(item)
                        }
                    });
                } else {
                    list = this.options[this.data.type]
                }
                list.forEach(item => {
                    if(item.children.length > 0){
                        item.children.forEach(its => {
                            this.voices.push(its)
                        });
                    }
                });
            },
            submit() {
                this.$refs.data.validate((valid) => {
                    if (valid) {
                        this.btnLoading = true;
                        request({
                            params: {r: 'mall/volcengine/tts'},
                            method: 'post',
                            data: Object.assign(this.data, {account_id: this.searchData.account_id}),
                        }).then(e => {
                            if (e.data.code === 0) {
                                this.getList()
                                this.dialog = false;
                            } else {
                                this.$message.error(e.data.msg);
                                this.btnLoading = false;
                            }
                        }).catch(e => {
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
            search() {
                this.page = 1;
                this.getList();
            },
            pagination(currentPage) {
                this.page = currentPage;
                this.getList();
            },
            getList(type = 1) {
                if(!this.searchData.account_id){
                    return;
                }
                if(type === 1) {
                    this.listLoading = true;
                }
                let param = Object.assign({r: 'mall/volcengine/tts', page: this.page}, this.searchData);
                request({
                    params: param,
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
            this.page = getQuery('page') ? getQuery('page') : 1;
            this.getList();
            this.timer = setInterval(() => {
                this.getList(0);
            }, 5000)
            this.change();
        }
    });
</script>
