<?php

namespace app\models;

/**
 * This is the model class for table "{{%user_info}}".
 *
 * @property string $id
 * @property int $user_id
 * @property string $avatar 头像
 * @property string $integral 积分
 * @property string $total_integral 最高积分
 * @property string $balance 余额
 * @property string $total_balance 总余额
 * @property string $award_money
 * @property int $is_blacklist 是否黑名单
 * @property int $parent_id
 * @property string $mobile 手机号
 * @property string $email 邮箱
 * @property string $invite_code
 * @property string $remark 备注
 * @property string $register_time
 * @property int $is_delete
 * @property User $parent
 */
class UserInfo extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_info}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'is_blacklist', 'is_delete', 'parent_id'], 'integer'],
            [['balance', 'total_balance', 'integral', 'total_integral', 'award_money'], 'number'],
            [['avatar', 'remark', 'invite_code', 'mobile', 'email', 'register_time'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'integral' => '积分',
            'total_integral' => '最高积分',
            'avatar' => '头像',
            'balance' => '余额',
            'total_balance' => '总余额',
            'is_blacklist' => '是否黑名单',
            'mobile' => '联系方式',
            'remark' => '备注',
            'is_delete' => 'Is Delete',
        ];
    }

    public function getParent()
    {
        return $this->hasOne(User::className(), ['id' => 'parent_id'])->andWhere(['is_delete' => 0]);
    }

    public function code()
    {
        if($this->invite_code){
            return;
        }
        do {
            // 生成唯一ID（时间戳 + 随机数）
            $uniqueId = uniqid() . rand (1000, 9999);
            // 使用CRC32哈希并取模1000000，确保结果在6位数范围内（0-999999）
            $code = abs(crc32($uniqueId)) % 1000000;
            // 不足6位时补零
            $this->invite_code = str_pad($code, 6, '0', STR_PAD_LEFT);
            $exist = self::find()
                ->where(['invite_code' => $this->invite_code, 'is_delete' => 0])
                ->exists();
            if(!$exist){
                break;
            }
        }while(true);
    }

    public function invite($code = null)
    {
        if(!$code || $this->parent_id){
            return;
        }
        $info = self::findOne(['invite_code' => $code, 'is_delete' => 0]);
        $this->parent_id = $info->user_id;
    }
}