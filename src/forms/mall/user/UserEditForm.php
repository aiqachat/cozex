<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\user;

use app\bootstrap\response\ApiCode;
use app\models\Model;
use app\models\User;

class UserEditForm extends Model
{
    public $id;

    public $is_blacklist;
    public $remark;
    public $contact_way;
    public $remark_name;

    public function rules()
    {
        return [
            [['is_blacklist', 'id'], 'integer'],
            [['contact_way', 'remark', 'remark_name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'is_blacklist' => '是否黑名单',
            'contact_way' => '联系方式',
            'remark' => '备注',
            'remark_name' => '备注名',
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        };

        /* @var User $form */
        $form = User::find()->alias('u')
            ->with('identity')
            ->with('userInfo')
            ->where(['u.id' => $this->id])
            ->one();

        if (!$form) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '数据为空'
            ];
        }
        $form->userInfo->is_blacklist = $this->is_blacklist;
        $form->userInfo->remark = $this->remark;
        $form->userInfo->contact_way = $this->contact_way;
        $form->userInfo->remark_name = $this->remark_name;


        $t = \Yii::$app->db->beginTransaction();

        try {
            if (!$form->identity->save()) {
                throw new \Exception($this->getErrorMsg($form->identity));
            }

            if (!$form->userInfo->save()) {
                throw new \Exception($this->getErrorMsg($form->userInfo));
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
