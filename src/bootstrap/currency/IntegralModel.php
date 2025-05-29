<?php
/**
 * Created by PhpStorm.
 * User: wstianxia
 * Date: 2019/1/30
 * Time: 17:23
 * @copyright: ©2021 深圳网商天下科技
 * @link: https://www.netbcloud.com/
 */

namespace app\bootstrap\currency;

use app\models\IntegralLog;
use app\models\Mall;
use app\models\Model;
use app\models\User;
use yii\db\Exception;

/**
 * @property Mall $mall;
 * @property User $user;
 */
class IntegralModel extends Model implements BaseCurrency
{
    public $mall;
    public $user;
    public $type;// 积分类型：1=收入，2=支出

    /**
     * @param $integral
     * @param $desc
     * @param string $customDesc
     * @param string $orderNo
     * @return bool
     * @throws Exception
     */
    public function add($integral, $desc, $customDesc = '', $orderNo = '')
    {
        $this->mall = \Yii::$app->mall;
        if (!is_float($integral) && !is_int($integral) && !is_double($integral)) {
            throw new Exception('积分必须为数字类型');
        }
        $t = \Yii::$app->db->beginTransaction();
        $this->user->userInfo->integral += $integral;
        $this->user->userInfo->total_integral += $integral;
        if ($this->user->userInfo->save()) {
            try {
                $this->createLog(1, $integral, $desc, $customDesc, $orderNo);
                $t->commit();
                return true;
            } catch (Exception $e) {
                $t->rollBack();
                throw $e;
            }
        } else {
            $t->rollBack();
            throw new Exception($this->getErrorMsg($this->user->userInfo), $this->user->userInfo->errors, 1);
        }
    }

    /**
     * @param $integral
     * @param $desc
     * @param string $customDesc
     * @param string $orderNo
     * @return bool
     * @throws Exception
     */
    public function sub($integral, $desc, $customDesc = '', $orderNo = '')
    {
        $this->mall = \Yii::$app->mall;
        if (!is_float($integral) && !is_int($integral) && !is_double($integral)) {
            throw new Exception('积分必须为数字类型');
        }
        if ($this->user->userInfo->integral < $integral) {
            throw new Exception('用户积分不足');
        }
        $t = \Yii::$app->db->beginTransaction();
        $this->user->userInfo->integral -= $integral;
        if ($this->user->userInfo->save()) {
            try {
                $this->createLog(2, $integral, $desc, $customDesc, $orderNo);
                $t->commit();
                return true;
            } catch (Exception $e) {
                $t->rollBack();
                throw $e;
            }
        } else {
            $t->rollBack();
            throw new Exception($this->getErrorMsg($this->user->userInfo), $this->user->userInfo->errors, 1);
        }
    }

    /**
     * @return integer
     */
    public function select()
    {
        return round($this->user->userInfo->integral, \Yii::$app->precision);
    }

    /**
     * @return integer
     */
    public function selectTotal()
    {
        return round($this->user->userInfo->total_integral, \Yii::$app->precision);
    }

    /**
     * @param $integral
     * @param $desc
     * @param string $customDesc
     * @param string $orderNo
     * @return bool
     * @throws Exception
     */
    public function refund($integral, $desc, $customDesc = '', $orderNo = '')
    {
        $this->mall = \Yii::$app->mall;
        if (!is_int($integral)) {
            throw new Exception('积分必须是整数类型');
        }
        $t = \Yii::$app->db->beginTransaction();
        $this->user->userInfo->integral += $integral;
        if ($this->user->userInfo->save()) {
            try {
                $this->createLog(1, $integral, $desc, $customDesc, $orderNo);
                $t->commit();
                return true;
            } catch (Exception $e) {
                $t->rollBack();
                throw $e;
            }
        } else {
            $t->rollBack();
            throw new Exception($this->getErrorMsg($this->user->userInfo), $this->user->userInfo->errors, 1);
        }
    }

    /**
     * @param $type
     * @param $integral
     * @param $desc
     * @param string $customDesc
     * @param string $orderNo
     * @return bool
     * @throws \Exception
     */
    private function createLog($type, $integral, $desc, $customDesc = '', $orderNo = '')
    {
        if ($integral == 0) {
            \Yii::warning('积分为' . $integral . '不记录日志');
            return true;
        }
        if (!$customDesc) {
            $customDesc = \Yii::$app->serializer->encode(['msg' => '用户积分变动说明']);
        }
        $form = new IntegralLog();
        $form->user_id = $this->user->id;
        $form->mall_id = $this->user->mall_id;
        $form->type = $type;
        $form->integral = $integral;
        $form->desc = $desc;
        $form->custom_desc = $customDesc;
        $form->order_no = $orderNo;
        if ($form->save()) {
            return true;
        } else {
            throw new \Exception($this->getErrorMsg($form), $form->errors, 1);
        }
    }
}
