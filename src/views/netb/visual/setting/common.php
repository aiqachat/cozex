<?php
// 共享组件和 Mixin
?>

<!-- 积分换算说明组件 -->
<template id="integral-alert">
    <el-alert type="info" v-if="form.integral"
              :description="'1<?= $data['currency_name']; ?> = ' + form.integral.integral_rate + '积分，积分可用于兑换各项语音合成服务'"
              show-icon :closable="false">
        <div slot="title" style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
            <span>积分换算说明</span>
            <el-button type="primary" size="mini" @click="$navigate({r:'netb/integral/setting'}, true)">
                <i class="el-icon-setting"></i> 前往积分设置
            </el-button>
        </div>
    </el-alert>
</template>

<!-- 服务提示组件 -->
<template id="service-alert">
    <el-alert type="warning" style="margin-top: 10px;" show-icon :closable="false">
        <div slot="title" style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
            <span>服务提示</span>
            <div>
                <el-button type="success" size="mini" @click="openServiceLink">
                    <i class="el-icon-link"></i> {{ serviceButtonText }}
                </el-button>
                <el-button v-if="showKeyButton" type="primary" size="mini"
                           @click="$navigate({r:'netb/index/volcengine'}, true)">
                    <i class="el-icon-key"></i> 设置默认密钥
                </el-button>
            </div>
        </div>
        <div>{{ serviceDescription }}</div>
    </el-alert>
</template>

<!-- 密钥管理组件 -->
<template id="key-management">
    <div>
        <el-divider content-position="left">{{ keyTitle }}</el-divider>
        <el-form-item :label="keyLabel" prop="key_id">
            <div class="currency-width">
                <el-select v-model="form.key_id" placeholder="请选择密钥" size="small">
                    <el-option v-for="item in filteredKeys" :key="item.id"
                               :label="item.name + ' (' + item.account_id + ')'" :value="item.id">
                    </el-option>
                </el-select>
                <el-button type="text" @click="goToAddKey" style="padding-left: 10px;">添加密钥</el-button>
                <el-tooltip effect="dark" content="刷新密钥列表" placement="top">
                    <el-button class="el-icon-refresh" type="text" @click="refreshKeys"
                               style="padding-left: 5px;"></el-button>
                </el-tooltip>
            </div>
        </el-form-item>
        <el-form-item v-if="showApiKey" label="API Key" prop="api_key">
            <el-input v-model="form.api_key" class="currency-width" type="text"></el-input>
            <el-button type="text" @click="goToApiKey" style="padding-left: 10px;">获取KEY</el-button>
        </el-form-item>
    </div>
</template>

<!-- 图片生成计费组件 -->
<template id="image-pricing">
    <div>
        <el-divider content-position="left">图片计费</el-divider>
        <el-form-item label="文生图" prop="image_generate_price">
            <el-input v-model="form.image_generate_price" class="currency-width" type="number">
                <template slot="prepend">按调用请求成功一次扣</template>
                <template slot="append">积分</template>
            </el-input>
        </el-form-item>
        <el-form-item label="图生图" prop="img_to_img_generate_price" v-if="form.img_to_img_generate_price">
            <el-input v-model="form.img_to_img_generate_price" class="currency-width" type="number">
                <template slot="prepend">按调用请求成功一次扣</template>
                <template slot="append">积分</template>
            </el-input>
        </el-form-item>
    </div>
</template>

<!-- 图片模型 -->
<template id="image-open">
    <div>
        <el-divider content-position="left">图片模型开启支持</el-divider>
        <el-tabs type="border-card">
            <el-tab-pane label="doubao-seedream-3.0">
                <el-form-item label="名称">
                    <el-input v-model="form.img_model_name" class="currency-width" type="text" />
                </el-form-item>
                <el-form-item label="图标">
                    <app-attachment v-model="form.img_model_pic">
                        <el-button size="mini">选择图标</el-button>
                    </app-attachment>
                    <app-image mode="aspectFill" width="45px" height='45px' :src="form.img_model_pic">
                    </app-image>
                </el-form-item>
                <el-form-item label="使用开启">
                    <el-switch v-model="form.img_model_open" :active-value="1" :inactive-value="0" active-text="开启"
                               inactive-text="关闭">
                    </el-switch>
                </el-form-item>
                <el-form-item label="仅使用">
                    <el-switch v-model="form.img_model_only" :active-value="1" :inactive-value="0" active-text="开启"
                               inactive-text="关闭">
                    </el-switch>
                    <div style="color: #909399;">选择开启，用户端不展示模型信息，所有生成都调用当前模型</div>
                </el-form-item>
            </el-tab-pane>
        </el-tabs>
    </div>
