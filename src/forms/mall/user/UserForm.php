<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\user;

use app\bootstrap\response\ApiCode;
use app\models\BalanceLog;
use app\models\IntegralLog;
use app\models\Model;
use app\models\User;
use app\models\UserIdentity;
use app\models\UserInfo;

class UserForm extends Model
{
    public $password;
    public $keyword;
    public $field;
    public $id;
    public $sort;
    public $user_id;
    public $status;
    public $start_date;
    public $end_date;

    public function rules()
    {
        return [
            [['password', 'start_date', 'end_date', 'field', 'sort'], 'string'],
            [['id', 'user_id', 'status'], 'integer'],
            [['keyword'], 'string', 'max' => 255],
        ];
    }

    public function getList()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        };
        $query = User::find()->alias('u')->where([
            'u.is_delete' => 0,
            'u.mall_id' => \Yii::$app->mall->id,
        ])->InnerJoin([
            'i' => UserInfo::tableName(),
        ], 'u.id = i.user_id')
            ->InnerJoin([
                'd' => UserIdentity::tableName(),
            ], 'd.user_id = u.id');

        if($this->field){
            switch ($this->field){
                case 'uid':
                    $query->andWhere(['like', 'u.uid', $this->keyword]);
                    break;
                case 'nickname':
                    $query->andWhere(['like', 'BINARY(u.nickname)', $this->keyword]);
                    break;
                case 'mobile':
                    $query->andWhere(['like', 'i.mobile', $this->keyword]);
                    break;
                case 'email':
                    $query->andWhere(['like', 'i.email', $this->keyword]);
                    break;
            }
        }
        if($this->sort){
            $pos = strrpos($this->sort, "_");
            switch (substr($this->sort, 0, $pos)){
                case 'created_at':
                    $query->orderBy ('u.created_at ' . substr($this->sort, $pos + 1));
                    break;
                case 'uid':
                    $query->orderBy ('u.uid ' . substr($this->sort, $pos + 1));
                    break;
                case 'balance':
                    $query->orderBy ('i.balance ' . substr($this->sort, $pos + 1));
                    break;
                case 'integral':
                    $query->orderBy ('i.integral ' . substr($this->sort, $pos + 1));
                    break;
            }
        }else{
            $query->orderBy ('u.id DESC');
        }
        if($this->status !== null){
            $query->andWhere(['i.is_blacklist' => $this->status]);
        }

        $query->select(['i.*', 'u.nickname', 'u.created_at', 'u.uid']);

        $list = $query->page($pagination)
            ->asArray()
            ->all();
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $list,
                'pagination' => $pagination,
            ],
        ];
    }

    public function getDetail()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        };
        /* @var User $user */
        $user = User::find()->alias('u')
            ->with('identity')
            ->with('userInfo')
            ->where(['u.id' => $this->id, 'u.mall_id' => \Yii::$app->mall->id])
            ->one();

        if (!$user) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '数据为空',
            ];
        }

        $newList = [
            'id' => $user->id,
            'username' => $user->username,
            'nickname' => $user->nickname,
            'mobile' => $user->userInfo->mobile,
            'email' => $user->userInfo->email,
            'avatar' => $user->userInfo->avatar,
            'remark' => $user->userInfo->remark,
            'is_blacklist' => $user->userInfo->is_blacklist,
            'created_at' => $user->created_at,
        ];
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '请求成功',
            'data' => [
                'list' => $newList,
            ],
        ];
    }

    public function updatePassword()
    {
        try {
            $user = User::findOne(\Yii::$app->user->id);
            if (!$user) {
                throw new \Exception('用户不存在');
            }

            $user->password = \Yii::$app->getSecurity()->generatePasswordHash($this->password);
            $res = $user->save();
            if (!$res) {
                throw new \Exception($this->getErrorMsg($user));
            }

            \Yii::$app->user->identity->clearLogin();
            \Yii::$app->user->logout();
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '密码修改成功',
            ];
        } catch (\Exception $e) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage()
            ];
        }
    }

    /**
     * 余额记录
     */
    public function balanceLog()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        };

        $query = BalanceLog::find()->alias('b')->where([
            'b.mall_id' => \Yii::$app->mall->id,
        ])->joinwith(['user'])->orderBy('id desc');

        if ($this->user_id) {
            $query->andWhere(['b.user_id' => $this->user_id]);
        }

        if ($this->start_date && $this->end_date) {
            $query->andWhere(['<', 'b.created_at', $this->end_date])
                ->andWhere(['>', 'b.created_at', $this->start_date]);
        }

        if ($this->keyword) {
            $userQuery = User::find()->where(['like', 'BINARY(nickname)', $this->keyword])->select('id');
            $query->andWhere([
                'or',
                ['like', 'order_no', $this->keyword],
                ['user_id' => $userQuery]
            ]);
        }

        $list = $query->page($pagination)->asArray()->all();

        foreach ($list as &$v) {
            $desc = json_decode($v['custom_desc'], true) ?? [];
            $v['info_desc'] = $desc;
        };
        unset($v);

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $list,
                'pagination' => $pagination,
            ],
        ];
    }

    /**
     * 积分记录
     */
    public function integralLog()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        };

        $query = IntegralLog::find()->alias('i')->where([
            'i.mall_id' => \Yii::$app->mall->id,
        ])->joinwith(['user'])->orderBy('id desc');

        if ($this->user_id) {
            $query->andWhere(['i.user_id' => $this->user_id]);
        }

        if ($this->start_date && $this->end_date) {
            $query->andWhere(['<', 'i.created_at', $this->end_date])->andWhere(['>', 'i.created_at', $this->start_date]);
        }

        if ($this->keyword) {
            $userQuery = User::find()->where(['like', 'BINARY(nickname)', $this->keyword])->select('id');
            $query->andWhere([
                'or',
                ['like', 'order_no', $this->keyword],
                ['user_id' => $userQuery]
            ]);
        }

        $list = $query->page($pagination)->asArray()->all();

        foreach ($list as &$v) {
            $desc = json_decode($v['custom_desc'], true) ?? [];
            $v['info_desc'] = $desc;
        };
        unset($v);
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $list,
                'pagination' => $pagination,
            ],
        ];
    }
}
