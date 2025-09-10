<?php

namespace app\forms\mall\level;

use app\bootstrap\response\ApiCode;
use app\models\Model;
use app\models\User;
use app\models\UserIdentity;
use app\models\UserLevel;

class LevelForm extends Model
{
    public $id;
    public $page;
    public $keyword;
    public $page_size;


    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['page'], 'default', 'value' => 1],
            [['page_size'], 'default', 'value' => 20],
            [['keyword'], 'string']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '会员ID',
        ];
    }

    public function getList()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $query = UserLevel::find()->where([
            'mall_id' => \Yii::$app->mall->id,
            'is_delete' => 0,
        ]);

        if ($this->keyword) {
            $query->andWhere(['like', 'name', $this->keyword]);
        }

        $list = $query->page($pagination, $this->page_size)->orderBy(['id' => SORT_DESC])->asArray()->all();

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '请求成功',
            'data' => [
                'list' => $list,
                'pagination' => $pagination
            ]
        ];
    }

    public function getDetail()
    {
        /** @var UserLevel $detail */
        $detail = UserLevel::find()->where([
            'id' => $this->id
        ])->one();
        $detail->language_data = $detail->language_data ? json_decode($detail['language_data'], true) : [];

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '请求成功',
            'data' => [
                'detail' => $detail,
            ]
        ];
    }

    public function destroy()
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $member = UserLevel::findOne(['mall_id' => \Yii::$app->mall->id, 'id' => $this->id]);

            if (!$member) {
                return [
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => '数据异常,该条数据不存在'
                ];
            }

            $member->is_delete = 1;
            $res = $member->save();
            if (!$res) {
                throw new \Exception($this->getErrorMsg($member));
            }

            $userIds = User::find()->where(['is_delete' => 0, 'mall_id' => \Yii::$app->mall->id])->select('id');
            $userIdentity = UserIdentity::find()->where([
                'user_id' => $userIds,
                'user_level' => $member->id,
                'is_delete' => 0
            ])->one();
            if ($userIdentity) {
                throw new \Exception('有用户属于该会员！无法删除');
            }

            $transaction->commit();
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '删除成功',
            ];

        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage(),
                'error' => [
                    'line' => $e->getLine(),
                    'msg' => $e->getMessage(),
                ]
            ];
        }
    }

    public function switchStatus()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        try {
            $member = UserLevel::findOne($this->id);
            if (!$member) {
                return [
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => '数据异常,该条数据不存在'
                ];
            }

//            if ($member->status) {
//                $userIds = User::find()->where(['is_delete' => 0, 'mall_id' => \Yii::$app->mall->id])->select('id');
//                $userIdentity = UserIdentity::find()->where([
//                    'user_id' => $userIds,
//                    'user_level' => $member->id,
//                    'is_delete' => 0
//                ])->one();
//                if ($userIdentity) {
//                    throw new \Exception('有用户属于该会员！无法禁用');
//                }
//            }

            $member->status = $member->status ? 0 : 1;
            $res = $member->save();
            if (!$res) {
                $this->getErrorMsg($member);
            }

            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '更新成功'
            ];
        } catch (\Exception $e) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage()
            ];
        }
    }

    public function setDefault()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        try {
            $member = UserLevel::findOne($this->id);
            if (!$member) {
                return [
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => '数据异常,该条数据不存在'
                ];
            }
            UserLevel::updateAll(['is_default' => 0], ['mall_id' => \Yii::$app->mall->id]);
            $member->is_default = 1;
            $res = $member->save();
            if (!$res) {
                $this->getErrorMsg($member);
            }

            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '更新成功'
            ];
        } catch (\Exception $e) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage()
            ];
        }
    }
}
