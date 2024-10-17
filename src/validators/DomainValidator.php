<?php
/**
 * @copyright ©2022 wstianxia
 * author: wstianxia
 * Created by IntelliJ IDEA
 * Date Time: 2018/10/12 10:49
 */


namespace app\validators;


use yii\validators\Validator;

class DomainValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $result = $this->validateValue($model->$attribute);
        if (!empty($result) && is_array($result)) {
            $model->addError($attribute, $result[0]);
        }
    }

    protected function validateValue($value)
    {
        if ($value === null || $value === '') {
            return ['域名不能为空。', []];
        }
        $pattern = '/^'
            . '[-a-zA-Z0-9\x{4e00}-\x{9fa5}]{1,64}'
            . '(\.[-a-zA-Z0-9\x{4e00}-\x{9fa5}]{1,64}){1,4}'
            . '(\:[0-9]{1,5}){0,1}'
            . '$/u';
        if ($value && !preg_match($pattern, $value)) {
            return ['域名格式不正确。', []];
        }
        return null;
    }
}
