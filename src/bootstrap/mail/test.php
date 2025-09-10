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
    <div style="text-align: center;">
        <p>这是一条测试邮件信息</p>
    </div>
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
