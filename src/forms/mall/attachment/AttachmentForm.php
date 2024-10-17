<?php
/**
 * Created by PhpStorm.
 * User: wstianxia
 * Date: 2019/7/24
 * Time: 9:50
 * @copyright: ©2021 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\forms\mall\attachment;


use app\bootstrap\response\ApiCode;
use app\forms\common\attachment\CommonAttachment;
use app\models\AttachmentStorage;
use app\models\Model;

class AttachmentForm extends Model
{
    public $id;

    public function rules()
    {
        return [
            ['id', 'integer']
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        try {
            $common = CommonAttachment::getCommon(\Yii::$app->user->identity);
            $auth = $common->getStorageAuth();
            if (!is_array($auth)) {
                throw new \Exception('该账户没有设置存储方式');
            }
            $attachment = AttachmentStorage::findOne([
                'id' => $this->id, 'user_id' => \Yii::$app->user->id, 'mall_id' => 0, 'type' => $auth
            ]);
            if (!$attachment) {
                throw new \Exception('该账户下不存在id为' . $this->id . '的上传设置');
            }
            $data = [
                'type' => $attachment->type,
                'config' => \Yii::$app->serializer->decode($attachment->config)
            ];
            $common->attachmentCreateStorage($data);
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '保存成功'
            ];
        } catch (\Exception $exception) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $exception->getMessage()
            ];
        }
    }
}
