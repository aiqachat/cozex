<?php
/**
 * @copyright ©2022 wstianxia
 * author: wstianxia
 * Created by IntelliJ IDEA
 * Date Time: 2018/10/11 15:32
 */


namespace app\validators;


use yii\validators\Validator;

class PhoneNumberValidator extends Validator
{
    public $pattern = '/^1\d{10}$/';

    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        $pattern = $this->pattern;
        if ($value && !preg_match($pattern, $value)) {
            $model->addError($attribute, "{$model->getAttributeLabel($attribute)}格式不正确。");
        }
    }
}
