<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\user;

use app\bootstrap\response\ApiCode;
use app\forms\mall\export\UserExport;
use app\models\Model;
use app\models\User;
use app\models\UserIdentity;
use app\models\UserInfo;
use yii\helpers\ArrayHelper;

class UserForm extends Model
{
    public $id;
    public $page_size;
    public $keyword;
    public $user_id;
    public $status;
    public $date;
    public $start_date;
    public $end_date;

    public $flag;
    public $fields;
    public $sort;

    public $batch_ids;
    public $pic_url;
    public $num;
    public $remark;
    public $type;
    public $price;

    public $password;

    public function rules()
    {
        return [
            [['date', 'flag', 'keyword'], 'trim'],
            [['start_date', 'end_date', 'keyword', 'sort', 'pic_url', 'remark', 'password'], 'string'],
            [['id', 'user_id', 'status', 'num', 'type'], 'integer'],
            [['keyword'], 'string', 'max' => 255],
            [['page_size'], 'default', 'value' => 10],
            [['fields', 'batch_ids'], 'safe'],
            [['keyword'], 'default', 'value' => ''],
            [['price'], 'number', 'min' => 0.01, 'max' => 99999999],
        ];
    }

    public function searchUser()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $query = User::find()->alias('u')->with('userPlatform')->select('u.id,u.nickname')->where([
            'AND',
            ['or', ['LIKE', 'BINARY(u.nickname)', $this->keyword], ['u.id' => $this->keyword], ['u.mobile' => $this->keyword]],
        ]);
        $list = $query->InnerJoinwith('userInfo')->orderBy('nickname')->limit(30)->all();

        $newList = [];
        /** @var User $item */
        foreach ($list as $item) {
            $newItem = ArrayHelper::toArray($item);
            $newItem['avatar'] = $item->userInfo ? $item->userInfo->avatar : '';
            $newItem['nickname'] = $item->nickname;
            $newList[] = $newItem;
        }

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $newList,
            ],
        ];
    }

    //用户列表
    public function getList()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        };
        $query = User::find()->alias('u')->where([
            'u.is_delete' => 0,
        ])->InnerJoin([
            'i' => UserInfo::tableName(),
        ], 'u.id = i.user_id')
            ->InnerJoin([
                'd' => UserIdentity::tableName(),
            ], 'd.user_id = u.id');

        $searchWhere = [
            'OR',
            ['like', 'BINARY(u.nickname)', $this->keyword],
            ['like', 'u.mobile', $this->keyword],
            ['like', 'u.id', $this->keyword],
            ['like', 'BINARY(i.remark_name)', $this->keyword],
            ['like', 'BINARY(i.remark)', $this->keyword],
            ['like', 'i.contact_way', $this->keyword],
        ];

        $query->keyword($this->keyword, $searchWhere);

        $query->select(['i.*', 'u.nickname', 'u.mobile', 'd.is_admin', 'u.mobile', 'u.created_at']);

        switch ($this->sort) {
            default:
                $query->orderBy('u.id DESC');
                break;
        }

        $list = $query->page($pagination, $this->page_size)
            ->asArray()
            ->all();

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $list,
                'pagination' => $pagination,
                'exportList' => (new UserExport())->fieldsList(),
            ],
        ];
    }

    //用户编辑
    public function getDetail()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        };
        /* @var User $user */
        $user = User::find()->alias('u')
            ->with('identity')
            ->with('userInfo')
            ->where(['u.id' => $this->id])
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
            'mobile' => $user->mobile,
            'avatar' => $user->userInfo->avatar,
            'contact_way' => $user->userInfo->contact_way,
            'remark' => $user->userInfo->remark,
            'is_blacklist' => $user->userInfo->is_blacklist,
            'created_at' => $user->created_at,
            'remark_name' => $user->userInfo->remark_name,
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
}
