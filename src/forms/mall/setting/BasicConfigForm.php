<?php
/**
 * Created by PhpStorm
 * User: chenzs
 * Date: 2020/9/29
 * Time: 4:15 下午
 * @copyright: ©2020 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\forms\mall\setting;

use app\bootstrap\response\ApiCode;
use app\forms\common\CommonOption;
use app\models\Model;

abstract class BasicConfigForm extends Model
{
    public $tab;
    public $formData;

    public function rules()
    {
        return [
            [['formData'], 'safe'],
            [['tab'], 'string'],
        ];
    }

    abstract public function getList();

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        try {
            $data = CommonOption::checkDefault($this->formData, $this->getDefault());
            $data = $this->afterHandle($data);
            CommonOption::set($this->getName(), $data, \Yii::$app->mall->id, CommonOption::GROUP_APP);
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '保存成功'
            ];
        }catch (\Exception $e){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage()
            ];
        }
    }

    public function get(){
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功',
            'data' => [
                'data' => $this->config(),
                'default' => $this->getDefault()
            ]
        ];
    }

    public function config()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $setting = CommonOption::get($this->getName(), \Yii::$app->mall->id, CommonOption::GROUP_APP);
        $setting = $setting ? (array)$setting : [];
        return $this->append(CommonOption::checkDefault($setting, $this->getDefault()));
    }

    public function getDefault()
    {
        return $this->{$this->tab}();
    }

    public function getName()
    {
        return $this->getList()[$this->tab];
    }

    public function afterHandle($data)
    {
        return $data;
    }

    public function append($data)
    {
        return $data;
    }
}
