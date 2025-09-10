<?php

namespace app\forms\api\user;

use app\bootstrap\response\ApiCode;
use app\models\Model;
use app\models\User;
use app\models\UserInfo;

class PromoteForm extends Model
{
    public $size;

    public function rules()
    {
        return [
            [['size'], 'integer'],
        ];
    }

    /**
     * 字符串脱敏处理，将中间字符替换为*
     * @param string $str 需要脱敏的字符串
     * @param int $prefixKeep 保留前缀字符数
     * @param int $suffixKeep 保留后缀字符数
     * @return string 脱敏后的字符串
     */
    private function maskString($str, $prefixKeep = 0, $suffixKeep = 0)
    {
        if (empty($str)) {
            return $str;
        }

        $length = mb_strlen($str, 'UTF-8');

        // 如果字符串长度很短，直接返回部分内容
        if ($length <= 2) {
            return mb_substr($str, 0, 1, 'UTF-8') . '*';
        }

        // 自动计算保留字符数
        if ($prefixKeep == 0 && $suffixKeep == 0) {
            if ($length <= 6) {
                $prefixKeep = 1;
                $suffixKeep = 1;
            } else {
                $prefixKeep = 2;
                $suffixKeep = 2;
            }
        }

        $prefix = mb_substr($str, 0, $prefixKeep, 'UTF-8');
        $suffix = mb_substr($str, -$suffixKeep, $suffixKeep, 'UTF-8');
        $maskLength = $length - $prefixKeep - $suffixKeep;

        return $prefix . str_repeat('*', max(1, $maskLength)) . $suffix;
    }

    /**
     * 邮箱脱敏处理
     * @param string $email 邮箱地址
     * @return string 脱敏后的邮箱
     */
    private function maskEmail($email)
    {
        if (empty($email) || strpos($email, '@') === false) {
            return $email;
        }

        list($username, $domain) = explode('@', $email, 2);
        $maskedUsername = $this->maskString($username);

        return $maskedUsername . '@' . $domain;
    }

    public function getInfo()
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        $promoteNum = UserInfo::find()->alias("i")
            ->leftJoin(['u' => User::tableName()], 'i.user_id = u.id')
            ->where([
                'u.mall_id' => \Yii::$app->mall->id,
                'i.parent_id' => $user->id,
                'u.is_delete' => 0
            ])->count();
        $user->identity->level->switchData();
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'promote_num' => $promoteNum,
                'award_money' => $user->userInfo->award_money,
                'promotion_desc' => $user->identity->level->promotion_desc,
            ],
        ];
    }

    public function getUser()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        /** @var User $user */
        $user = \Yii::$app->user->identity;
        /** @var User[] $userList */
        $userList = User::find()->alias("u")
            ->leftJoin(['i' => UserInfo::tableName()], 'i.user_id = u.id')
            ->select("u.*")->with('userInfo')
            ->where([
                'u.mall_id' => \Yii::$app->mall->id,
                'i.parent_id' => $user->id,
                'u.is_delete' => 0
            ])->page($pagination, $this->size ?: 10)->all();
        $data = [];
        foreach ($userList as $item) {
            $data[] = [
                'uid' => $this->maskString($item->uid),
                'nickname' => $item->nickname,
                'email' => $this->maskEmail($item->userInfo->email),
                'avatar' => $item->userInfo->avatar,
                'register_time' => $item->userInfo->register_time ?: $item->updated_at,
            ];
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $data,
                'pagination' => $pagination
            ],
        ];
    }
}
