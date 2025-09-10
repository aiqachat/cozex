<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\data;

use app\forms\common\volcengine\ark\ImageGenerate;
use app\forms\common\volcengine\ArkRequestForm;
use app\forms\common\volcengine\RequestForm;
use app\forms\common\volcengine\sdk\CVSync2AsyncGetResult;
use app\forms\common\volcengine\sdk\CVSync2AsyncSubmitTask;
use app\forms\mall\member\MemberLevelForm;
use app\forms\mall\setting\ContentForm;
use app\forms\mall\visual\SettingForm;
use app\helpers\ArrayHelper;
use app\jobs\CommonJob;
use app\models\AvData;
use app\models\Model;
use app\models\User;
use app\models\VisualImage;
use app\models\VolcengineKeys;
use yii\helpers\Json;

class VisualImgForm extends Model
{
    /** @var VolcengineKeys */
    private $key;

    private $apiKey; // 火山方舟key

    private $userId;

    public $query;

    /** @var VisualImage */
    public $model;

    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    public function setAppKey($key)
    {
        $this->apiKey = $key;
        return $this;
    }

    public function setUserId($id)
    {
        $this->userId = $id;
        return $this;
    }

    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    public function setData($where, $type = 1)
    {
        $this->query = VisualImage::find()->where([
            'mall_id' => \Yii::$app->mall->id,
            'is_delete' => 0,
            'type' => $type
        ]);
        if($this->userId){
            $this->query->andWhere(['user_id' => $this->userId]);
        }else{
            $this->query->andWhere(['user_id' => 0]);
        }
        if(!empty($where['sort'])){
            $this->query->orderBy(['created_at' => $where['sort'] == 'new' ? SORT_DESC : SORT_ASC]);
        }
        if(!empty($where['text'])){
            $this->query->andWhere(['like', 'prompt', $where['text']]);
        }
        if(!empty($where['is_home'])){
            $this->query->andWhere(['=', 'is_home', $where['is_home']]);
        }
        $data = $this->query->page($pagination)->all();
        /** @var VisualImage $item */
        foreach ($data as $item){
            $item->data = Json::decode($item->data) ?: [];
        }
        return [
            'list' => array_map(function($var){
                $var = array_merge($var, $var['data']);
                unset($var['data']);
                return $var;
            }, ArrayHelper::toArray($data)),
            'pagination' => $pagination
        ];
    }

    public function saveData($requestData)
    {
        if (!empty($requestData['id'])) {
            $model = VisualImage::findOne(['id' => $requestData['id'], 'mall_id' => \Yii::$app->mall->id]);
            if (!$model) {
                throw new \Exception('图片不存在');
            }
            $model->created_at = mysql_timestamp();
        } else {
            $model = new VisualImage();
            $model->mall_id = \Yii::$app->mall->id;
        }
        $model->attributes = $requestData;
        if($this->userId){
            $model->user_id = $this->userId;
        }else{
            $model->user_id = 0;
        }
        $model->key_id = $this->key->id ?? 0;
        unset($requestData['id'], $requestData['prompt'], $requestData['is_saved'], $requestData['type']);
        foreach($requestData as $k => $v){
            if($v === null){
                unset($requestData[$k]);
            }
        }
        if(\Yii::$app->user->id == $model->user_id){
            $user = \Yii::$app->user->identity;
        }else{
            $user = User::findOne($model->user_id);
        }
        $model->status = 1;
        $model->populateRelation('user', $user);
        $model->data = Json::encode($requestData);
        $model->save();
        return $this->setModel($model);
    }

    public function getSetting()
    {
        $init = [];
        if($this->model && in_array($this->model->type, [2, 4])){
            $init = ['tab' => $this->model->is_home == 2 ? SettingForm::TAB_ARK_GLOBAL : SettingForm::TAB_ARK];
        }
        return (new SettingForm($init))->config();
    }

    public function job($data)
    {
        try {
            $id = $data['id'] ?? 0;
            $model = VisualImage::findOne(['id' => $id, 'mall_id' => \Yii::$app->mall->id]);
            if (!$model) {
                throw new \Exception('图片不存在');
            }
            $this->setModel($model);
            if($model->key_id){
                $this->setKey($model->key);
            }else{
                $config = $this->getSetting();
                $this->setAppKey($config['api_key']);
            }
            $this->generateImg();
        } catch (\Exception $e) {
            \Yii::error($e->getMessage());
        }
    }

