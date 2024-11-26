<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: chenzs
 */

use app\forms\mall\setting\ConfigForm;
use app\forms\mall\volcengine\SpeechForm;

Yii::$app->loadViewComponent('app-volcengine-choose');
Yii::$app->loadViewComponent('app-attachment-dragging');
$indSetting = (new ConfigForm())->config();
?>
<style type="text/css">
    @import "<?= \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/css/table.css' ?>";
</style>
<style>
    .speaker .el-table__row {
        cursor: pointer;
    }

    .table-body {
        padding: 10px;
        background-color: #fff;
    }

    .voice_text {
        border: 1px solid #1664ff55;
        border-radius: 12px;
        position: relative;
        height: 250px;
        background-color: #FFFFFF;
    }

    .voice_input {
        height: 70%;
    }

    .ss {
        height: 30%;
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
    }

    .voice_input img {
        background-color: white;
        border-radius: 40px;
    }

    .voice_input .item {
        background-color: #F3F3F3;
        border-radius: 10px;
        padding: 6px;
        margin-left: 10px;
    }
</style>
<div id="app" v-cloak>
    <app-volcengine-choose @account="changeAccount" title="  <?=SpeechForm::text_name[SpeechForm::TYPE_TTS_3]?>"></app-volcengine-choose>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <div style="color: #a4a4a4;">
                <?=SpeechForm::text[SpeechForm::TYPE_TTS_3]?>
            </div>
        </div>
        <el-table class="speaker" :data="form" border style="width: 100%" v-loading="listLoading" @row-click="handleRowClick">
            <el-table-column type="selection" width="55"></el-table-column>
            <el-table-column prop="Alias" label="名称"></el-table-column>
            <el-table-column prop="SpeakerID" label="speakerID" width="120"></el-table-column>
            <el-table-column prop="Version" label="已训练次数" width="110"></el-table-column>
            <el-table-column prop="AvailableTrainingTimes" label="剩余训练次数" width="120"></el-table-column>
            <el-table-column label="状态" width="190">
                <template slot-scope="scope">
                    <span v-if="scope.row.State == 'Unknown'">未进行训练</span>
                    <span v-if="scope.row.State == 'Training'">声音复刻训练中</span>
                    <span v-if="scope.row.State == 'Success'">训练成功</span>
                    <span v-if="scope.row.State == 'Active'">已激活（无法再次训练）</span>
                    <span v-if="scope.row.State == 'Expired'">已过期或账号欠费</span>
                    <span v-if="scope.row.State == 'Reclaimed'">已回收</span>
                </template>
            </el-table-column>
            <el-table-column prop="CreateTime" label="创建时间" width="170"></el-table-column>
            <el-table-column prop="ExpireTime" label="过期时间" width="170"></el-table-column>
            <el-table-column label="操作" width="150" fixed="right">
                <template slot-scope="scope">
                    <el-tooltip class="item" effect="dark" content="试听" placement="top" v-if="scope.row.DemoAudio">
                        <el-button circle type="text" size="mini" @click.stop="playMusic(scope.row.DemoAudio)">
                            <img src="statics/img/mall/music.png" alt="">
                        </el-button>
                    </el-tooltip>
                    <el-tooltip class="item" effect="dark" content="训练" placement="top">
                        <el-button circle type="text" size="mini" @click.stop="train(scope.row)">
                            <img src="statics/img/mall/edit.png" alt="">
                        </el-button>
                    </el-tooltip>
                </template>
            </el-table-column>
        </el-table>
        <div flex="dir:right" style="margin-top: 10px;margin-bottom: 10px;">
            <el-pagination @current-change="pageChange" hide-on-single-page background layout="total, prev, pager, next, jumper"
                           :total="pagination.page_count" :page-size="pagination.pageSize" :current-page="pagination.current_page"></el-pagination>
        </div>
        <el-form :model="sdf" label-width="0px" :rules="sdfRule" ref="sdf" v-loading="formLoading">
            <div class="voice_text">
                <div class="voice_input" style="margin-top: 10px;">
                    <el-form-item label="" prop="text">
                        <el-input size="small" type="textarea" v-model="sdf.text" rows="8" maxlength="5000" show-word-limit></el-input>
                    </el-form-item>
                </div>
                <div class="voice_input ss">
                    <el-form-item label="" prop="data.speed" class="item">
                        语速（0.8-2 默认为 1）
                        <el-input-number size="small" v-model="sdf.data.speed" :min="0.8" :max="2" :step="0.1"></el-input-number>
                    </el-form-item>
                    <el-form-item label="" prop="data.voice_type">
                        <div flex="cross:center" class="csd">
                            <template v-if="sdf.data.voice_type">
                                <span style="margin-left: 10px;color: white;">{{sdf.name}}</span>
                                <span style="margin: 0 10px;color: white;">|</span>
                                <el-button :loading="btnLoading" size="small" type="text" @click="submit" style="color: white;font-weight: bold">立即合成</el-button>
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
                <el-button type="primary" size="small" @click="$navigate({r:'mall/volcengine/record', type: <?=SpeechForm::TYPE_TTS_3;?>})">更多</el-button>
            </div>
            <el-table :data="list" border style="width: 100%" v-loading="loading">
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
                <el-table-column prop="created_at" label="创建时间" width="180" sortable="false"></el-table-column>
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
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <el-dialog title="训练操作" :visible.sync="newDialog" width="60%">
            <el-form :model="data" label-width="50px" :rules="rules" ref="data">
                <el-form-item label="语言" prop="language">
                    <el-radio-group v-model="data.language" size="medium">
                        <el-radio :label="item.id" v-for="item in language">{{item.name}}</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="效果" prop="model_type">
                    <el-radio-group v-model="data.model_type" size="medium">
                        <el-radio :label="0">1.0</el-radio>
                        <el-radio :label="1">2.0</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="音频" prop="type">
                    <el-radio-group v-model="data.type" size="medium">
                        <el-radio-button label="1">语音录制</el-radio-button>
                        <el-radio-button label="2">文件上传</el-radio-button>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="" prop="file" v-if="data.type == 1">
                    <div flex="main:center" style="padding: 50px;border: 1px dashed #E5E6EB;margin-top: 10px;">
                        <div style="background-color: #F6F8FA;padding: 0 10px 0 10px;border-radius: 5px;" v-if="time">
                            <i :class="recorderIcon" style="margin-right: 5px;cursor: pointer;" @click="play"></i>{{ time }}
                        </div>
                        <el-button @click="start">
                            <i class="el-icon-microphone" style="color: #3175FE;"></i>{{text}}</el-button>
                    </div>
                </el-form-item>
                <el-form-item label="" prop="file_id" v-if="data.type == 2">
                    <app-attachment-dragging :notice="'支持wav、mp3、m4a 格式 文件小于 8M'" :size="8 * 1024 * 1024" :type="3" @success="success"></app-attachment-dragging>
                    <div style="margin-top: 10px;">
                        <span style="color: #999;">已上传文件：</span>
                        <span style="margin-right: 10px;">{{data.file_name}}</span>
                    </div>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="newDialog = false">取消</el-button>
                <el-button :loading="btnLoading" type="primary" @click="dataSubmit">确认</el-button>
            </div>
        </el-dialog>
    </el-card>
