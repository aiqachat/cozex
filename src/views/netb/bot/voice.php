<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: chenzs
 */
?>
<style>
    .table-body {
        padding: 20px;
        background-color: #fff;
    }

    .voice-card {
        position: relative;
        background: #fff;
        border-radius: 10px;
        border: 1.5px solid #eee;
        padding: 14px;
        display: flex;
        flex-direction: row;
        align-items: center;
        transition: all 0.2s;
        cursor: pointer;
        min-height: 72px;
    }

    .voice-card.active {
        border: 1.5px solid #409EFF;
        background: #f6f8ff;
        box-shadow: 0 2px 8px rgba(64, 158, 255, 0.08);
    }
</style>
<div id="app" v-cloak>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <span>智能体语音配置</span>
        </div>
        <div class="table-body" v-loading="listLoading">
            <el-card class="box-card" style="margin-top: 10px;">
                <div slot="header" class="clearfix">
                    <span>配置项</span>
                </div>
                <el-form :model="data" label-width="120px" :rules="dataRules" ref="data">
                    <el-form-item label="音色选择" prop="selected_voice">
                        <el-input v-model="data.audio_conf.voice_name" style="width: 300px;" readonly>
                            <template slot="append">
                                <el-button @click="dialogVisible = true">选择音色</el-button>
                            </template>
                        </el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button v-loading="btnLoading" type="primary" size="mini" @click="save">保存</el-button>
                    </el-form-item>
                    <el-dialog title="选择音色" :visible.sync="dialogVisible" width="700px" append-to-body>
                            <div style="height: 400px; overflow-y: auto; padding: 10px;">
                                <el-row :gutter="18">
                                    <el-col :span="8" v-for="(voice, idx) in voice_list" :key="idx" style="margin-bottom: 10px;">
                                        <div :class="['voice-card', {active: data.audio_conf.voice_id === voice.voice_id}]"
                                             @click="data.audio_conf.voice_id = voice.voice_id">
                                            <img :src="voice.avatar ? voice.avatar : 'data:image/svg+xml;utf8,<svg width=\'72\' height=\'72\' viewBox=\'0 0 72 72\' fill=\'none\' xmlns=\'http://www.w3.org/2000/svg\'><circle cx=\'36\' cy=\'36\' r=\'36\' fill=\'%23F3F6FA\'/><circle cx=\'36\' cy=\'32\' r=\'16\' fill=\'%23B3C6E2\'/><ellipse cx=\'36\' cy=\'54\' rx=\'18\' ry=\'10\' fill=\'%23B3C6E2\'/><ellipse cx=\'36\' cy=\'32\' rx=\'8\' ry=\'8\' fill=\'%23fff\'/><ellipse cx=\'36\' cy=\'54\' rx=\'10\' ry=\'6\' fill=\'%23fff\'/><circle cx=\'36\' cy=\'32\' r=\'4\' fill=\'%23B3C6E2\'/></svg>'"
                                                 style="width:56px; height:56px; border-radius:50%; object-fit:cover; margin-right:14px;">
                                            <div style="flex:1; min-width:0;">
                                                <div style="font-weight:bold; font-size:15px; color:#222; margin-bottom:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                                    {{ voice.name }}
                                                </div>
                                                <div style="font-size:12px; color:#666; margin-bottom:2px;">
                                                    {{ voice.language_name }} {{ voice.model_type == 'big' ? '大模型' : '小模型'}}
                                                </div>
                                                <el-button v-if="voice.preview_audio" type="default" size="mini"
                                                           @click.stop="playAudio(idx)"
                                                           style="margin-top:6px; border-radius:14px; background:#f6f7fa; color:#409EFF; border:none; font-weight:bold;">
                                                    {{ playingIndex === idx ? '暂停' : '试听' }}
                                                </el-button>
                                            </div>
                                            <i v-if="data.audio_conf.voice_id === voice.voice_id" class="el-icon-check"
                                               style="position:absolute;top:8px;right:8px;color:#409EFF;font-size:20px;"></i>
                                        </div>
                                    </el-col>
                                </el-row>
                                <audio ref="audioPlayer" style="display:none;"></audio>
                            </div>
                            <span slot="footer" class="dialog-footer">
                                <el-button @click="dialogVisible = false">取消</el-button>
                                <el-button type="primary" @click="confirmVoice">确定</el-button>
                            </span>
                        </el-dialog>
                </el-form>
            </el-card>
        </div>
    </el-card>
</div>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                listLoading: false,
                btnLoading: false,

                data: {
                    audio_conf: {}
                },
                dataRules: {},
                voice_list: [],
                selected_voice: null,
                playingIndex: null, // 当前播放的音频索引
                dialogVisible: false,
                selected_voice_name: '',
            };
        },
        methods: {
            save() {
                this.$refs.data.validate((valid) => {
                    if (valid) {
                        this.btnLoading = true;
                        request({
                            params: { r: 'netb/bot/set' },
                            method: 'post',
                            data: {
                                data: this.data,
                                space_id: getQuery('space_id'),
                                account_id: getQuery('account_id'),
                                type: 'voice'
                            },
                        }).then(e => {
                            if (e.data.code === 0) {
                                this.$message.success(e.data.msg);
                                setTimeout(() => {
                                    navigateTo({ r: 'netb/bot/index' });
                                }, 1000);
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
            getList() {
                this.listLoading = true;
                request({
                    params: {
                        r: 'netb/bot/set',
                        bot_id: getQuery('bot_id'),
                        account_id: getQuery('account_id'),
                        type: 'voice'
                    },
                }).then(e => {
                    if (e.data.code === 0) {
                        this.data = Object.assign({}, this.data, e.data.data.data);
                        this.voice_list = e.data.data.voice_list || [];
                    } else {
                        this.$message.error(e.data.msg);
                    }
                    this.listLoading = false;
                }).catch(e => {
                    this.listLoading = false;
                });
            },
            playAudio(idx) {
                const audio = this.$refs.audioPlayer;
                if (this.playingIndex === idx) {
                    audio.pause();
                    this.playingIndex = null;
                } else {
                    audio.src = this.voice_list[idx].preview_audio;
                    audio.play();
                    this.playingIndex = idx;
                }
                audio.onended = () => {
                    this.playingIndex = null;
                };
            },
            confirmVoice() {
                const data = this.voice_list.find(item => item.voice_id === this.data.audio_conf.voice_id);
                if(data) {
                    this.data.audio_conf.voice_name = data.name;
                }
                this.dialogVisible = false;
            },
        },
        mounted: function () {
            this.getList();
        }
    });
</script>