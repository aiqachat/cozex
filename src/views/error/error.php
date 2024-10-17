<?php

/** @var \app\bootstrap\ErrorHandler $handler */

$result = $handler->getResult();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Error</title>
</head>
<body>
<?php dd($result, false); ?>
</body>
</html>
