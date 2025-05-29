<?php
/**
 * Created by PhpStorm.
 * User: wstianxia
 * Date: 2019/2/14
 * Time: 10:58
 * @copyright: ©2021 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\forms\admin\mall;


use app\bootstrap\response\ApiCode;
use app\models\Mall;
use app\models\Model;
use app\models\User;
use yii\db\Exception;

class MallRemovalForm extends Model
{
    public $user_id;
    public $mall_id;

    public function rules()
    {
        return [
            [['user_id', 'mall_id'], 'required'],
            [['user_id', 'mall_id'], 'integer'],
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        try {
            \Yii::$app->setMall(Mall::findOne($this->mall_id));
            if (\Yii::$app->user->identity->identity->is_super_admin != 1) {
                throw new Exception('当前登录账号无权限');
            }
            $user = User::findOne([
                'id' => $this->user_id, 'is_delete' => 0, 'mall_id' => 0
            ]);
            if (!$user) {
                throw new Exception('账户不存在');
            }

            if (!($user->identity->is_admin == 1 || $user->identity->is_super_admin == 1)) {
                throw new Exception('错误的账户');
            }

            $mall = Mall::findOne([
                'id' => $this->mall_id,
                'is_delete' => 0
            ]);

            if (!$mall) {
                throw new Exception('准备迁移的商城不存在');
            }

            $mall->user_id = $user->id;
            if ($mall->save()) {
                return [
                    'code' => ApiCode::CODE_SUCCESS,
                    'msg' => '迁移成功'
                ];
            } else {
                return $this->getErrorResponse($mall);
            }
        } catch (Exception $e) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage()
            ];
        }
    }
}
