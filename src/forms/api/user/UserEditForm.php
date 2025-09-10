<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\api\user;

use app\bootstrap\response\ApiCode;
use app\models\AvData;
use app\models\Model;
use app\models\User;

class UserEditForm extends Model
{
    public $nickname;
    public $avatar;

    public function rules()
    {
        return [
            [['nickname'], 'string'],
            [['avatar'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'nickname' => '昵称',
            'avatar' => '头像',
        ];
    }

    public function update()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        };

        /* @var User $form */
        $form = User::find()->alias('u')
            ->where(['u.id' => \Yii::$app->user->id])
            ->one();

        if (!$form) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '用户不存在'
            ];
        }

        $t = \Yii::$app->db->beginTransaction();
        try {
            if($form->userInfo->avatar != $this->avatar) {
                $res = (new AvData())->localFile($form->userInfo->avatar, false);
                @unlink($res);
                $form->userInfo->avatar = $this->avatar;
            }
//            $form->userInfo->mobile = $this->mobile;
//            $form->userInfo->email = $this->email;
            $form->nickname = $this->nickname;

            if (!$form->userInfo->save()) {
                throw new \Exception($this->getErrorMsg($form->userInfo));
            }

            if (!$form->save()) {
                throw new \Exception($this->getErrorMsg($form));
            }

            $t->commit();
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '保存成功'
            ];
        } catch (\Exception $e) {
            $t->rollBack();
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage()
            ];
        }
    }
}
