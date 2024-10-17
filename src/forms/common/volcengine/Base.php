<?php
/**
 * @copyright ©2024
 * author: chenzs
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 */

namespace app\forms\common\volcengine;

use yii\base\Model;

class Base extends Model
{
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';

    function getMethod()
    {
        return self::METHOD_POST;
    }

    public function getMethodName(){
        return '';
    }

    public function getParams(){
        return $this->attributes;
    }

    public function getHeaders(){
        return [];
    }

    public function response($response){
        if(isset($response['code']) && $response['code'] == 0 && $response['message'] == 'Success'){
            return $response;
        }
        \Yii::error($this->getMethodName() . " = 对接火山引擎接口异常：");
        \Yii::error($response);
        $this->errorMsg($response);
    }

    public function errorMsg($response)
    {
        $res = [
            2000 => '任务处理中。',
            1001 => '请求参数缺失必需字段 / 字段值无效 / 重复请求。',
            1002 => 'token 无效 / 过期 / 无权访问指定服务。',
            1010 => '音频数据时长超出阈值。',
            1011 => '音频数据大小超出阈值。',
            1012 => '音频 header 有误 / 无法进行音频解码。',
            1013 => '音频未识别出任何文本结果。',
        ];
        throw new \Exception($res[$response['code'] ?? ''] ?? $response['message']);
    }
}
