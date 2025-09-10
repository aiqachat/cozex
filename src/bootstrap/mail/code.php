<?php
/**
 * Created by PhpStorm.
 * User: wstianxia
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
<div class="container">
    <header style="background-color: black; display: flex; justify-content: center; align-items: center; padding: 10px;">
        <img src="<?= $logo ?>" style="width: 30px; height: auto; margin-right: 10px;border-radius: 50%;border: 2px solid #ffffff;" />
        <span style="color: white; font-size: 24px;"><?= $title ?? 'cozex' ?></span>
    </header>
    <h1><?=Yii::t('common', '尊敬的用户') ?>：</h1>
    <div style="text-align: center;">
        <h2><?=Yii::t('common', '验证码') ?></h2>
        <span style="font-size: 40px;"><strong><?= $code ?></strong></span>
    </div>
    <h4><?=Yii::t('common', '邮件注意') ?>！</h4>
    <p style="text-align: right;font-weight: 500;">
        <span style="padding-right: 10px;"><?= $title ?? 'cozex' ?></span><br/>
        <?= utc_time() ?>（UTC）
    </p>
    <hr/>
    <div>
        <?= $desc ?>
    </div>
</div>
</body>
</html>
