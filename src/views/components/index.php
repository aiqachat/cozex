<?php
/**
 * author: chenzs
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/11/14 13:52
 */
$components = [
    'app-attachment',
    'app-gallery',
    'app-image',
    'app-ellipsis',
    'app-upload',
    'app-image-upload',
    'app-new-export-dialog-2',
];
$html = "";
foreach ($components as $component) {
    $html .= $this->renderFile(__DIR__ . "/{$component}.php") . "\n";
}
echo $html;
