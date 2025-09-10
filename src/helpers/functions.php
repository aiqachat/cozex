<?php

/**
 * 强调：此处不要出现 use 语句！
 */

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param string $key
     * @param mixed $default
     * @param string $delimiter
     * @return mixed
     */
    function env($key, $default = null, $delimiter = '')
    {
        $value = getenv($key);

        if ($value === false) {
            return value($default);
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }

        if (strlen($value) > 1 && str_starts_with($value, '"') && str_ends_with($value, '"')) {
            $value = substr($value, 1, -1);
        }

        if (strlen($delimiter) > 0) {
            if (strlen($value) == 0) {
                $value = $default;
            } else {
                $value = explode($delimiter, $value);
            }
        }

        return $value;
    }
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param mixed $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (!function_exists('str_starts_with')) {
    /**
     * Determine if a given string starts with a given substring.
     *
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    function str_starts_with($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ($needle !== '' && substr($haystack, 0, strlen($needle)) === (string)$needle) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('str_ends_with')) {
    /**
     * Determine if a given string ends with a given substring.
     *
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    function str_ends_with($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if (substr($haystack, -strlen($needle)) === (string)$needle) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('define_once')) {
    /**
     * Define a const if not exists.
     *
     * @param string $name
     * @param mixed $value
     * @return bool
     */
    function define_once($name, $value = true)
    {
        return defined($name) or define($name, $value);
    }
}

if (!function_exists('dd')) {
    /**
     * Dump the passed variable and end the script.
     *
     * @param mixed $arg
     * @param bool $dumpAndDie
     * @return void
     */
    function dd($arg, $dumpAndDie = true)
    {
        echo "<pre>";
        // http_response_code(500);
        \yii\helpers\VarDumper::dump($arg);
        echo "</pre>";
        if ($dumpAndDie) {
            die(1);
        }
    }
}

