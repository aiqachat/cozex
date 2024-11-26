<?php
/**
 * @copyright ©2024
 * author: chenzs
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 */

namespace app\forms\common\coze;

/**
 * @property array $attribute
 */
class Base
{
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';
    const METHOD_UPLOAD = 'UPLOAD';

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
        \Yii::error($this->getMethodName() . " = 对接扣子异常结果：");
        \Yii::error($response);
        $this->errorMsg($response);
    }

    public function errorMsg($response)
    {
        $res = [
            4000 => '请求参数错误，包括参数格式错误、必选参数缺失等',
            4001 => '指定对话不存在。包括 chat id 错误、当前账号无此 chat 的权限等。',
            4002 => '指定会话不存在。包括 conversation id 错误、当前账号无此 conversation 的权限等。',
            4003 => 'meta_data 字段的传参超出字段限制',
            4005 => '指定消息不存在。包括 message id 错误、当前账号无此 message 的权限、content 内容不符合要求等。',
            4006 => '指定 Bot 不存在。包括 Bot id 错误、当前账号无此 Bot 的权限等。',
            4008 => '当日 Bot 使用次数超过限制',
            4009 => '当前使用人数过多',
            4011 => '当前账户的 Coze Token 余额不足',
            4014 => 'Bot 无法回答此问题',
            4019 => '扣子专业版账号已欠费',
            4020 => '当前 RPM 已超出购买的额度',
            4100 => '访问令牌（secret）不正确',
            4101 => '当前使用的个人访问令牌没有权限访问该资源',
            4200 => '未找到该资源，包括资源 ID 错误、此资源并非由当前账号创建等。',
            4302 => '待上传的文件大小超出接口限制',
            4303 => '文件类型不支持',
            4304 => '文件无效',
            5000 => '服务器内部错误',
            708232003 => '当前使用的个人访问令牌未被授予知识库所在空间的权限',
            708232001 => '请求参数错误，包括参数格式错误、必选参数缺失等',
            700012006 => '访问令牌（secret）不正确',
        ];
        throw new \Exception($res[$response['code'] ?? ''] ?? ($response['msg'] ?? $response['err_msg']));
    }
}
