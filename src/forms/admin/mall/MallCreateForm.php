<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/11/15 11:57
 */

namespace app\forms\admin\mall;

use app\bootstrap\response\ApiCode;
use app\forms\common\CommonUser;
use app\models\Mall;
use app\models\Model;
use app\models\User;

class MallCreateForm extends Model
{
    public $id;
    public $name;
    public $expired_at;

    public function rules()
    {
        return [
            [['name'], 'trim'],
            [['name', 'expired_at'], 'required'],
            [['name'], 'string', 'max' => 64],
            [['id',], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => '商城名称',
            'expired_at' => '商城有效期'
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse($this);
        }
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        if ($this->id) {
            $model = Mall::findOne($this->id);
        } else {
            $adminInfo = CommonUser::getAdminInfo('app_max_count');
            $count = Mall::find()->where([
                'user_id' => $user->id,
                'is_delete' => 0,
            ])->count();
            if ($adminInfo->app_max_count >= 0 && $count >= $adminInfo->app_max_count) {
                return [
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => '超出创建最大数量',
                ];
            }
            $model = new Mall();
            $model->user_id = $user->id;
        }
        $model->attributes = $this->attributes;
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
