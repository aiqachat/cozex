<div id="app" v-cloak>
    <el-card shadow="never" v-loading="loading">
        <!-- 搜索表单 -->
        <div class="search-container">
            <div class="search-header">
                <h3 class="search-title">
                    <i class="el-icon-search"></i>
                    音色搜索
                </h3>
            </div>
            <el-form :inline="true" :model="searchForm" class="search-form">
                <div class="search-row">
                    <el-form-item label="音色名称" class="search-item">
                        <el-input v-model="searchForm.name" placeholder="请输入音色名称" clearable prefix-icon="el-icon-user"
                            class="search-input">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="音色ID" class="search-item">
                        <el-input v-model="searchForm.voice_id" placeholder="请输入音色ID" clearable
                            prefix-icon="el-icon-postcard" class="search-input">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="类别" class="search-item">
                        <el-select v-model="searchForm.voice_type" placeholder="请选择类别" clearable class="search-select">
                            <el-option v-for="item in voiceTypeOptions" :key="item.value" :label="item.label"
                                :value="item.value"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="语种" class="search-item">
                        <el-select v-model="searchForm.language" placeholder="请选择语种" clearable class="search-select">
                            <el-option v-for="item in languageOptions" :key="item.value" :label="item.label"
                                :value="item.value"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="状态" class="search-item">
                        <el-select v-model="searchForm.status" placeholder="请选择状态" clearable class="search-select">
                            <el-option v-for="item in statusOptions" :key="item.value" :label="item.label"
                                :value="item.value"></el-option>
                        </el-select>
                    </el-form-item>
                </div>
                <div class="search-actions">
                    <el-button type="primary" @click="handleSearch" :loading="loading" class="search-btn">
                        <i class="el-icon-search"></i> 搜索
                    </el-button>
                </div>
            </el-form>
        </div>

        <div style="float: right;margin-bottom: 10px;">
            <el-button type="primary" size="small" @click="reset">
                <i class="el-icon-refresh"></i> 恢复默认数据
            </el-button>
            <el-button type="primary" size="small" @click="handleAdd">
                <i class="el-icon-plus"></i> 添加音色
            </el-button>
        </div>
        <!-- 音色列表 -->
        <el-table :data="voiceList" border style="width: 100%">
            <el-table-column label="音色信息" min-width="200" align="center">
                <template slot-scope="scope">
                    <div style="display: flex; align-items: center; justify-content: center;">
                        <!-- 音色图片 -->
                        <div style="margin-right: 15px;">
                            <el-avatar :size="60" :src="scope.row.pic" shape="circle" fit="cover"></el-avatar>
                        </div>
                        <!-- 音色详细信息 -->
                        <div style="text-align: left;">
                            <div style="font-weight: bold; margin-bottom: 5px;">{{ scope.row.name }}</div>
                            <div style="font-size: 12px; color: #666; margin-bottom: 3px;">
                                <span>性别：{{ scope.row.sex == 1 ? '男' : '女' }}</span>
                                <span style="margin-left: 10px;">年龄：{{ scope.row.age_txt }}</span>
                            </div>
                            <div>
                                <el-button size="mini" type="text" @click="playVoice(scope.row)"
                                    :loading="scope.row.playing">
                                    <i class="el-icon-video-play"></i> 试听
                                </el-button>
                            </div>
                        </div>
                    </div>
                </template>
            </el-table-column>
            <el-table-column prop="voice_id" label="音色ID" min-width="160" align="center"></el-table-column>
            <el-table-column prop="voice_type" label="类别" min-width="70" align="center"></el-table-column>
            <el-table-column prop="language" label="语言" min-width="70" align="center"></el-table-column>
            <el-table-column prop="status" label="状态" width="80" align="center">
                <template slot-scope="scope">
                    <el-tag :type="scope.row.status == 1 ? 'success' : 'danger'">
                        {{ scope.row.status == 1 ? '启用' : '禁用' }}
                    </el-tag>
                </template>
            </el-table-column>
            <el-table-column prop="created_at" label="创建时间" width="160" align="center"></el-table-column>
            <el-table-column label="操作" width="160" align="center">
                <template slot-scope="scope">
                    <el-button size="mini" type="primary" @click="handleEdit(scope.row)">编辑</el-button>
                    <el-button size="mini" type="danger" @click="handleDelete(scope.row)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>

        <!-- 分页 -->
        <div style="text-align: center; margin-top: 20px;" v-if="pagination">
            <el-pagination @current-change="handleCurrentChange" :current-page="page" layout="prev, pager, next"
                :page-count="pagination.page_count">
            </el-pagination>
        </div>
    </el-card>

    <!-- 添加/编辑对话框 -->
    <el-dialog :title="ruleForm.id ? '编辑音色' : '添加音色'" :visible.sync="dialogVisible" width="600px" @close="resetForm">
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px">
            <el-form-item label="音色中文名" prop="name">
                <el-input v-model="ruleForm.name" placeholder="请输入音色名称"></el-input>
            </el-form-item>
            <el-form-item label="音色英文名" prop="name">
                <el-input v-model="ruleForm.language_data.en.name" placeholder="请输入英文名称"></el-input>
            </el-form-item>
            <el-form-item label="音色ID" prop="voice_id">
                <el-input v-model="ruleForm.voice_id" placeholder="请输入音色ID"></el-input>
            </el-form-item>
            <el-form-item label="类别" prop="voice_type">
                <el-select v-model="ruleForm.voice_type" placeholder="请选择类别" style="width: 100%;">
                    <el-option v-for="item in voiceTypeOptions" :key="item.value" :label="item.label"
                        :value="item.value"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="语种" prop="language">
                <el-select v-model="ruleForm.language" placeholder="请选择语种" style="width: 100%;">
                    <el-option v-for="item in languageOptions" :key="item.value" :label="item.label"
                        :value="item.value"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="支持的情感" prop="emotion" v-if="ruleForm.voice_type === '多情感'">
                <el-select v-model="ruleForm.emotion" multiple placeholder="请选择支持的情感" style="width: 100%;">
                    <el-option v-for="item in emotionOptions" :key="item.value" :label="item.label"
                        :value="item.value"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="音色性别" prop="sex">
                <el-radio-group v-model="ruleForm.sex">
                    <el-radio :label="1">男</el-radio>
                    <el-radio :label="2">女</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="年龄段" prop="age">
                <el-radio-group v-model="ruleForm.age">
                    <el-radio :label="5">儿童</el-radio>
                    <el-radio :label="2">少年/少女</el-radio>
                    <el-radio :label="1">青年</el-radio>
                    <el-radio :label="3">中年</el-radio>
                    <el-radio :label="4">老年</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="音色图片" prop="pic">
                <el-input v-model="ruleForm.pic">
                    <template slot="append">
                        <app-attachment v-model="ruleForm.pic">
                            <el-button>上传图片</el-button>
                        </app-attachment>
                    </template>
                </el-input>
                <img style="border-color: #100a46; height: 100px;" v-if="ruleForm.pic" :src="ruleForm.pic">
            </el-form-item>
            <el-form-item label="试听音频" prop="audio">
                <el-input v-model="ruleForm.audio" placeholder="请输入试听音频URL"></el-input>
            </el-form-item>
            <el-form-item label="状态" prop="status">
                <el-radio-group v-model="ruleForm.status">
                    <el-radio :label="1">启用</el-radio>
                    <el-radio :label="0">禁用</el-radio>
                </el-radio-group>
            </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button @click="dialogVisible = false">取消</el-button>
            <el-button type="primary" @click="submitForm" :loading="submitLoading">确定</el-button>
        </div>
    </el-dialog>