</template>

<!-- 视频生成计费组件 -->
<template id="video-pricing">
    <div>
        <el-divider content-position="left">视频计费</el-divider>
        <div style="margin-bottom: 15px;">
            <span style="color: #606266; font-size: 14px;">
                <i class="el-icon-info"></i> 按生成视频时长每秒扣除相应积分，不同{{ useTable ? '分辨率和' : '' }}比例价格不同
            </span>
        </div>

        <!-- 表格模式 -->
        <el-table v-if="useTable" :data="tableData" border style="width: 100%; margin-bottom: 20px;">
            <el-table-column prop="modelLabel" label="模型" width="120" align="center">
                <template slot-scope="scope">
                    <strong>{{ scope.row.modelLabel }}</strong>
                </template>
            </el-table-column>
            <el-table-column prop="resolution" label="分辨率" width="80" align="center">
                <template slot-scope="scope">
                    <strong>{{ scope.row.resolution }}</strong>
                </template>
            </el-table-column>
            <el-table-column v-for="ratio in dynamicPricingData.ratios" :key="ratio" :label="ratio" min-width="150"
                             align="center">
                <template slot-scope="scope">
                    <el-input v-if="supportsRatio(scope.row, ratio)"
                              v-model="form['video'][getFormFieldName(scope.row, ratio)]" type="number" min="0"
                              placeholder="0.00" size="mini">
                        <template slot="append">积分/秒</template>
                    </el-input>
                    <span v-else style="color: #ccc; font-size: 12px;">不支持</span>
                </template>
            </el-table-column>
        </el-table>

        <!-- 简单模式 -->
        <div v-else>
            <el-form-item label="16：9">
                <el-input v-model="form.video_16_9" class="currency-width" type="number">
                    <template slot="append">积分/秒</template>
                </el-input>
            </el-form-item>
            <el-form-item label="4：3">
                <el-input v-model="form.video_4_3" class="currency-width" type="number">
                    <template slot="append">积分/秒</template>
                </el-input>
            </el-form-item>
            <el-form-item label="1：1">
                <el-input v-model="form.video_1_1" class="currency-width" type="number">
                    <template slot="append">积分/秒</template>
                </el-input>
            </el-form-item>
            <el-form-item label="21：9">
                <el-input v-model="form.video_21_9" class="currency-width" type="number">
                    <template slot="append">积分/秒</template>
                </el-input>
            </el-form-item>
        </div>
    </div>
</template>

<!-- 视频模型 -->
<template id="video-open">
    <div>
        <el-divider content-position="left">视频模型开启支持</el-divider>
        <el-tabs type="border-card">
            <el-tab-pane :label="item.label" v-for="(item, key) in models" :key="key">
                <el-form-item label="名称">
                    <el-input v-model="getModelData(item.label).name" class="currency-width"
                              type="text"></el-input>
                </el-form-item>
                <el-form-item label="支持类型">
                    <el-tag v-for="item in getModelInfo(item.label).modes || []">{{item == 'text' ? '文生视频' : '图生视频'}}</el-tag>
                </el-form-item>
                <el-form-item label="图标">
                    <app-attachment v-model="getModelData(item.label).pic">
                        <el-button size="mini">选择图标</el-button>
                    </app-attachment>
                    <app-image mode="aspectFill" width="45px" height='45px'
                               :src="getModelData(item.label).pic">
                    </app-image>
                </el-form-item>
                <el-form-item label="使用开启">
                    <el-switch v-model="getModelData(item.label).open" active-value="1"
                               inactive-value="0" active-text="开启" inactive-text="关闭">
                    </el-switch>
                </el-form-item>
                <el-form-item label="默认">
                    <el-switch v-model="getModelData(item.label).default" active-value="1"
                               inactive-value="0" active-text="开启" inactive-text="关闭"
                               @change="handleDefaultChange(item.label)">
                    </el-switch>
                    <div style="color: #909399;">只能有一个模型是默认开启</div>
                </el-form-item>
                <el-form-item label="仅使用">
                    <el-switch v-model="getModelData(item.label).only" active-value="1"
                               inactive-value="0" active-text="开启" inactive-text="关闭"
                               @change="handleUseChange(item.label)">
                    </el-switch>
                    <div style="color: #909399;">选择开启，用户端不展示模型信息，所有生成都调用当前模型</div>
                </el-form-item>
            </el-tab-pane>
        </el-tabs>
    </div>
