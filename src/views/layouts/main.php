<?php

use app\forms\mall\setting\ConfigForm;

$indSetting = (new ConfigForm())->config();
$siteName = $indSetting['name'];
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="keywords" content=""/>
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=0">
    <title><?= $siteName ?></title>
    <link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ?>/statics/unpkg/element-ui@2.12.0/lib/theme-chalk/index.css">
    <link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ?>/statics/css/flex.css">
    <link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ?>/statics/css/common.css">
    <link href="<?= Yii::$app->request->baseUrl ?>/../favicon.ico" mce_href="<?= Yii::$app->request->baseUrl ?>/../favicon.ico" rel="shortcut icon"/>
    <script src="<?= Yii::$app->request->baseUrl ?>/statics/unpkg/jquery@3.3.1/dist/jquery.min.js"></script>
    <script src="<?= Yii::$app->request->baseUrl ?>/statics/unpkg/vue@2.6.10/dist/vue.js"></script>
    <script src="<?= Yii::$app->request->baseUrl ?>/statics/unpkg/element-ui@2.12.0/lib/index.js"></script>
    <script src="<?= Yii::$app->request->baseUrl ?>/statics/unpkg/qs@6.5.2/dist/qs.js"></script>
    <script src="<?= Yii::$app->request->baseUrl ?>/statics/unpkg/axios@0.18.0/dist/axios.min.js"></script>
    <script src="<?= Yii::$app->request->baseUrl ?>/statics/unpkg/vue-line-clamp@1.2.4/dist/vue-line-clamp.umd.js"></script>
    <script>
        let _layout = null;
        let _csrf = '<?=Yii::$app->request->csrfToken?>';
        const _scriptUrl = '<?=Yii::$app->request->scriptUrl?>';
        const _baseUrl = '<?= \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl ?>';
        setInterval(function () {
            _csrf = '<?=Yii::$app->request->csrfToken?>';
        }, 3600000);
    </script>
    <?php if (!empty($indSetting['passport_logo'])) : ?>
        <script>let _siteLogo = '<?=$indSetting['passport_logo']?>';</script>
    <?php else : ?>
        <script>let _siteLogo = _baseUrl + '/statics/img/admin/login-logo.png';</script>
    <?php endif; ?>
    <script src="<?= Yii::$app->request->baseUrl ?>/statics/js/common.js"></script>
    <style>
        html, body {
            height: 100%;
            padding: 0;
            margin: 0;
        }

        #app {
            height: 100%;
        }

        [v-cloak] {
            display: none !important;
        }
    </style>

</head>
<body>
<?php $this->beginBody() ?>
<div id="_layout"></div>
<?= $this->renderFile('@app/views/components/index.php') ?>
<?= $content ?>
<script>
    _layout = new Vue({
        el: '#_layout',
        created() {
            setInterval(() => {
                this.$request({
                    params: {
                        r: 'keep-alive/index'
                    }
                }).then(e => {
                }).catch(e => {
                });
            }, 1000 * 60 * 5);
        }
    });
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
