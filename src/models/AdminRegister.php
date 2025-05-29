<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%admin_register}}".
 *
 * @property int $id
 * @property string $username 用户名
 * @property string $password 密码
 * @property string $mobile 手机号
 * @property string $name 姓名/企业名
 * @property string $remark 申请原因
 * @property string $wechat_id 微信号
 * @property string $id_card_front_pic 身份证正面
 * @property string $id_card_back_pic 身份证反面
 * @property string $business_pic 营业执照
 * @property int $status 审核状态：0=待审核，1=通过，2=不通过
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property int $is_delete
 */
class AdminRegister extends ModelActiveRecord
{
    /**
     * 审核状态：待审核
     */
    const AUDIT_STATUS_ING = 0;

    /**
     * 审核状态：通过
     */
    const AUDIT_STATUS_TRUE = 1;

    /**
     * 审核状态：不通过
     */
    const AUDIT_STATUS_FALSE = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%admin_register}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'is_delete'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'required'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['username', 'password', 'mobile', 'remark'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 45],
            [['wechat_id'], 'string', 'max' => 64],
            [['id_card_front_pic', 'id_card_back_pic', 'business_pic'], 'string', 'max' => 2000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'password' => '密码',
            'mobile' => '手机号',
            'name' => '姓名/企业名',
            'remark' => '申请原因',
            'wechat_id' => '微信号',
            'id_card_front_pic' => '身份证正面',
            'id_card_back_pic' => '身份证反面',
            'business_pic' => '营业执照',
            'status' => '审核状态：0=待审核，1=通过，2=不通过',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
            'is_delete' => 'Is Delete',
        ];
    }
}