if (!function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null)
    {
        $array = array();
        foreach ($input as $value) {
            if (!array_key_exists($columnKey, $value)) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            } else {
                if (!array_key_exists($indexKey, $value)) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if (!is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }
}

if (!function_exists('make_dir')) {
    /**
     * Create the directory by pathname
     * @param string $pathname The directory path.
     * @param int $mode
     * @return bool
     */
    function make_dir($pathname, $mode = 0777)
    {
        if (is_dir($pathname)) {
            return true;
        }
        if (is_dir(dirname($pathname))) {
            return mkdir($pathname, $mode);
        }
        make_dir(dirname($pathname));
        return mkdir($pathname, $mode);
    }
}

if (!function_exists('hump')) {
    /**
     * @param string $words
     * @param string $separator
     * @return string
     * 下划线转驼峰或者字符串第一个字母大写
     */
    function hump($words, $separator = '_')
    {
        if (strpos($words, $separator) !== false) {
            $newWords = str_replace($separator, " ", strtolower($words));
            return ltrim(str_replace(" ", "", ucwords($newWords)), $separator);
        } else {
            return ucfirst($words);
        }
    }
}

if (!function_exists('price_format')) {
    define_once('PRICE_FORMAT_FLOAT', 'float');
    define_once('PRICE_FORMAT_STRING', 'string');

    /**
     * @param $val
     * @param string $returnType PRICE_FORMAT_FLOAT|PRICE_FORMAT_STRING
     * @param int $decimals
     * @return float|string
     */
    function price_format($val, $returnType = 'string', $decimals = 2)
    {
        $val = floatval($val);
        $result = number_format($val, $decimals, '.', '');
        if ($returnType === PRICE_FORMAT_FLOAT) {
            return (float)$result;
        }
        return $result;
    }
}

if (!function_exists('mysql_timestamp')) {
    /**
     * 生成mysql数据库时间戳（eg. 2000-01-01 12:00:00）
     * @param integer $time
     * @return false|string
     */
    function mysql_timestamp($time = null)
    {
        if ($time === null) {
            $time = time();
        }
        return date('Y-m-d H:i:s', $time);
    }
}

if (!function_exists('get_supported_image_lib')) {
    /**
     * 获取支持的图片处理库
     * @return array
     * @throws Exception
     */
    function get_supported_image_lib()
    {
        switch (true) {
            case class_exists('\Imagick') && method_exists((new \Imagick()), 'setImageOpacity'):
                return ['Imagick'];
            case function_exists('gd_info'):
            default:
                return ['Gd'];
        }
        throw new Exception('找不到可处理图片的扩展，请检查PHP是否正确安装了GD或Imagick扩展。');
    }
}

if (!function_exists('table_exists')) {
    /**
     * 检查数据表是否存在
     * @param $tableName
     * @return bool
     * @throws \yii\db\Exception
     */
    function table_exists($tableName)
    {
        $sql = "SHOW TABLES LIKE '{$tableName}';";
        $result = Yii::$app->db->createCommand($sql)->queryAll();
        if (is_array($result) && count($result)) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('sql_execute')) {


    /**
     * 执行SQL
     * @param string $sql 要运行的SQL
     * @param bool $split 自动拆分SQL
     * @param bool $continueOnError 遇到错误继续执行
     * @throws Exception
     */
    function sql_execute($sql, $split = true, $continueOnError = true)
    {
        if ($split) {
            $list = SqlFormatter::splitQuery($sql);
        } else {
            $list = [$sql];
        }
        foreach ($list as $item) {
            try {
                Yii::$app->db->createCommand($item)->execute();
            } catch (Exception $exception) {
                if (!$continueOnError) {
                    throw $exception;
                }
            }
        }
    }
}

if (!function_exists('get_distance')) {
    /**
     * 求两个已知经纬度之间的距离,单位为米
     *
     * @param $lng1 Number 位置1经度
     * @param $lat1 Number 位置1纬度
     * @param $lng2 Number 位置2经度
     * @param $lat2 Number 位置2纬度
     * @return float 距离，单位米
     */
    function get_distance($lng1, $lat1, $lng2, $lat2)
    {
        // 将角度转为狐度
        $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
        return $s;
    }
}

if (!function_exists('remove_dir')) {
    /**
     * 删除文件夹
     * @param $dir
     * @return bool
     */
    function remove_dir($dir)
    {
        //先删除目录下的文件：
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;
                if (!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    remove_dir($fullpath);
                }
            }
        }

        closedir($dh);
        //删除当前文件夹：
        if (rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('is_point_in_polygon')) {
    /**
     * 判断一个坐标是否在一个多边形内（由多个坐标围成的）
     * 基本思想是利用射线法，计算射线与多边形各边的交点，如果是偶数，则点在多边形外，否则
     * 在多边形内。还会考虑一些特殊情况，如点在多边形顶点上，点在多边形边上等特殊情况。
     * @param array $point 指定点坐标
     * @param array $pts 多边形坐标 顺时针方向
     * @return bool
     */
    function is_point_in_polygon($point, $pts)
    {
        $N = count($pts);
        $boundOrVertex = true; //如果点位于多边形的顶点或边上，也算做点在多边形内，直接返回true
        $intersectCount = 0;//cross points count of x
        $precision = 2e-10; //浮点类型计算时候与0比较时候的容差
        $p1 = 0;//neighbour bound vertices
        $p2 = 0;
        $p = $point; //测试点

        $p1 = $pts[0];//left vertex
        for ($i = 1; $i <= $N; ++$i) {//check all rays
            // dump($p1);
            if ($p['lng'] == $p1['lng'] && $p['lat'] == $p1['lat']) {
                return $boundOrVertex;//p is an vertex
            }

            $p2 = $pts[$i % $N];//right vertex
            if ($p['lat'] < min($p1['lat'], $p2['lat']) || $p['lat'] > max($p1['lat'], $p2['lat'])) {
                //ray is outside of our interests
                $p1 = $p2;
                continue;//next ray left point
            }

            if ($p['lat'] > min($p1['lat'], $p2['lat']) && $p['lat'] < max($p1['lat'], $p2['lat'])) {
                //ray is crossing over by the algorithm (common part of)
                if ($p['lng'] <= max($p1['lng'], $p2['lng'])) {
                    //x is before of ray
                    if ($p1['lat'] == $p2['lat'] && $p['lng'] >= min($p1['lng'], $p2['lng'])) {
                        //overlies on a horizontal ray
                        return $boundOrVertex;
                    }

                    if ($p1['lng'] == $p2['lng']) {//ray is vertical
                        if ($p1['lng'] == $p['lng']) {//overlies on a vertical ray
                            return $boundOrVertex;
                        } else {//before ray
                            ++$intersectCount;
                        }
                    } else {//cross point on the left side
                        $xinters = ($p['lat'] - $p1['lat']) * ($p2['lng'] - $p1['lng']) / ($p2['lat'] - $p1['lat']) + $p1['lng'];//cross point of lng
                        if (abs($p['lng'] - $xinters) < $precision) {//overlies on a ray
                            return $boundOrVertex;
                        }

                        if ($p['lng'] < $xinters) {//before ray
                            ++$intersectCount;
                        }
                    }
                }
            } else {//special case when ray is crossing through the vertex
                if ($p['lat'] == $p2['lat'] && $p['lng'] <= $p2['lng']) {//p crossing over p2
                    $p3 = $pts[($i + 1) % $N]; //next vertex
                    if ($p['lat'] >= min($p1['lat'], $p3['lat']) && $p['lat'] <= max($p1['lat'], $p3['lat'])) {
                        //p.lat lies between p1.lat & p3.lat
                        ++$intersectCount;
                    } else {
                        $intersectCount += 2;
                    }
                }
            }
            $p1 = $p2;//next ray left point
        }

        if ($intersectCount % 2 == 0) {//偶数在多边形外
            return false;
        } else { //奇数在多边形内
            return true;
        }
    }
}

if (!function_exists('generate_order_no')) {
    /**
     * 生成 前缀+24位数字的订单号
     * @param string $prefix 前缀
     * @return string
     */
    function generate_order_no($prefix = '')
    {
        $randLen = 6;
        $id = base_convert(substr(uniqid(), 0 - $randLen), 16, 10);
        if (strlen($id) > 10) {
            $id = substr($id, -10);
        } elseif (strlen($id) < 10) {
            $rLen = 10 - strlen($id);
            $id = $id . rand(pow(10, $rLen - 1), pow(10, $rLen) - 1);
        }
        $dateTimeStr = date('YmdHis');
        return $prefix . $dateTimeStr . $id;
    }
}

if (!function_exists('filter_emoji')) {
    /**
     * 过滤emoji字符
     * @param $str
     * @return string|null
     */
    function filter_emoji($str)
    {
        $str = preg_replace_callback(
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str);

        return $str;
    }
}

if (!function_exists('file_uri')) {
    /**
     * @param $path
     * @return string[]
     * 获取指定文件夹的物理路径及网络路径
     */
    function file_uri($path)
    {
        $root = Yii::$app->basePath;
        if (!is_dir($root . $path)) {
            make_dir($root . $path);
        }
        if (Yii::$app instanceof \yii\web\Application) {
            $webRoot = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl;
        }else{
            $webRoot = Yii::$app->hostInfo . Yii::$app->baseUrl;
            cmd_exe("chown -R www:www " . $root . $path . " & chmod -R 755 " . $root . $path);
        }
        $webRoot = dirname($webRoot);
        $webUri = $webRoot . $path;
        return [
            'local_uri' => $root . $path,
            'web_uri' => $webUri
        ];
    }
}

if (!function_exists('web_url')) {
    function web_url()
    {
        if (Yii::$app instanceof \yii\web\Application) {
            $webRoot = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl;
        }else{
            $webRoot = Yii::$app->hostInfo . Yii::$app->baseUrl;
        }
        return $webRoot;
    }
}

if (!function_exists('space_unit')) {
    /**
     * 空间单位转换
     * @return string
     */
    function space_unit($value, $default = 0){
        $n = ["Byte", "KB", "MB", "GB", "TB"];
        if($value < 1024){
            return round($value, 2) . " {$n[$default]}";
        }
        $default++;
        return space_unit(round($value / 1024, 2), $default);
    }
}

if (!function_exists('cmd_exe')) {
    /**
     * 执行命令
     * @return array
     */
    function cmd_exe($command, $input = null, $file = ''){
        set_time_limit(0); // 取消脚本运行时间的超时上限
        ignore_user_abort(true); // 后台运行

        $descriptor_spec = array(
            0 => array("pipe", "r"),  // 标准输入，子进程从此管道中读取数据
            1 => array("pipe", "w"),  // 标准输出，重定向子进程输入到主进程STDOUT
        );
        if($file){
            $descriptor_spec[] = ["file", $file, "a"];
        }
        if(!function_exists( "proc_open")){
            throw new Exception('需在PHP函数里面开启proc_open', '220099321');
        }
        $proc = proc_open($command, $descriptor_spec, $pipes);
        // 检查 proc_open 是否成功
        if (!is_resource($proc)) {
            throw new Exception('proc_open failed');
        }
        if ($input) {
            fwrite($pipes[0], $input);
            fclose($pipes[0]);
        }
        // 读取标准输出
        if (isset($pipes[1]) && is_resource($pipes[1])) {
            $stdout = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
        } else {
            $stdout = '';
        }
        // 读取标准错误输出
        if (isset($pipes[2]) && is_resource($pipes[2])) {
            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
        } else {
            $stderr = '';
        }
        @proc_close($proc);
        return [$stdout, $stderr];
    }
}

if (!function_exists('app_version')) {
    /**
     * @return string
     */
    function app_version()
    {
        if (!class_exists('\Yii')) {
            return '0.0.0';
        }
        $versionFile = Yii::$app->basePath . '/version.json';
        if (!file_exists($versionFile)) {
            return '0.0.0';
        }
        $versionContent = file_get_contents($versionFile);
        if (!$versionContent) {
            return '0.0.0';
        }
        $versionData = json_decode($versionContent, true);
        if (!$versionData) {
            return '0.0.0';
        }
        return $versionData['version'] ?? '0.0.0';
    }
}

if (!function_exists('utc_time')) {
    /**
     * @return string
     */
    function utc_time($time = null)
    {
        $default = date_default_timezone_get();
        date_default_timezone_set('UTC');
        $utcNow = date('Y-m-d H:i:s', $time ?: time());
        date_default_timezone_set($default);
        return $utcNow;
    }
}