    public function generateImg()
    {
        if(!$this->model){
            throw new \Exception('未设置数据');
        }
        if(in_array($this->model->type, [1, 3]) && !$this->key){
            throw new \Exception('请先配置默认火山引擎账号');
        }
        if(in_array($this->model->type, [2, 4]) && !$this->apiKey){
            throw new \Exception('未配置火山方舟key');
        }

        if($this->model->image_url){
            $name = basename($this->model->image_url);
        }else{
            $name = uniqid() . ".jpg";
        }

        $data = Json::decode($this->model->data);
        try {
            if($this->model->user_id){
                $t = \Yii::$app->db->beginTransaction();
                $config = $this->getSetting();
                $price = in_array($this->model->type, [3, 4]) ? $config['img_to_img_generate_price'] : $config['image_generate_price'];
                $this->pay($this->model->id, $price);
                $data['cost'] = $price;
            }
            if(in_array($this->model->type, [2, 4])){ // 火山方舟
                $obj = new ImageGenerate();
                $obj->prompt = $this->model->prompt;
                $obj->size = $data['size'] ?? null;
                if(!isset($data['model'])){
                    $data['model'] = $obj->model;
                    if($this->model->is_home == 2){
                        $data['model'] = ImageGenerate::GLOBAL_MODEL;
                    }
                    if($this->model->type == 4){
                        $data['model'] = ImageGenerate::IMAGE_G_SERVICE;
                        if($this->model->is_home == 2){
                            $data['model'] = ImageGenerate::GLOBAL_IMAGE_G_SERVICE;
                        }
                    }
                }
                $obj->model = $data['model'];
                if($this->model->type == 4){
                    $obj->size = 'adaptive';
                    $obj->image = $data['image_urls'][0];
                    $url = (new AvData())->localFile($obj->image);
                    $info = @getimagesize($obj->image);
                    $mimeType = $info['mime'] ?? 'image/jpeg';
                    $obj->image = "data:{$mimeType};base64," . base64_encode(file_get_contents($url));
                }
                if(isset($data['guidance_scale'])) {
                    $obj->guidance_scale = floatval($data['guidance_scale']);
                }
                if(isset($data['watermark'])) {
                    $obj->watermark = boolval($data['watermark']);
                }
                $obj->seed = intval($data['seed']);
                $res = (new ArkRequestForm(['object' => $obj, 'apiKey' => $this->apiKey, 'type' => $this->model->is_home]))->request();
                $content = $res['data'][0]['b64_json'];
                $data = array_merge($data, $res['usage'] ?? []);
            }elseif(in_array($this->model->type, [1, 3])){ // 即梦
                $obj = new CVSync2AsyncSubmitTask();
                if(!isset($data['model'])){
                    $data['model'] = $obj::TI_GEN_SERVICE;
                    if($this->model->type == 3) { // 图生图
                        $data['model'] = $obj::IMAGE_G_SERVICE;
                    }
                }
                $obj->req_key = $data['model'];
                if($this->model->type == 3) { // 图生图
                    $obj->scale = floatval($data['scale']);
                    $url = (new AvData())->localFile($data['image_urls'][0]);
                    $obj->binary_data_base64 = [base64_encode(file_get_contents($url))];
                }else{
                    $obj->use_pre_llm = (bool)$data['use_pre_llm'];
                    $obj->width = intval($data['width']);
                    $obj->height = intval($data['height']);
                }
                $obj->seed = intval($data['seed']);
                $obj->prompt = $this->model->prompt;
                $form = new RequestForm(['account' => $this->key, 'object' => $obj]);
                $res = $form->request();
                $obj = new CVSync2AsyncGetResult();
                $obj->task_id = $res['task_id'];
                $obj->req_key = $data['model'];
                if(!empty($data['logo_info']) && $data['logo_info']['add_logo'] == 'true') {
                    $obj->req_json = Json::encode([
                        'logo_info' => [
                            'add_logo' => (boolean)$data['logo_info']['add_logo'],
                            'position' => intval($data['logo_info']['position']),
                            'language' => intval($data['logo_info']['language']),
                            'opacity' => floatval($data['logo_info']['opacity']),
                            'logo_text_content' => $data['logo_info']['logo_text_content'],
                        ]
                    ]);
                }
                $form->object = $obj;
                do{
                    sleep (5);
                    $res = $form->request();
                    $content = $res['binary_data_base64'][0] ?? '';
                }while(!$content);
            }else{
                throw new \Exception('未知的生成类型');
            }
            $fileRes = file_uri(VisualImage::FILE_DIR . date ("Y-m-d") . "/");
            file_put_contents($fileRes['local_uri'] . $name, base64_decode($content));
            $this->model->image_url = $fileRes['web_uri'] . $name;
            if(!$this->model->is_saved) {
                $data['auto_del'] = mysql_timestamp(strtotime("+" . $this->model->deleteTime() . " hours"));
            }
            $this->model->data = Json::encode($data);
            $config = (new ContentForm())->config();
            if($config['is_img_audit']){
                if($config['img_audit_type'] === 1 || ($config['img_audit_time'] &&
                        date("H:i") >= $config['img_audit_time'][0] && date("H:i") <= $config['img_audit_time'][1])){
                    $this->model->is_admin_public = 1;
                    $this->model->is_user_public = 1;
                }
            }
            if(!$this->model->is_user_public){
                $memberLevelForm = new MemberLevelForm();
                $memberLevelForm->id = $this->model->user->identity->member_level;
                $member_permission = $memberLevelForm->getPermissions();
                if(empty($member_permission['system_data']['private_image_protection'])){
                    $this->model->is_user_public = 1;
                }
            }
            $this->model->status = 2;
            if(isset($t)) {
                $t->commit();
            }
        }catch (\Exception $e){
            $this->model->status = 3;
            $this->model->err_msg = $e->getMessage();
            if(isset($t)) {// 错误时MySQL事务回滚，扣款也就没有了
                $t->rollBack();
            }
            \Yii::error($e);
            if(\Yii::$app instanceof \yii\web\Application) {
                if(isset($fileRes)) {
                    @unlink($fileRes['local_uri'] . $name);
                }
                throw $e;
            }
        }

        if(!$this->model->save()) {
            \Yii::error("保存数据失败" . var_export ($this->model->getErrors(), true));
            throw new \Exception($this->getErrorMsg($this->model));
        }
        if($this->model->image_url && !$this->model->is_saved){
            \Yii::$app->queue1->delay($this->model->deleteTime() * 3600)->push(new CommonJob([
                'type' => 'delete_visual_img',
                'mall' => \Yii::$app->mall,
                'data' => ['id' => $this->model->id]
            ]));
        }
        return array_merge($this->model->toArray(), $data);
    }

