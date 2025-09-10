<?php
/**
 * @copyright ©2024
 * author: chenzs
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 */

namespace app\forms\common\volcengine\ark;

/**
 * @property array $attribute
 */
class Base
{
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';

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
        return get_object_vars($this);
    }

    function getMethod()
    {
        return self::METHOD_POST;
    }

    public function getMethodName(){
        return '';
    }

    public function response($response){
        if(isset($response['code']) && $response['code'] == 0){
            return $response;
        }
        if(isset($response['error'])){
            \Yii::error($this->getMethodName() . " = 对接火山方舟异常结果：");
            \Yii::error($response['error']);
            $this->errorMsg($response['error']);
        }
        return $response;
    }

    public function errorMsg($response)
    {
        switch ($response['code'] ?? ''){
            case 'InvalidParameter':
                if($response['param'] == 'model'){
                    throw new \Exception('指定的大模型ID无效或未开通');
                }
                throw new \Exception('请求包含参数不正确');
            case 'ResourceNotFound':
                throw new \Exception('未查询到数据，请确认参数后重试');
            case 'InvalidParameter.UnsupportedImageFormat':
                throw new \Exception('不支持图像格式，请求失败');
            case 'ModelNotOpen':
                throw new \Exception('未开通对应模型服务');
            case 'InvalidEndpoint.NotFound':
                throw new \Exception('模型免费额度已用完或者模型无权访问');
            case 'InvalidEndpointOrModel.NotFound':
                throw new \Exception('模型不存在或者无权访问它');
            case 'InputTextSensitiveContentDetected':
                throw new \Exception('输入文本可能包含敏感信息，请更换后重试');
            case 'QuotaExceeded':
                if(strpos($response['message'], "has exhausted its free trial quota") !== false){
                    throw new \Exception('模型免费试用额度已消耗完毕');
                }
                throw new \Exception('请求数过多，请稍后重试');
        }
        if(strpos($response['code'], "SensitiveContentDetected") !== false){
            throw new \Exception('输入文本信息不合规');
        }
        throw new \Exception($response['message'] ?? '异常');
    }
}
