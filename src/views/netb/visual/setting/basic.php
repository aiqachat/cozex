<template id="basic">
    <el-form @submit.native.prevent :model="form" :rules="rules" label-width="120px" :ref="formName">
        <!-- 积分换算说明 -->
        <integral-alert :form="form"></integral-alert>

        <!-- 服务提示 -->
        <service-alert service-url="https://console.volcengine.com/ai/ability/detail/10" service-button-text="立即开通"
            service-description="请先开通火山引擎AI服务后才能使用此功能，开通后即可获得相应的接口调用权限。" :show-key-button="true">
        </service-alert>

        <!-- 密钥管理 -->
        <key-management :form="form" :keys="keys" key-type="1" key-title="火山账号" key-label="火山账号"
            @refresh-keys="refreshKeys">
        </key-management>

        <!-- 图片生成计费 -->
        <image-pricing :form="form"></image-pricing>

        <!-- 视频生成计费 -->
        <video-pricing :form="form" :models="models" :use-table="true"></video-pricing>

        <video-open :form="form" :models="models"></video-open>
    </el-form>
</template>

<script>
    Vue.component('basic', {
        template: '#basic',
        mixins: [visualSettingMixin],
        props: {
            formName: String,
        },
        data() {
            return {
            };
        }
    });
</script>