</div>

<script>
    new Vue({
        el: '#app',
        data() {
            return {
                loading: false,
                submitLoading: false,
                voiceList: [],
                pagination: {},
                page: 1,
                dialogVisible: false,
                searchForm: {
                    name: '',
                    voice_id: '',
                    voice_type: '',
                    language: '',
                    status: ''
                },
                ruleForm: {
                    language_data: { en: {} }
                },
                rules: {
                    name: [
                        { required: true, message: '请输入音色名称', trigger: 'blur' },
                        { min: 1, max: 50, message: '长度在 1 到 50 个字符', trigger: 'blur' }
                    ],
                    voice_id: [
                        { required: true, message: '请输入音色ID', trigger: 'blur' },
                        { min: 1, max: 50, message: '长度在 1 到 50 个字符', trigger: 'blur' }
                    ],
                    voice_type: [
                        { required: true, message: '请选择类别', trigger: 'change' }
                    ],
                    pic: [
                        { required: true, message: '请选择图片', trigger: 'change' }
                    ],
                },
                // 类别选项
                voiceTypeOptions: [
                    { value: '通用场景', label: '通用场景' },
                    { value: '有声阅读', label: '有声阅读' },
                    { value: '视频配音', label: '视频配音' },
                    { value: '角色扮演', label: '角色扮演' },
                    { value: '趣味方言', label: '趣味方言' },
                    { value: '多语种', label: '多语种' },
                    { value: '客服场景', label: '客服场景' },
                    { value: '多情感', label: '多情感' },
                ],
                // 语种选项
                languageOptions: [
                    { value: '中文', label: '中文' },
                    { value: '北京口音', label: '北京口音' },
                    { value: '台湾口音', label: '台湾口音' },
                    { value: '四川口音', label: '四川口音' },
                    { value: '广东口音', label: '广东口音' },
                    { value: '广西口音', label: '广西口音' },
                    { value: '长沙口音', label: '长沙口音' },
                    { value: '青岛口音', label: '青岛口音' },
                    { value: '河南口音', label: '河南口音' },
                    { value: '日语', label: '日语' },
                    { value: '澳洲英语', label: '澳洲英语' },
                    { value: '美式英语', label: '美式英语' },
                    { value: '英式英语', label: '英式英语' },
                    { value: '西班牙语', label: '西班牙语' },
                ],
                // 状态选项
                statusOptions: [
                    { value: '', label: '全部' },
                    { value: 1, label: '启用' },
                    { value: 0, label: '禁用' }
                ],
                // 情感类型选项
                emotionOptions: [
                    { value: 'happy', label: '开心' },
                    { value: 'sad', label: '悲伤' },
                    { value: 'angry', label: '生气' },
                    { value: 'surprised', label: '惊讶' },
                    { value: 'fear', label: '恐惧' },
                    { value: 'hate', label: '厌恶' },
                    { value: 'excited', label: '激动' },
                    { value: 'coldness', label: '冷漠' },
                    { value: 'neutral', label: '中性' }
                ]
            };
        },
        created() {
            this.getVoiceList();
        },
        methods: {
            // 获取音色列表
            getVoiceList() {
                this.loading = true;
                const params = {
                    r: 'admin/setting/voice',
                    page: this.page,
                };

                // 添加搜索参数
                if (this.searchForm.name) {
                    params.name = this.searchForm.name;
                }
                if (this.searchForm.voice_id) {
                    params.voice_id = this.searchForm.voice_id;
                }
                if (this.searchForm.voice_type) {
                    params.voice_type = this.searchForm.voice_type;
                }
                if (this.searchForm.language) {
                    params.language = this.searchForm.language;
                }
                if (this.searchForm.status !== '') {
                    params.status = this.searchForm.status;
                }
                this.voiceList = [];

                this.$request({
                    params: params,
                    method: 'get',
                }).then(e => {
                    this.loading = false;
                    if (e.data.code === 0) {
                        this.voiceList = e.data.data.list || [];
                        this.pagination = e.data.data.pagination || {};
                    } else {
                        this.$message.error(e.data.msg);
                    }
                }).catch(e => {
                    this.loading = false;
                    this.$message.error('获取数据失败');
                });
            },

            // 添加音色
            handleAdd() {
                this.dialogVisible = true;
                this.resetForm();
            },

            // 编辑音色
            handleEdit(row) {
                this.dialogVisible = true;
                this.$nextTick(() => {
                    this.ruleForm = JSON.parse(JSON.stringify(row));
                });
            },

            // 删除音色
            handleDelete(row) {
                this.$confirm('确定要删除这个音色吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.deleteVoice(row.id);
                });
            },

            reset() {
                this.$confirm('确定恢复原始数据吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.$request({
                        params: {
                            r: 'admin/setting/voice',
                        },
                        method: 'post',
                        data: {
                            action: 'reset',
                        },
                    }).then(e => {
                        if (e.data.code === 0) {
                            this.$message.success(e.data.msg);
                            this.getVoiceList();
                        } else {
                            this.$message.error(e.data.msg);
                        }
                    })
                });
            },

            // 执行删除
            deleteVoice(id) {
                this.$request({
                    params: {
                        r: 'admin/setting/voice',
                    },
                    method: 'post',
                    data: {
                        action: 'delete',
                        id: id
                    },
                }).then(e => {
                    if (e.data.code === 0) {
                        this.$message.success('删除成功');
                        this.getVoiceList();
                    } else {
                        this.$message.error(e.data.msg);
                    }
                }).catch(e => {
                    this.$message.error('删除失败');
                });
            },

            // 提交表单
            submitForm() {
                this.$refs.ruleForm.validate((valid) => {
                    if (valid) {
                        this.submitLoading = true;

                        this.$request({
                            params: {
                                r: 'admin/setting/voice',
                            },
                            method: 'post',
                            data: this.ruleForm,
                        }).then(e => {
                            this.submitLoading = false;
                            if (e.data.code === 0) {
                                this.$message.success(e.data.msg);
                                this.dialogVisible = false;
                                this.getVoiceList();
                            } else {
                                this.$message.error(e.data.msg);
                            }
                        }).catch(e => {
                            this.submitLoading = false;
                            this.$message.error('操作失败');
                        });
                    }
                });
            },

            // 重置表单
            resetForm() {
                if (this.$refs.ruleForm) {
                    this.$refs.ruleForm.resetFields();
                }
                this.ruleForm = {
                    status: 1,
                    sex: 1,
                    age: 1,
                    emotion: [],
                    language_data: { en: {} }
                };
            },

            // 当前页改变
            handleCurrentChange(val) {
                this.page = val;
                this.getVoiceList();
            },

            // 搜索
            handleSearch() {
                this.page = 1; // 搜索时重置到第一页
                this.getVoiceList();
            },

            // 播放音色试听
            playVoice(row) {
                if (!row.audio) {
                    this.$message.warning('该音色暂无试听音频');
                    return;
                }

                // 设置播放状态
                this.$set(row, 'playing', true);

                // 创建音频对象
                const audio = new Audio(row.audio);

                // 播放结束后重置状态
                audio.addEventListener('ended', () => {
                    this.$set(row, 'playing', false);
                });

                // 播放失败处理
                audio.addEventListener('error', () => {
                    this.$set(row, 'playing', false);
                    this.$message.error('音频播放失败');
                });

                // 开始播放
                audio.play().then(() => {
                    // 播放成功
                }).catch(() => {
                    this.$set(row, 'playing', false);
                    this.$message.error('音频播放失败');
                });
            },
        },
    });
</script>

<style>
    /* 搜索容器样式 */
    .search-container {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border: 1px solid #e4e7ed;
    }

    /* 搜索标题样式 */
    .search-header {
        margin-bottom: 20px;
        border-bottom: 2px solid #409eff;
        padding-bottom: 10px;
    }

    .search-title {
        margin: 0;
        color: #303133;
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .search-title i {
        color: #409eff;
        font-size: 20px;
    }

    /* 搜索表单样式 */
    .search-form {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    /* 按钮区域样式 */
    .search-actions {
        display: flex;
        justify-content: center;
        padding-top: 15px;
        border-top: 1px solid #f0f0f0;
    }

    /* 动画效果 */
    .search-container {
        animation: fadeInUp 0.6s ease-out;
    }
</style>