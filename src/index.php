<?php

if (!file_exists(__DIR__ . '/install.lock') && !file_exists(__DIR__ . '/config/db.php')) {
    header('location: web/wsroot.php?r=install');
}