<?php
/**
 * @var \omnilight\scheduling\Schedule $schedule
 */

$schedule->command('index/del-attachment')->everyMinute();
$schedule->command('index/del-data')->dailyAt("00:01");