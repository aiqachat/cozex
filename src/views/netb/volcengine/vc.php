<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: chenzs
 */
Yii::$app->loadViewComponent ('app-volcengine-choose');
$url = Yii::$app->request->absoluteUrl;
?>
<style type="text/css">
    @import "<?= \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/css/table.css' ?>";
</style>
<style>
    .table-body {
        padding: 10px;
        background-color: #fff;
    }

    .card-container {
        display: flex;
    }

    .item {
        width: 7vw;
    }

    .bi {
        font-size: 20px;
    }
</style>
<div id="app" v-cloak>
    <app-volcengine-choose @account="changeAccount" :dialog="dialog" @close="closeDialog" url="<?=$url;?>" title="字幕生成"></app-volcengine-choose>
    <el-alert style="margin-bottom: 10px;" :closable="false"
              type="success">
        基于语音识别技术，能够自动将音/视频中的语音、歌词转换为字幕文本，适用于辅助视频字幕创作和外挂字幕自动生成。产品支持多个语种的识别、打轴，是完美适配视频创作和视频观看场景的智能字幕解决方案。
    </el-alert>
    <div class="table-body">
        <div class="card-container">
            <el-card style="width: 50vw;">
                <div slot="header">
                    <span>字幕列表</span>
                    <el-button size="mini" type="text" style="float: right;" @click="$navigate({r:'netb/volcengine/generate'})">更多</el-button>
                </div>
                <el-alert title="温馨提示" type="warning" :closable="false" show-icon style="margin-bottom: 10px;">
                    <div style="padding-left: 4px;">生成的文件和批量上传的文本将在<span style="font-weight: bold">3天</span>后自动删除，请及时下载保存重要文件。</div>
                </el-alert>
                <el-table :data="form" border v-loading="listLoading">
                    <el-table-column prop="file" label="文件"></el-table-column>
                    <el-table-column label="状态" width="90">
                        <template slot-scope="scope">
                            <span v-if="scope.row.status == 1">识别中</span>
                            <span v-if="scope.row.status == 2">成功</span>
                            <span v-if="scope.row.status == 3">
                                <el-tooltip effect="dark" :content="scope.row.err_msg" placement="top">
                                    <el-button type="text">失败</el-button>
                                </el-tooltip>
                            </span>
                        </template>
                    </el-table-column>
                    <el-table-column label="操作" width="130" fixed="right">
                        <template slot-scope="scope">
                            <el-dropdown @command="(format) => down(scope.row, format)" v-if="scope.row.status == 2">
                                <el-button circle type="text" size="mini" :disabled="!!scope.row.is_data_deleted">
                                    <i class="bi bi-download"></i>
                                </el-button>
                                <el-dropdown-menu slot="dropdown">
                                    <el-dropdown-item command="txt">TXT格式</el-dropdown-item>
                                    <el-dropdown-item command="srt">SRT格式</el-dropdown-item>
                                    <el-dropdown-item command="lrc">LRC格式</el-dropdown-item>
                                </el-dropdown-menu>
                            </el-dropdown>
                            <el-tooltip effect="dark" content="删除" placement="top">
                                <el-button circle type="text" size="mini" @click="destroy(scope.row)">
                                    <i class="bi bi-trash"></i>
                                </el-button>
                            </el-tooltip>
                        </template>
                    </el-table-column>
                </el-table>
            </el-card>
            <el-card style="margin-left: 10px;width: 50vw;">
                <div slot="header">
                    <span>配置选项</span>
                </div>
                <el-form ref="data" :model="data" :rules="rules" label-width="180px">
                    <el-form-item label="音视频文件" prop="file" style="width: 35vw;">
                        <el-input v-model="data.file" size="small">
                            <template slot="append">
                                <app-attachment v-model="data.file" :type="'video'">
                                    <el-button style="padding: 12px;background-color: #409EFF;color: #FFF">上传文件</el-button>
                                </app-attachment>
                            </template>
                        </el-input>
                    </el-form-item>
                    <el-form-item label="选择语言" prop="data.language" style="width: 17vw;">
                        <el-select size="small" v-model="data.data.language" filterable>
                            <el-option v-for="item in language" :key="item.value" :label="item.label" :value="item.value"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="字幕识别类型" prop="data.caption_type">
                        <el-radio-group size="small" v-model="data.data.caption_type">
                            <el-radio label="auto">默认都识别</el-radio>
                            <el-radio label="speech">说话</el-radio>
                            <el-radio label="singing">唱歌</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="每行最多展示字数" prop="data.words_per_line" >
                        <el-input v-model="data.data.words_per_line" type="number" size="small" class="item"></el-input>
                        <span style="color: #a4a4a4;">默认推荐视频15，歌词推荐55</span>
                    </el-form-item>
                    <el-form-item label="每屏最多展示行数" prop="data.max_lines">
                        <el-input v-model="data.data.max_lines" type="number" size="small" class="item"></el-input>
                        <span style="color: #a4a4a4;">默认推荐1行</span>
                    </el-form-item>
                    <el-form-item label="是否使用数字转换功能" prop="data.use_itn">
                        <el-switch v-model="data.data.use_itn" active-value="1"
                                   inactive-value="0"></el-switch>
                        <span style="color: #a4a4a4;">开启会将识别结果中的中文数字自动转成阿拉伯数字。</span>
                    </el-form-item>
                    <el-form-item label="增加标点" prop="data.use_punc">
                        <el-switch v-model="data.data.use_punc" active-value="1"
                                   inactive-value="0"></el-switch>
                        <span style="color: #a4a4a4;">开启后仅当字幕识别类型是说话，则会将识别结果中增加标点符号。</span>
                    </el-form-item>
                    <el-form-item label="使用顺滑标注水词" prop="data.use_ddc">
                        <el-switch v-model="data.data.use_ddc" active-value="1"
                                   inactive-value="0"></el-switch>
                        <span style="color: #a4a4a4;">开启会在返回的 utterances 里增加 text 为空的静音句子，其 attribute 的 event 是 silent。且 words 中可能需要被顺滑的词会被标注出来，如"extra": { "smoothed": "repeat" }，smoothed 的值可能为 repeat（重复词）或 filler（口水词）。</span>
                    </el-form-item>
                    <el-form-item prop="data.is_del" label="文件删除">
                        <el-switch v-model="data.data.is_del" :active-value="1"
                                   :inactive-value="0"></el-switch>
                        <span style="color: #a4a4a4;">开启会在文件处理完后自动删除。</span>
                    </el-form-item>
                    <div flex="main:center">
                        <el-button :loading="btnLoading" size="small" type="primary" @click="submit">开始识别</el-button>
                    </div>
                </el-form>
            </el-card>
        </div>
    </div>
