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
        $this->errorMsg($response);
    }

    public function errorMsg($response)
    {
        \Yii::error($this->api->getUrl() . $this->getMethodName() . " = 对接火山引擎接口异常：");
        \Yii::error($response);
        $res = [
            2000 => '任务处理中。',
            1001 => '请求参数缺失必需字段 / 字段值无效 / 重复请求。',
            1002 => 'token 无效 / 过期 / 无权访问指定服务。',
            1010 => '音频数据时长超出阈值。',
            1011 => '音频数据大小超出阈值。',
            1012 => '音频 header 有误 / 无法进行音频解码。',
            1013 => '音频未识别出任何文本结果。',
            3001 => '无效的请求，一些参数的值非法或者无权限',
            3003 => '并发超限',
            3005 => '后端服务器负载高',
            3010 => '文本长度超限',
            3011 => '参数有误或者无效文本',
            3030 => '单次请求超过服务最长时间限制',
            3031 => '处理错误，文本过长或者超时',
            3032 => '等待获取音频超时',
            3040 => '后端链路连接错误',
            3050 => '音色不存在，检查使用的voice_type代号',

            1101 => '音频上传失败',
            1102=>'ASR（语音识别成文字）转写失败',
            1103 => 'SID声纹检测失败',
            1104 => '声纹检测未通过，声纹跟名人相似度过高',
            1105 => '获取音频数据失败',
            1106 => 'SpeakerID重复',
            1107 => 'SpeakerID未找到',
            1108 => '音频转码失败',
            1109 => 'wer检测错误，上传音频与请求携带文本对比字错率过高',
            1111 => 'aed检测错误，通常由于音频不包含说话声',
            1112 => 'SNR检测错误，通常由于信噪比过高',
            1113 => '降噪处理失败',
            1122 => '未检测到人声',
            1123 => '上传接口已经达到次数限制，目前同一个音色支持10次上传',
            1114 => '音频质量低，降噪失败',
        ];
        if(strpos($response['message'], 'resource not granted') !== false){
            throw new \Exception('无权限，请开通服务');
        }
        if(strpos($response['message'], 'quota exceeded for types') !== false){
            if(strpos($response['message'], 'text_words_lifetime') !== false){
                throw new \Exception('试用版用量用完了，需要开通正式版才能继续使用');
            }
            throw new \Exception('并发超过了限定值，需要减少并发调用情况或者增购并发');
        }
        if($response['message'] == 'extract request resource id: get resource id: access denied'){
//            throw new \Exception('语音合成未拥有当前音色授权，需要在控制台购买该音色才能调用');
            throw new \Exception(\Yii::t('voice', '音色维护中'));
        }
        throw new \Exception($res[$response['code'] ?? ''] ?? $response['message']);
    }
}
