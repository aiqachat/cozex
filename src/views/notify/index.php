<!DOCTYPE html>
<html>
<head>
    <title>返回页面</title>
    <?php
    $message = $message ?? '';
    $jumpUrl = $jumpUrl ?? '';
   ?>
    <script>
        window.onload = function() {
            alert('<?php echo $message;?>');
            window.location.href = '<?php echo $jumpUrl;?>';
        };
    </script>
</head>
<body>
</body>
</html>