</div>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                searchData: {
                    account_id: '',
                },
                form: [],
                listLoading: false,
                btnLoading: false,

                data: {
                    data: {
                        language: 'zh-CN',
                        caption_type: 'auto',
                        max_lines: '1',
                        words_per_line: '15',
                        is_del: 1
                    }
                },
                rules: {
                    file: [
                        {required: true, message: '文件不能为空', trigger: 'blur'},
                    ],
                },
                language: [
                    {label: '中文', value: 'zh-CN'},
                    {label: '粤语', value: 'yue'},
                    {label: '上海话', value: 'wuu'},
                    {label: '闽南语', value: 'nan'},
                    {label: '西南官话', value: 'xghu'},
                    {label: '中原官话', value: 'zgyu'},
                    {label: '维语', value: 'ug'},
                    {label: '英语', value: 'en-US'},
                    {label: '日语', value: 'ja-JP'},
                    {label: '韩语', value: 'ko-KR'},
                    {label: '西班牙语', value: 'es-MX'},
                    {label: '俄语', value: 'ru-RU'},
                    {label: '法语', value: 'fr-FR'},
                ],

                dialog: false,
            };
        },
        watch: {
            'searchData.account_id': function (val, oldValue) {
                this.getList();
            },
        },
        methods: {
            closeDialog() {
                this.dialog = false;
            },
            changeAccount(val) {
                this.searchData.account_id = val;
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
                            params: {r: 'netb/volcengine/vc'},
                            method: 'post',
                            data: Object.assign(this.data, this.searchData),
                        }).then(e => {
                            if (e.data.code === 0) {
                                location.reload();
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
            down(row, format = 'txt') {
                this.listLoading = true;
                request({
                    params: {
                        r: 'netb/volcengine/download',
                        id: row.id,
                        format: format,
                    },
                }).then(e => {
                    if (e.data.code === 0) {
                        const url = e.data.data.url;
                        let urlObject = new URL(url);
                        let pathname = urlObject.pathname;
                        let fileName = pathname.split('/').pop();
                        
                        // 如果文件名不包含格式后缀，添加它
                        if (!fileName.includes('.')) {
                            fileName = fileName + '.' + format;
                        }
                        
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = fileName;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                    } else {
                        this.$message.error(e.data.msg);
                    }
                    this.listLoading = false;
                }).catch(e => {
                    this.$message.error('下载失败');
                    this.listLoading = false;
                });
            },
            getList(type = 1) {
                if (!this.searchData.account_id) {
                    return;
                }
                if (type === 1) {
                    this.listLoading = true;
                }
                let param = Object.assign({r: 'netb/volcengine/vc'}, this.searchData);
                request({
                    params: param,
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
                        params: {r: 'netb/volcengine/destroy'},
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
        }
    });
</script>