    public function pay($id, $price)
    {
        $rsd = $this->model->is_home == 2 ? '国际版' : '';
        $params = [
            'id' => $id,
            'name' => ($this->model->type == 2 ? "火山方舟{$rsd}" : '即梦AI') . '-图片生成消耗'
        ];
        if($this->model->type == 3){
            $params['name'] = "即梦AI - 图生图消耗";
        }
        $currency = \Yii::$app->currency->setUser($this->model->user);
        $amount = floatval($price ?? 0);
        $currency->integral->sub(
            $amount,
            "账户积分支付：" . $amount,
            \Yii::$app->serializer->encode($params)
        );
    }

    public function down($id)
    {
        $model = VisualImage::findOne(['id' => $id, 'mall_id' => \Yii::$app->mall->id]);
        if(!$model){
            throw new \Exception('图片不存在');
        }
        $model->image_url = (new AvData())->localFile($model->image_url);
        if(!$model->image_url){
            throw new \Exception('图片不存在');
        }

        // 发送文件到客户端
        return \Yii::$app->response->sendFile($model->image_url);
    }

    public function del($id)
    {
        $model = VisualImage::findOne(['id' => $id, 'mall_id' => \Yii::$app->mall->id]);
        if(!$model){
            return;
        }
        $model->deleteData();
    }

    public function isPublic($id)
    {
        $model = VisualImage::findOne(['id' => $id, 'mall_id' => \Yii::$app->mall->id]);
        if(!$model){
            return;
        }
        $model->is_user_public = $model->is_user_public ? 0 : 1;
        if(!$model->is_user_public){
            $model->is_admin_public = 0;
        }
        if($model->is_permanent_public){
            $model->is_admin_public = $model->is_permanent_public == 1 ? 1 : 0;
        }
        if($model->is_user_public && !$model->is_admin_public){ // 用户公开，但管理员未公开，则判断是否自动审核
            $config = (new ContentForm())->config();
            if($config['is_video_audit']){
                if($config['video_audit_type'] === 1 || ($config['video_audit_time'] &&
                        date("H:i") >= $config['video_audit_time'][0] && date("H:i") <= $config['video_audit_time'][1])){
                    $model->is_admin_public = 1;
                }
            }
        }
        $model->save();
    }
}