</template>

<script>
    // 积分换算说明组件
    Vue.component('integral-alert', {
        template: '#integral-alert',
        props: {
            form: Object
        }
    });

    // 服务提示组件
    Vue.component('service-alert', {
        template: '#service-alert',
        props: {
            serviceUrl: String,
            serviceButtonText: {
                type: String,
                default: '开通'
            },
            serviceDescription: String,
            showKeyButton: {
                type: Boolean,
                default: false
            }
        },
        methods: {
            openServiceLink() {
                window.open(this.serviceUrl, '_blank');
            }
        }
    });

    // 密钥管理组件
    Vue.component('key-management', {
        template: '#key-management',
        props: {
            form: Object,
            keys: Array,
            keyType: String,
            keyTitle: String,
            keyLabel: String,
            showApiKey: {
                type: Boolean,
                default: false
            },
            apiKeyUrl: String
        },
        computed: {
            filteredKeys() {
                return this.keys.filter(key => key.type === this.keyType);
            }
        },
        methods: {
            goToAddKey() {
                window.open('?r=netb/index/volcengine', '_blank');
            },
            refreshKeys() {
                this.$emit('refresh-keys');
            },
            goToApiKey() {
                if (this.apiKeyUrl) {
                    window.open(this.apiKeyUrl, '_blank');
                }
            }
        }
    });

    // 图片生成计费组件
    Vue.component('image-pricing', {
        template: '#image-pricing',
        props: {
            form: Object
        }
    });

    Vue.component('image-open', {
        template: '#image-open',
        props: {
            form: Object
        }
    });

    Vue.component('video-open', {
        template: '#video-open',
        props: {
            form: Object,
            models: Array,
        },
        computed: {
            // 获取或创建 video_model 对象的计算属性（改为对象存储，key为model的value）
            videoModelObject() {
                if (!this.form.video_model) {
                    this.$set(this.form, 'video_model', {});
                }
                return this.form.video_model;
            }
        },
        methods: {
            // 根据模型的value获取对应的数据对象
            getModelData(modelValue) {
                const key = modelValue;

                // 找到对应的模型信息
                const modelInfo = this.getModelInfo(key);

                // 确保该模型的数据存在
                if (!this.videoModelObject[key]) {
                    this.$set(this.videoModelObject, key, {
                        name: modelInfo ? modelInfo.label : '',
                        pic: '',
                        open: '1',
                        default: '0',
                        only: '0'
                    });
                }

                return this.videoModelObject[key];
            },

            // 根据模型的value获取对应的数据对象
            getModelInfo(modelValue) {
                // 找到对应的模型信息
                return this.models.find(m => m.label === modelValue);
            },

            // 处理默认模型切换，确保只有一个模型是默认的
            handleDefaultChange(currentModelValue) {
                const currentData = this.getModelData(currentModelValue);

                if (currentData.default === '1') {
                    // 如果当前模型设为默认，则其他所有模型取消默认
                    Object.keys(this.videoModelObject).forEach(key => {
                        if (key !== currentModelValue && this.videoModelObject[key].default === '1') {
                            this.$set(this.videoModelObject[key], 'default', '0');
                        }
                    });
                }
            },

            // 处理
            handleUseChange(currentModelValue) {
                const currentData = this.getModelData(currentModelValue);

                if (currentData.only === '1') {
                    // 如果当前模型设为默认，则其他所有模型取消默认
                    Object.keys(this.videoModelObject).forEach(key => {
                        if (key !== currentModelValue && this.videoModelObject[key].only === '1') {
                            this.$set(this.videoModelObject[key], 'only', '0');
                        }
                    });
                }
            }
        }
    });

    // 视频生成计费组件
    Vue.component('video-pricing', {
        template: '#video-pricing',
        props: {
            form: Object,
            models: Array,
            useTable: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {};
        },
        computed: {
            // 从models数据中提取所有分辨率和比例组合
            dynamicPricingData() {
                if (!this.models || this.models.length === 0) {
                    return {
                        resolutions: [],
                        ratios: [],
                        modelResolutions: []
                    };
                }

                const allResolutions = new Set();
                const modelResolutions = [];
                const allRatios = {};

                this.models.forEach(model => {
                    if (model.resolution_details) {
                        const modelData = {
                            label: model.label,
                            resolutions: {}
                        };

                        Object.keys(model.resolution_details).forEach(resolution => {
                            allResolutions.add(resolution);
                            modelData.resolutions[resolution] = model.resolution_details[resolution] || [];

                            if (Array.isArray(model.resolution_details[resolution])) {
                                model.resolution_details[resolution].forEach(ratio => {
                                    const [num, denom] = ratio.split(':');
                                    const key = `${Math.max(num, denom)}:${Math.min(num, denom)}`; // 保留最大值
                                    allRatios[key] = key; // 存储原始比例
                                });
                            }
                        });

                        modelResolutions.push(modelData);
                    }
                });

                return {
                    resolutions: Array.from(allResolutions).sort((a, b) => {
                        const order = { '480p': 1, '720p': 2, '1080p': 3 };
                        return (order[a] || 999) - (order[b] || 999);
                    }),
                    ratios: Object.keys(allRatios).map(key => allRatios[key]),
                    modelResolutions: modelResolutions
                };
            },

            // 生成表格行数据
            tableData() {
                const { resolutions, modelResolutions } = this.dynamicPricingData;
                const tableRows = [];

                modelResolutions.forEach(model => {
                    resolutions.forEach(resolution => {
                        if (model.resolutions[resolution] && model.resolutions[resolution].length > 0) {
                            tableRows.push({
                                modelLabel: model.label,
                                resolution: resolution.toUpperCase(),
                                resolutionKey: resolution,
                                supportedRatios: model.resolutions[resolution],
                                formKeyPrefix: `${model.label}_${resolution}`
                            });
                        }
                    });
                });

                return tableRows;
            }
        },
        methods: {
            // 获取表单字段名
            getFormFieldName(rowData, ratio) {
                const ratioKey = ratio.replace(':', '_');
                return `${rowData.formKeyPrefix}_${ratioKey}`;
            },

            // 检查该模型-分辨率是否支持某个比例
            supportsRatio(rowData, ratio) {
                return rowData.supportedRatios.includes(ratio);
            }
        }
    });

    // 共享的 Mixin
    const visualSettingMixin = {
        props: {
            keys: {
                type: Array,
                default: () => []
            }
        },
        data() {
            return {
                form: {},
                default: {},
                models: [],
                rules: {}
            };
        },
        methods: {
            refreshKeys() {
                this.$emit('refresh-keys');
            }
        }
    };

    /*
     * 视频价格组件使用示例：
     * 
     * <video-pricing 
     *     :form="form" 
     *     :models="models" 
     *     :use-table="true">
     * </video-pricing>
     * 
     * models 数据结构示例：
     * [
     *   {
     *     "label": "豆包-Seendance-1.0-pro-250528",
     *     "resolution_details": {
     *       "480p": ["1:1", "3:4", "4:3", "9:16", "16:9"],
     *       "720p": ["1:1", "3:4", "4:3", "9:16", "16:9"], 
     *       "1080p": ["1:1", "3:4", "4:3", "9:16", "16:9"]
     *     }
     *   },
     *   {
     *     "label": "Artsdance-Pro",
     *     "resolution_details": {
     *       "480p": ["1:1", "3:4", "4:3", "9:16"],
     *       "720p": ["1:1", "3:4", "4:3", "9:16"],
     *       "1080p": ["1:1", "3:4", "4:3", "9:16"]
     *     }
     *   }
     * ]
     * 
     * 表单字段命名规则：
     * {model_name}_{resolution}_{ratio}
     * 例如：doubao_seendance_1_0_pro_250528_480p_1_1
     * 
     * 表格会自动生成以下结构：
     * - 模型列：显示模型名称
     * - 分辨率列：显示480P/720P/1080P
     * - 比例列：动态生成所有支持的比例列（1:1, 3:4, 4:3, 9:16, 16:9, 21:9等）
     * - 价格输入框：仅在该模型-分辨率组合支持对应比例时显示，否则显示"不支持"
     */
</script>