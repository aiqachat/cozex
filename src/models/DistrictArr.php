<?php
/**
 * @link:https://www.netbcloud.com/
 * @copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 *
 * Created by PhpStorm.
 * User: wstianxia
 * Date: 2018/12/4
 * Time: 9:33
 */

namespace app\models;

use yii\helpers\Json;

class DistrictArr
{
    public static function getArr()
    {
        return Json::decode(file_get_contents(__DIR__ . '/district.json'), true);
    }

    public static function getDiffCityDistrict($city_name)
    {
        $list = [];
        return isset($list[$city_name]) ? $list[$city_name] : null;
    }

    // 数组排序 先按parent_id正序再按id正序进行排列
    public static function getSort($arr)
    {
        uasort($arr, function ($a, $b) {
            $a_p = intval($a['parent_id']);
            $b_p = intval($b['parent_id']);
            if ($a_p == $b_p) {
                $a_id = intval($a['id']);
                $b_id = intval($b['id']);
                if ($a_id == $b_id) {
                    return 0;
                }
                return ($a_id < $b_id) ? -1 : 1;
            }
            return ($a_p < $b_p) ? -1 : 1;
        });
        echo "<pre>";
        var_export($arr);
        echo "</pre>";

        exit();
    }

    /**
     * 获取已父级id为$parent_id为根节点的树型结构数组
     * @param array $arr 省市区数据
     * @param string $level 不需要的数据的level，当前等级且包含其下级都排除
     * @return array
     */
    public static function getList(&$arr, $level = null)
    {
        $treeData = [];// 保存结果
        $catList = $arr;
        foreach ($catList as &$item) {
            if ($level && $item['level'] == $level) {
                continue;
            }
            $parent_id = $item['parent_id'];
            if (isset($catList[$parent_id]) && !empty($catList[$parent_id])) {// 肯定是子分类
                $catList[$parent_id]['list'][] = &$catList[$item['id']];
            } else {// 肯定是一级分类
                $treeData[] = &$catList[$item['id']];
            }
        }
        unset($item);
        return $treeData[0]['list'];
    }

    // 根据id获取信息
    public static function getDistrict($param)
    {
        if (is_array($param)) {
            $id = $param['id'];
        } else {
            $id = $param;
        }
        $arr = self::getArr();
        if (!isset($arr[$id])) {
            throw new \Exception('未找到省市区，请重新选择');
        }
        $list = $arr[$id];
        $str = \Yii::$app->serializer->encode($list);
        return \Yii::$app->serializer->decode($str);
    }

    // 根据指定的key=>value查找需要的数组
    public static function getInfo($param)
    {
        $newParam = [];
        foreach ($param as $key => $value) {
            $newParam[0] = $key;
            $newParam[1] = $value;
        }
        $arr = self::getArr();
        $list = array_filter($arr, function ($v) use ($newParam) {
            return $v[$newParam[0]] == $newParam[1];
        });
        $str = \Yii::$app->serializer->encode($list);
        return \Yii::$app->serializer->decode($str);
    }

    // 运费规则、起送规则、包邮规则
    public static function getRules()
    {
        $arr = self::getArr();
        $empty = [];
        $emptyPointer = &$empty;
        $ok = false;
        foreach ($arr as $index => &$item) {
            if ($item['parent_id'] == 1) {
                $okCity = false;
                $data = [
                    'id' => $item['id'],
                    'name' => $item['name']
                ];
                $data['show'] = false;
                $data['city'] = [];
                $dataPointer = &$data['city'];
                foreach ($arr as $key => $value) {
                    if ($value['parent_id'] == $index) {
                        $okCity = true;
                        $dataPointer[] = [
                            'id' => $value['id'],
                            'name' => $value['name'],
                            'show' => false
                        ];
                    }
                    if ($okCity && $value['parent_id'] != $index) {
                        break;
                    }
                }
                array_push($emptyPointer, $data);
                $ok = true;
            }
            if ($ok && $item['parent_id'] != 1) {
                break;
            }
        }

        return $empty;
    }

    // 微信获取地址
    public static function getWechatDistrict($province_name, $city_name, $county_name)
    {
        $arr = self::getArr();
        $ok = false;
        $res = [
            'code' => 0,
            'msg' => '',
            'data' => [
                'district' => [

                ]
            ]
        ];
        $county = [];
        foreach ($arr as $item) {
            if ($item['name'] == $county_name && $item['level'] == 'district') {
                $county = $item;
                $city = $arr[$item['parent_id']];
                if (isset($arr[$county['parent_id']]) && $city['name'] == $city_name) {
                    $province = $arr[$city['parent_id']];
                    if (isset($arr[$city['parent_id']]) && $province['name'] = $province_name) {
                        $ok = true;
                        break;
                    }
                }
            }
        }
        if (!$ok) {
            $diff_district = self::getDiffCityDistrict($city_name);
            $res['data']['district'] = [
                'province' => [
                    'id' => 3268,
                    'name' => '其他',
                ],
                'city' => [
                    'id' => 3269,
                    'name' => '其他',
                ],
                'district' => [
                    'id' => 3270,
                    'name' => '其他',
                ],
            ];
            if ($diff_district) {
                $res['data']['district'] = $diff_district;
            }
            return $res;
        }

        $res['data']['district'] = [
            'province' => [
                'id' => $province['id'],
                'name' => $province['name']
            ],
            'city' => [
                'id' => $city['id'],
                'name' => $city['name']
            ],
            'district' => [
                'id' => $county['id'],
                'name' => $county['name']
            ]
        ];

        return $res;
    }

    public static function getTerritorial()
    {
        $data = \Yii::$app->cache->get('territorial_list');
        if ($data) {
            return $data;
        }
        $arr = self::getArr();
        $treeData = [];// 保存结果
        $catList = &$arr;
        foreach ($catList as &$item) {
            $item['selected'] = false;
            $item['show'] = false;
            $parent_id = $item['parent_id'];
            if (isset($catList[$parent_id]) && !empty($catList[$parent_id])) {// 肯定是子分类
                $catList[$parent_id]['list'][] = &$catList[$item['id']];
            } else {// 肯定是一级分类
                $treeData[] = &$catList[$item['id']];
            }
        }
        unset($item);
        $data = $treeData[0]['list'];
        \Yii::$app->cache->set('territorial_list', $data);
        return $data;
    }
}