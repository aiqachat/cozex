<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common;

use app\models\Option;

class CommonOption
{
    const GROUP_ADMIN = 'admin';
    const GROUP_APP = 'app';

    const NAME_IND_SETTING = 'ind_setting'; // 设置
    const NAME_COZE_WEB_SDK = 'coze_web_sdk'; // coze web设置
    const NAME_VERSION = 'version'; // 更新记录的系统版本号

    private static $loadedOptions = [];

    /**
     * @param $name string Name
     * @param $value mixed Value
     * @param $group string Name
     * @return boolean
     */
    public static function set($name, $value, $group = '')
    {
        if (empty($name)) {
            return false;
        }
        $model = Option::findOne([
            'name' => $name,
            'group' => $group,
        ]);
        if (!$model) {
            $model = new Option();
            $model->name = $name;
            $model->group = $group;
        }
        $model->value = \Yii::$app->serializer->encode($value);
        $result = $model->save();
        if ($result) {
            $loadedOptionKey = md5(json_encode([
                'name' => $name,
                'group' => $group,
            ]));
            self::$loadedOptions[$loadedOptionKey] = $value;
        }
        return $result;
    }

    /**
     * @param $name string Name
     * @param $group string Name
     * @param $default string Name
     * @return null|array|string|object
     */
    public static function get($name, $group = '', $default = null)
    {
        $loadedOptionKey = md5(json_encode([
            'name' => $name,
            'group' => $group,
        ]));
        if (array_key_exists($loadedOptionKey, self::$loadedOptions)) {
            return self::$loadedOptions[$loadedOptionKey];
        }
        $model = Option::findOne([
            'name' => $name,
            'group' => $group
        ]);

        if (!$model) {
            $result = $default;
        } else {
            $result = \Yii::$app->serializer->decode($model->value);
        }
        self::$loadedOptions[$loadedOptionKey] = $result;
        return $result;
    }

    /**
     * @param $list
     * @param string $group
     * @return bool
     */
    public static function setList($list, $group = '')
    {
        if (!is_array($list)) {
            return false;
        }
        foreach ($list as $item) {
            self::set(
                $item['name'],
                $item['value'],
                ($item['group'] ?? $group)
            );
        }
        return true;
    }

    /**
     * @param $names
     * @param string $group
     * @param null $default
     * @return array
     */
    public static function getList($names, $group = '', $default = null)
    {
        if (is_string($names)) {
            $names = explode(',', $names);
        }
        if (!is_array($names)) {
            return [];
        }
        $list = [];
        foreach ($names as $name) {
            if (empty($name)) {
                continue;
            }
            $value = self::get($name, $group, $default);
            $list[$name] = $value;
        }
        return $list;
    }

    /**
     * @param array $data
     * @param array $default
     * @return array
     * 处理新增的默认数据
     */
    public static function checkDefault($data, $default, $unset = true)
    {
        foreach ($default as $key => $item) {
            if (!isset($data[$key])) {
                $data[$key] = $item;
                continue;
            }
            if (is_array($item)) {
                $data[$key] = self::checkDefault($data[$key], $item);
            }
        }
        if($unset){
            $data = array_intersect_key($data, $default);
        }
        return array_map(function ($item) {
            return is_numeric($item) ? (int)$item : $item;
        }, $data);
    }
}
