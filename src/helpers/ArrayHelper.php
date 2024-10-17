<?php
/**
 * Created by PhpStorm
 * User: wstianxia
 * Date: 2020/9/3
 * Time: 3:48 下午
 * @copyright: ©2020 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\helpers;

class ArrayHelper extends \yii\helpers\ArrayHelper
{
    /**
     * @param $array
     * @param $keys
     * @param null $default
     * @return array|mixed|null
     * 批量删除键值
     */
    public static function removeList(&$array, $keys, $default = null)
    {
        if (!is_array($keys)) {
            return $default;
        }
        $value = [];
        while (count($keys) > 1) {
            $key = array_shift($keys);
            $value[] = self::remove($array, $key, $default);
        }
        return $value;
    }

    public static function filter($array, $filters)
    {
        if (empty($filters)) {
            return $array;
        } else {
            return parent::filter($array, $filters);
        }
    }

    /**
     * @param $string
     * @param string $key
     * @param bool $decode
     * @param int $expiry
     * @return false|string
     * @czs 加密解密
     */
    public static function authCode($string, $key = '', $decode = true, $expiry = 0) {
        $length = 4;
        $key = md5($key);
        $key1 = md5(substr($key, 0, 16));
        $key2 = md5(substr($key, 16, 16));
        $key3 = $decode ? substr($string, 0, $length) : substr(md5(microtime()), -$length);

        $crypt_key = $key1 . md5($key1 . $key3);
        $key_length = strlen($crypt_key);

        $string = $decode ? base64_decode(substr($string, $length)) :
            sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $key2), 0, 16) . $string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rnd_key = array();
        for ($i = 0; $i <= 255; $i++) {
            $rnd_key[$i] = ord($crypt_key[$i % $key_length]);
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rnd_key[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if ($decode) {
            if (
                (substr($result, 0, 10) == 0 || intval(substr($result, 0, 10)) - time() > 0) &&
                substr($result, 10, 16) == substr(md5(substr($result, 26) . $key2), 0, 16)
            ) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $key3 . str_replace('=', '', base64_encode($result));
        }
    }

    /**
     * 将短字符串转为数字
     * @param string $string
     * @return int 数字
     */
    public static function getNum($string){
        $codes = "0ghijklmnopvwqrstuxyz12ST3459ABCDJKL6cd78MNOPQRUVWEFGHIXYZabef";
        $num = 0;
        for ($i = 0; $i < strlen($string); $i++) {
            $n = strlen($string) - $i - 1;
            $pos = strpos($codes, $string[$i]);
            $num += $pos * pow(62, $n);
        }
        return $num;
    }

    /**
     * 将数字转为短字符串
     * @param int $number 数字
     * @return string 短字符串
     */
    public static function generateCode($number){
        $out = "";
        $codes = "0ghijklmnopvwqrstuxyz12ST3459ABCDJKL6cd78MNOPQRUVWEFGHIXYZabef";
        while ($number > 61) {
            $m = $number % 62;
            $out = $codes[$m] . $out;
            $number = ($number - $m) / 62;
        }
        return $codes[$number] . $out;
    }
}
