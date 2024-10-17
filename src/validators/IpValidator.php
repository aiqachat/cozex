<?php
/**
 * @copyright ©2022 wstianxia
 * author: wstianxia
 * Created by IntelliJ IDEA
 * Date Time: 2018/10/12 10:49
 */


namespace app\validators;


use yii\validators\Validator;

class IpValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        $pattern = '/^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.'
            . '(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.'
            . '(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.'
            . '(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/';
        if ($value && !preg_match($pattern, $value)) {
            $model->addError($attribute, "{$model->getAttributeLabel($attribute)}格式不正确。");
        }
    }
}
