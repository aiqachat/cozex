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
    <title><?= $title ?? 'cozex' ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            border: 1px solid #e0e0e0;
        }
        .header {
            background-color: #e62129;
            color: white;
            padding: 10px 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px;
        }
        .footer {
            text-align: right;
            padding: 10px 20px;
            color: #e62129;
            font-size: 14px;
        }
        .time {
            text-align: right;
            padding: 0 20px 20px 0;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?= $title ?? 'cozex' ?></h1>
        </div>
        <div class="content">
            <p>尊敬的用户:</p>
            <p>验证码为 <?= $code ?></p>
            <p>如非本人操作，请忽略！</p>
        </div>
        <div class="footer">
            <div><?= $title ?? 'cozex' ?></div>
        </div>
        <div class="time">
            <?= date('Y-m-d H:i:s') ?>
        </div>
    </div>
</body>
</html>