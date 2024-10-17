<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2019/2/19 11:21
 */


namespace app\forms\attachment;


use app\bootstrap\response\ApiCode;
use app\models\AttachmentGroup;
use app\models\Model;

class GroupUpdateForm extends Model
{
    public $id;
    public $name;
    public $type;

    public function rules()
    {
        return [
            [['name',], 'trim'],
            [['name'], 'required'],
            [['id', 'type'], 'integer',],
            [['name',], 'string', 'max' => 64,],
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse($this);
        }
        $model = AttachmentGroup::findOne([
            'id' => $this->id,
            'is_delete' => 0,
        ]);
        if (!$model) {
            $model = new AttachmentGroup();
            $model->type = $this->type;
        }
        $model->name = $this->name;
        if (!$model->save()) {
            return $this->getErrorResponse($model);
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '保存成功。',
            'data' => $model,
        ];
    }
}
