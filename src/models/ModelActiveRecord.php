<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\models;

use app\jobs\UserActionJob;
use app\models\BaseQuery\BaseActiveQuery;

class ModelActiveRecord extends \yii\db\ActiveRecord
{
    public $fillable = [];
    public $guarded = [];
    public $isLog = true; // 单独开关
    public static $log = true; // 全局开关

    /**
     * 有sql添加、更新操作时自动追加添加时间、更新时间
     * @return bool
     */
    public function beforeValidate()
    {
        $time = date('Y-m-d H:i:s', time());
        $insert = $this->isNewRecord;

        $isCreatedAt = false;
        $isUpdatedAt = false;
        $isDeletedAt = false;
        $isDelete = false;
        if (isset($this->attributes) && is_array($this->attributes())) {
            foreach ($this->attributes() as $item) {
                $item === 'created_at' ? $isCreatedAt = true : '';
                $item === 'updated_at' ? $isUpdatedAt = true : '';
                $item === 'deleted_at' ? $isDeletedAt = true : '';
                $item === 'is_delete' ? $isDelete = true : '';
            }
        }

        if ($insert === true && $isCreatedAt === true) {
            $this->created_at = $time;
        }

        if ($isUpdatedAt === true) {
            $this->updated_at = $time;
        }

        if ($isDelete === true && $isDeletedAt === true) {
            if ((int)$this->is_delete === 1) {
                $this->deleted_at = $time;
            } else {
                $this->deleted_at = '0000-00-00 00:00:00';
            }
        }

        return parent::beforeValidate();
    }

    /**
     * @return BaseActiveQuery
     */
    public static function find()
    {
        return \Yii::createObject(BaseActiveQuery::className(), [get_called_class()]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (!($this->isLog === true && self::$log === true)) {
            parent::afterSave($insert, $changedAttributes);
            return true;
        }
        try {
            $isSave = true;
            try {
                if (!\Yii::$app->user->isGuest) {
                    $userId = \Yii::$app->user->id;
                    /** @var User $user */
                    $user = \Yii::$app->user->identity;
                    $userIdentity = $user->identity;

                    // 会员影响队列时间
                    // 小程序端操作不记录日志
                    if ($userIdentity->is_super_admin == 0 && $userIdentity->is_admin == 0
                        && $userIdentity->is_operator == 0) {
                        $isSave = false;
                    }
                    // 管理员 新增操作不记录日志
                    if (($userIdentity->is_super_admin == 1 || $userIdentity->is_admin == 1) && $insert === true) {
                        $isSave = false;
                    }
                } else {
                    $userId = 0;
                    $isSave = !$insert;
                }
            } catch (\Exception $e) {
                $userId = 0;
                $isSave = !$insert;
            }

            // 更新时 保存日志
            if ($this->isLog === true && $isSave) {
                // 去除以下字段 不记录日志
                $arr = ['created_at', 'updated_at', 'deleted_at'];
                $afterUpdate = $this->attributes;
                $newBeforeUpdate = [];
                $newAfterUpdate = [];
                $remark = '数据更新';
                if (isset($afterUpdate['is_delete']) && $afterUpdate['is_delete'] == 1) {
                    $remark = '数据删除';
                }

                foreach ($changedAttributes as $key => $item) {
                    if (in_array($key, $arr)) {
                        unset($changedAttributes[$key]);
                        continue;
                    }
                    if ($item != $afterUpdate[$key]) {
                        try {
                            $newBeforeUpdate[$key] = \Yii::$app->serializer->decode($item);
                        } catch (\Exception $e) {
                            $newBeforeUpdate[$key] = $item;
                        }

                        try {
                            $newAfterUpdate[$key] = \Yii::$app->serializer->decode($afterUpdate[$key]);
                        } catch (\Exception $e) {
                            $newAfterUpdate[$key] = $afterUpdate[$key];
                        }
                    }
                }

                if ($newBeforeUpdate) {
                    // 黑名单之外的数据
                    if ($this->guarded) {
                        foreach ($this->guarded as $item) {
                            unset($newBeforeUpdate[$item]);
                            unset($newAfterUpdate[$item]);
                        }
                    }

                    // 白名单之内的数据
                    if (!$this->guarded && $this->fillable) {
                        foreach ($newBeforeUpdate as $key => $item) {
                            if (!in_array($key, $this->fillable)) {
                                unset($newBeforeUpdate[$key]);
                                unset($newAfterUpdate[$key]);
                            }
                        }
                    }

                    $modelName = self::className();
                    $dataArr = [
                        'newBeforeUpdate' => $newBeforeUpdate,
                        'newAfterUpdate' => $newAfterUpdate,
                        'modelName' => $modelName,
                        'modelId' => $this->attributes['id'] ?? 0,
                        'remark' => $remark,
                        'user_id' => $userId,
                    ];
                    $class = new UserActionJob($dataArr);
                    \Yii::$app->queue->delay(10)->push($class);
                }
            }
            parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
