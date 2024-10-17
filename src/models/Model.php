<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/10/30 12:00
 */


namespace app\models;

use app\bootstrap\response\ApiCode;

class Model extends \yii\base\Model
{
    /**
     * ActiveRecord 数据验证，返回第一条错误验证
     * @param array $model
     * @return array
     */
    public function getErrorResponse($model = [])
    {
        if (!$model) {
            $model = $this;
        }

        $msg = isset($model->errors) ? current($model->errors)[0] : '数据异常！';

        return [
            'code' => ApiCode::CODE_ERROR,
            'msg' => $msg
        ];
    }

    /**
     * ActiveRecord 数据验证，返回第一条错误验证
     * @param array $model
     * @return string
     */
    public function getErrorMsg($model = null)
    {
        if (!$model) {
            $model = $this;
        }
        $msg = isset($model->errors) ? current($model->errors)[0] : '数据异常！';
        return $msg;
    }

    public function success($data)
    {
        $res = $this->returnHandle($data);
        $res['code'] = ApiCode::CODE_SUCCESS;
        return $res;
    }

    public function fail($data)
    {
        $res = $this->returnHandle($data);
        $res['code'] = ApiCode::CODE_ERROR;
        return $res;
    }

    protected function returnHandle($data)
    {
        $msg = $data['msg'] ?? '';
        unset($data['msg']);
        return [
            'msg' => $msg,
            'data' => $data
        ];
    }

    /**
     * @param \Exception $exception
     * @return array
     */
    public function failByException($exception)
    {
        return $this->fail([
            'msg' => $exception->getMessage(),
            'line' => $exception->getLine(),
            'file' => $exception->getFile(),
            'errors' => $exception
        ]);
    }
}