</div>
<script src="<?= Yii::$app->request->baseUrl ?>/statics/js/recorder.js"></script>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                searchData: {
                    account_id: '',
                    page: 1
                },
                form: [],
                pagination: {},
                listLoading: false,

                formLoading: false,
                sdf: {
                    data: {
                        voice_type: '',
                        speed: 1
                    },
                    text: `<?= $indSetting['voice_text'] ?? ''?>`,
                    name: '',
                },
                sdfRule: {
                    text: [
                        {required: true, message: '请输入文本', trigger: 'blur'},
                    ],
                    'data.voice_type': [
                        {required: true, message: '请选择声音', trigger: 'blur'},
                    ],
                },

                loading: false,
                list: [],

                btnLoading: false,
                language: [],
                data: {},
                rules: {
                    file_id: [
                        {required: true, message: '请选择文件', trigger: 'blur'},
                    ],
                    file: [
                        {required: true, message: '请先录制语音', trigger: 'blur'},
                    ],
                },
                newDialog: false,
                recorder: null,
                recorderTime: null,
                recorderIcon: 'el-icon-video-play',
                text: '点击开始录音',
                recorderStatus: null,
                time: null,
                timer: null,
            };
        },
        watch: {
            'searchData.account_id': function (val, oldValue){
                this.getList();
            },
        },
        methods: {
            handleRowClick(row) {
                this.sdf.data.voice_type = row.SpeakerID;
                this.sdf.data.voice_name = row.Alias || '复刻音色';
                this.sdf.name = row.Alias;
            },
            changeAccount(val){
                this.searchData.account_id = val;
            },
            pageChange(currentPage) {
                this.searchData.page = currentPage;
                this.getList();
            },
            getList() {
                if(!this.searchData.account_id){
                    return;
                }
                this.listLoading = true;
                request({
                    params: Object.assign({r: 'mall/volcengine/tts-mega'}, this.searchData),
                }).then(e => {
                    if (e.data.code === 0) {
                        this.form = e.data.data.list;
                        this.pagination = e.data.data.pagination;
                        this.language = e.data.data.language;
                        if(this.form.length > 0){
                            this.handleRowClick(this.form[0])
                        }
                    } else {
                        this.$message.error(e.data.msg);
                    }
                    this.listLoading = false;
                }).catch(e => {
                    this.listLoading = false;
                });
                this.getData();
            },
            playMusic(row) {
                if (this.audio) {
                    this.audio.pause();
                    this.audio = null;
                }
                this.audio = new Audio(row);
                this.audio.play();
            },
            success(file){
                if (file.response.data && file.response.data.code === 0) {
                    this.data.file_name = file.response.data.data.name;
                    this.data.file_id = file.response.data.data.id;
                }
            },
            train(row){
                this.data = {type: 1, file_name: '', file_id: '', file: '', language: 'cn', model_type: 0};
                if(row){
                    this.data = Object.assign({}, this.data, {speaker_id: row.SpeakerID});
                }
                this.newDialog = true;
            },
            dataSubmit() {
                if(this.data.type === 1 && this.recorder){
                    this.recorder.pause();
                    clearInterval(this.timer);
                    this.data.file = this.recorder.getWAVBlob();
                }
                this.$refs.data.validate((valid) => {
                    if (valid) {
                        this.btnLoading = true;
                        let formData = new FormData()
                        for (let i in this.data) {
                            if(this.data[i] !== null) {
                                formData.append(i, this.data[i]);
                            }
                        }
                        formData.append('account_id', this.searchData.account_id)
                        this.$request({
                            headers: {'Content-Type': 'multipart/form-data'},
                            params: {r: 'mall/volcengine/tts-mega'},
                            method: 'post',
                            data: formData,
                        }).then(e => {
                            if (e.data.code === 1) {
                                this.$message.error(e.data.msg);
                                this.btnLoading = false;
                            }else{
                                this.$message.success(e.data.msg);
                                setTimeout(function (){
                                    location.reload();
                                }, 1000)
                            }
                        }).catch(e => {
                            this.btnLoading = false;
                        });
                    }
                });
            },
            start(){
                let that = this;
                if(that.recorderStatus > 0){
                    that.stop();
                }else {
                    that.time = null;
                    clearInterval(that.timer);
                    that.recorder = new Recorder();
                    Recorder.getPermission().then(
                        () => {
                            console.log("开始录音");
                            that.recorder.start(); // 开始录音
                            that.recorder.onprogress = function (params){
                                let seconds = params.duration.toFixed(0)
                                that.recorderTime = that.compute(seconds)
                                that.text = '录制中 ' + that.recorderTime + "/02:00";
                                if(seconds === '120'){
                                    that.stop();
                                }
                            }
                            that.recorderStatus = 1;
                        },
                        () => {
                            that.$message({
                                message: "请先允许该网页使用麦克风",
                                type: "info",
                            });
                        }
                    );
                }
            },
            compute(seconds){
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = seconds % 60;
                return `${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}`;
            },
            stop(){
                this.recorder.stop();
                this.text = '重新录制';
                this.recorderStatus = 0;
                this.time = "00:00/" + this.recorderTime
            },
            play(){
                let that = this;
                if(that.recorderIcon === 'el-icon-video-play'){
                    that.recorderStatus === -1 ? that.recorder.resumePlay() : that.recorder.play();
                    that.timer = setInterval(() => {
                        let seconds = that.compute(that.recorder.getPlayTime().toFixed(0))
                        that.time = seconds + "/" + this.recorderTime
                        if(seconds === that.recorderTime){
                            clearInterval(that.timer);
                            that.recorderIcon = 'el-icon-video-play'
                            that.recorderStatus = 0;
                        }
                    }, 100)
                }else{
                    that.recorder.pausePlay();
                    clearInterval(that.timer);
                    that.recorderStatus = -1;
                }
                that.recorderIcon = that.recorderIcon === 'el-icon-video-play' ? "el-icon-video-pause" : "el-icon-video-play";
            },
            handleProgress(event, file) {
                this.formLoading = true;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.sdf.text = e.target.result;
                    this.formLoading = false;
                };
                reader.readAsText(file.raw);
            },
            submit() {
                this.$refs.sdf.validate((valid) => {
                    if (valid) {
                        this.formLoading = true;
                        request({
                            params: {r: 'mall/volcengine/tts-mega-generate'},
                            method: 'post',
                            data: Object.assign(this.sdf, {account_id: this.searchData.account_id}),
                        }).then(e => {
                            if (e.data.code === 0) {
                                this.getData()
                            } else {
                                this.$message.error(e.data.msg);
                            }
                            this.formLoading = false;
                        }).catch(e => {
                            this.formLoading = false;
                        });
                    }
                });
            },
            getData(type = 1) {
                if(!this.searchData.account_id){
                    return;
                }
                if(type === 1) {
                    this.loading = true;
                }
                request({
                    params: Object.assign({r: 'mall/volcengine/tts-mega-generate'}, this.searchData),
                }).then(e => {
                    if (e.data.code === 0) {
                        this.list = e.data.data.list;
                    } else {
                        this.$message.error(e.data.msg);
                    }
                    this.loading = false;
                }).catch(e => {
                    this.loading = false;
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
            refresh: function (column) {
                this.$confirm('确认重试该记录吗?', '提示', {
                    type: 'warning'
                }).then(() => {
                    this.loading = true;
                    request({
                        params: {r: 'mall/volcengine/refresh'},
                        data: {id: column.id, account_id: this.searchData.account_id},
                        method: 'post'
                    }).then(e => {
                        if (e.data.code !== 0) {
                            this.$message.error(e.data.msg);
                        }
                        this.getData()
                    }).catch(e => {
                        this.loading = false;
                    });
                });
            },
        },
        mounted: function () {
            setInterval(() => {
                let s = false;
                for(let item of this.list) {
                    if(item.status === 1) {
                        s = true;
                    }
                }
                if(s){
                    this.getData(0)
                }
            }, 5000)
        }
    });
</script>
