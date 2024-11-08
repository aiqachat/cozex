<?php
/**
 * @copyright ©2024
 * author: chenzs
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 */

namespace app\forms\common\volcengine;

/**
 * @property array $attribute
 */
class Base
{
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';

    /** @var ApiForm */
    protected $api;

    function getMethod()
    {
        return self::METHOD_POST;
    }

    public function getMethodName(){
        return '';
    }

    public function __construct($array = [])
    {
        $this->attribute = $array;
    }

    /**
     * @param $name
     * @param $value
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        }
    }

    public function setAttribute($array = [])
    {
        foreach ($array as $key => $item) {
            if (property_exists($this, $key)) {
                $this->$key = $item;
            }
        }
    }

    function getAttribute(): array
    {
        $params = get_object_vars($this);
        unset($params['api']);
        return $params;
    }

    public function getParams(){
        return $this->getAttribute();
    }

    public function setApi(ApiForm $api){
        $this->api = $api;
    }

    public function getApi(){
        return $this->api;
    }

    public function getHeaders(){
        return [];
    }

    public function response($response){
        if(isset($response['code']) && $response['code'] == 0 && $response['message'] == 'Success'){
            return $response;
        }
        \Yii::error(explode("?", $this->getMethodName())[0] . " = 对接火山引擎接口异常：");
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
            3001 => '一些参数的值非法',
            3005 => '后端服务器负载高',
            3010 => '单次请求超过设置的文本长度阈值',
            3011 => '参数有误或者文本为空、文本与语种不匹配、文本只含标点',
            3030 => '单次请求超过服务最长时间限制',
            3050 => '音色不存在，检查使用的voice_type代号',
        ];
        if(strpos($response['message'], 'resource not granted') !== false){
            throw new \Exception('无权限，请开通服务');
        }
        throw new \Exception($res[$response['code'] ?? ''] ?? $response['message']);
    }
}
