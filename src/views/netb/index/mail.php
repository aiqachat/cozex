<?php
/**
 * @link:https://www.netbcloud.com/
 * @copyright: Copyright (c) 2018 深圳网商天下科技有限公司有限公司
 *
 * Created by PhpStorm.
 * Date: 2018/12/8
 * Time: 14:01
 */
Yii::$app->loadViewComponent('app-mail-setting');
?>
<div id="app" v-cloak>
    <el-card shadow="never" style="border:0" body-style="background-color: #f3f3f3;padding: 10px 0 0;">
        <div slot="header">
            <span>邮件管理（QQ邮箱）</span>
        </div>
        <app-mail-setting></app-mail-setting>
    </el-card>
</div>
<script>
    const app = new Vue({
        el: '#app',
    });
</script>
