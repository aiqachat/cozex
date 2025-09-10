<template id="arkGlobal">
    <el-form @submit.native.prevent :model="form" :rules="rules" label-width="120px" :ref="formName">
        <!-- 积分换算说明 -->
        <integral-alert :form="form"></integral-alert>

        <!-- 服务提示 -->
        <service-alert
            service-url="https://console.byteplus.com/ark/region:ark+ap-southeast-1/openManagement?LLM=%7B%7D&tab=ComputerVision"
            service-description="请先开通火山方舟模型服务后才能使用此功能，开通后即可获得相应的接口调用权限。">
        </service-alert>

        <!-- 密钥管理 -->
        <key-management :form="form" :keys="keys" key-type="2" key-title="Byteplus、API Key管理" key-label="Byteplus账号"
            :show-api-key="true"
            api-key-url="https://console.byteplus.com/ark/region:ark+ap-southeast-1/apiKey?apikey=%7B%7D"
            @refresh-keys="refreshKeys">
        </key-management>

        <!-- 图片生成计费 -->
        <image-pricing :form="form"></image-pricing>

        <image-open :form="form"></image-open>

        <!-- 视频生成计费 -->
        <video-pricing :form="form" :models="models" :use-table="true"></video-pricing>

        <video-open :form="form" :models="models"></video-open>
    </el-form>
</template>

<script>
    Vue.component('arkGlobal', {
        template: '#arkGlobal',
        mixins: [visualSettingMixin],
        props: {
            formName: String,
        },
        data() {
            return {
                form: {
                    type: 1
                }
            };
        }
    });
</script>