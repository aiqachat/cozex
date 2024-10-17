<?php

if (file_exists(__DIR__ . '/install.lock')) {
    header('location: web/index.php');
} else {
    header('location: web/index.php?r=install');
}