<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\data;

use app\forms\common\volcengine\ark\VideoGenerate;
use app\forms\common\volcengine\ark\VideoGenerateTask;
use app\forms\common\volcengine\ArkRequestForm;
use app\forms\common\volcengine\RequestForm;
use app\forms\common\volcengine\sdk\CVSync2AsyncGetResult;
use app\forms\common\volcengine\sdk\CVSync2AsyncSubmitTask;
use app\forms\mall\member\MemberLevelForm;
use app\forms\mall\setting\ContentForm;
use app\forms\mall\visual\SettingForm;
use app\jobs\CommonJob;
use app\models\AvData;
use app\models\Model;
use app\models\User;
use app\models\VisualVideo;
use app\models\VolcengineKeys;
use GuzzleHttp\Client;
use yii\helpers\Json;

class VisualVideoForm extends Model
{
    /** @var VolcengineKeys */
    private $key;

    private $apiKey; // 火山方舟key

    private $userId;

    public $query;

    /** @var VisualVideo */
    public $modelObj;

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
        $this->modelObj = $model;
        return $this;
    }

    public function setData($where, $type = 1)
    {
        $this->query = VisualVideo::find()->where([
            'mall_id' => \Yii::$app->mall->id,
            'is_delete' => 0,
            'type' => $type,
        ]);
        if($this->userId){
            $this->query->andWhere(['user_id' => $this->userId]);
        }else{
            $this->query->andWhere(['user_id' => 0]);
        }
        $this->query->orderBy(['created_at' => empty($where['sort']) || $where['sort'] == 'new' ? SORT_DESC : SORT_ASC]);
        $this->query->keyword(!empty($where['text']), ['like', 'prompt', $where['text']]);
        $this->query->keyword(!empty($where['is_home']), ['=', 'is_home', $where['is_home']]);
        $data = $this->query->page($pagination, 10)->all();
        /** @var VisualVideo $item */
        foreach ($data as $item){
            $item->data = Json::decode($item->data);
            if($item->status == 1 && $item->created_at < mysql_timestamp(strtotime('-1 day'))){
                $item->status = 3;
            }
        }
        return [
            'list' => $data,
            'pagination' => $pagination
        ];
    }

    public function saveData($id = null)
    {
        if ($id) {
            $model = VisualVideo::findOne (['id' => $id, 'mall_id' => \Yii::$app->mall->id]);
            if (!$model) {
                throw new \Exception('视频不存在');
            }
        } else {
            $model = new VisualVideo();
            $model->mall_id = \Yii::$app->mall->id;
        }
        $model->status = 1;
        $model->user_id = $this->userId ?: 0;
        if(\Yii::$app->user->id == $model->user_id){
            $user = \Yii::$app->user->identity;
        }else{
            $user = User::findOne($model->user_id);
        }
        $model->populateRelation('user', $user);
        $this->setModel($model);
        $this->handleData();
        if(is_array($model->image_urls)){
            $model->image_urls = Json::encode($model->image_urls);
        }
        if($model->mode == 'text'){
            $model->image_urls = '';
        }
        if(is_array($model->data)){
            $model->data = Json::encode($model->data);
        }
        $model->prompt = $model->prompt ?: '';
        $model->key_id = $this->key->id ?? 0;
        $model->save();

        \Yii::$app->queue->delay(0)->push(new CommonJob([
            'type' => 'listen_visual_video',
            'mall' => \Yii::$app->mall,
            'data' => ['id' => $model->id]
        ]));
        return $model;
    }

    public function handleData(){}

    public function job($data)
    {
        $t = \Yii::$app->db->beginTransaction();
        try {
            $id = $data['id'] ?? 0;
            $model = VisualVideo::findOne(['id' => $id, 'mall_id' => \Yii::$app->mall->id]);
            if (!$model) {
                throw new \Exception('视频不存在');
            }
            $this->setModel($model);
            if($model->type == 1){
                if(!$model->key){
                    throw new \Exception('请先配置默认火山引擎账号');
                }
                $this->setKey($model->key);
            }else{
                $config = $this->getSetting();
                $this->setAppKey($config['api_key']);
                if(!$this->apiKey){
                    throw new \Exception('未配置火山方舟key');
                }
            }
            $this->submitVideoTask()->getVideoResult();
            $t->commit();
        } catch (\Exception $e) {
            \Yii::error($e->getMessage());
            $t->rollBack();
        }
        if(isset($model)) {
            if (!$model->save()) {
                \Yii::error("保存数据失败" . var_export($model->getErrors(), true));
            }else {
                if ($model->video_url && !$model->is_saved) {
                    \Yii::$app->queue1->delay ($model->deleteTime() * 3600)->push(new CommonJob([
                        'type' => 'delete_visual_video',
                        'mall' => \Yii::$app->mall,
                        'data' => ['id' => $model->id]
                    ]));
                }
            }
        }
    }

    public function submitVideoTask()
    {
        try {
            $this->modelObj->status = 1;
            $data = Json::decode($this->modelObj->data);
            if($this->modelObj->user_id){
                $this->pay($this->modelObj->id, $data['cost']);
            }

            if($this->modelObj->type == 1){
                $this->submitTaskByDream($data);
            }
            if($this->modelObj->type == 2){
                $this->submitTaskByArk($data);
            }
        }catch (\Exception $e){
            \Yii::error($e);
            $this->modelObj->err_msg = $e->getMessage();
            $this->modelObj->status = 3;
            throw $e;
        }
        return $this;
    }

    public function submitTaskByDream($data)
    {
        $obj = new CVSync2AsyncSubmitTask();
        $obj->req_key = $data['model'];
        if($this->modelObj->mode == 'image'){
            $temp = new AvData();
            $obj->binary_data_base64 = [];
            foreach ((array)@Json::decode($this->modelObj->image_urls) as $url) {
                $url = $temp->localFile($url);
                $obj->binary_data_base64[] = base64_encode(@file_get_contents($url));
            }
        }else{
            $obj->aspect_ratio = $this->modelObj->aspect_ratio;
        }
        $obj->prompt = $this->modelObj->prompt;
        $obj->seed = intval($this->modelObj->seed);
        $obj->frames = 24 * intval($data['duration'] ?? 5) + 1;
        $res = (new RequestForm(['account' => $this->key, 'object' => $obj]))->request();
        $this->modelObj->task_id = $res['task_id'];
    }

    // https://www.volcengine.com/docs/85621/1777001
    public function submitTaskByArk($data)
    {
        $obj = new VideoGenerate();
        $obj->text = $this->modelObj->prompt;
        $obj->model = $data['model'];
        if($this->modelObj->image_urls){
            $obj->image_url = @json_decode($this->modelObj->image_urls, true) ?: $this->modelObj->image_urls;
            $temp = new AvData();
            foreach ((array)$obj->image_url as $k => $item) {
                $url = $temp->localFile($item);
                $info = @getimagesize($item);
                $mimeType = $info['mime'] ?? 'image/jpeg';
                $obj->image_url[$k] = "data:{$mimeType};base64," . base64_encode(file_get_contents($url));
            }
        }
        $obj->resolution = $data['resolution'];
        $obj->ratio = $this->modelObj->aspect_ratio ?: '';
        $obj->duration = intval($data['duration']);
        $obj->watermark = boolval($data['watermark']);
        if(isset($data['fix'])) {
            $obj->camerafixed = boolval($data['fix']);
        }
        $res = (new ArkRequestForm(['object' => $obj, 'apiKey' => $this->apiKey, 'type' => $this->modelObj->is_home]))->request();
        $this->modelObj->task_id = $res['id'];
    }

    public function getVideoResult()
    {
        if(!$this->modelObj->task_id){
            return $this;
        }

        if($this->modelObj->video_url){
            $name = basename($this->modelObj->video_url);
        }else{
            $name = uniqid() . ".mp4";
        }

        try {
            $data = Json::decode($this->modelObj->data);
            if($this->modelObj->type == 1){
                $this->resultByDream($name, $data['model']);
            }
            if($this->modelObj->type == 2){
                $this->resultByArk($name);
            }
            if(!\Yii::$app->db->isActive){
                \Yii::$app->db->open();
            }
            if(!$this->modelObj->is_saved) {
                $data['auto_del'] = mysql_timestamp(strtotime("+" . $this->modelObj->deleteTime() . " hours"));
            }
            $config = (new ContentForm())->config();
            if($config['is_video_audit']){
                if($config['video_audit_type'] === 1 || ($config['video_audit_time'] &&
                        date("H:i") >= $config['video_audit_time'][0] && date("H:i") <= $config['video_audit_time'][1])){
                    $this->modelObj->is_admin_public = 1;
                    $this->modelObj->is_user_public = 1;
                }
            }
            if(!$this->modelObj->is_user_public){
                $memberLevelForm = new MemberLevelForm();
                $memberLevelForm->id = $this->modelObj->user->identity->member_level;
                $member_permission = $memberLevelForm->getPermissions();
                if(empty($member_permission['system_data']['private_video_protection'])){
                    $this->modelObj->is_user_public = 1;
                }
            }
            $this->modelObj->data = Json::encode($data);
            $this->modelObj->status = 2;
        }catch (\Exception $e){
            $this->modelObj->err_msg = $e->getMessage();
            $this->modelObj->status = 3;
            throw $e;
        }
        return $this;
    }

    public function resultByDream($name, $model)
    {
        $obj = new CVSync2AsyncGetResult();
        $obj->req_key = $model;
        $obj->task_id = $this->modelObj->task_id;
        $fileRes = file_uri(VisualVideo::FILE_DIR . date ("Y-m-d") . "/");
        do {
            sleep(10);
            $res = (new RequestForm(['account' => $this->key, 'object' => $obj]))->request();
            if (!empty($res['video_url'])) {
                $this->modelObj->video_url = $res['video_url'];
                $fp = fopen($fileRes['local_uri'] . $name, 'w+');
                if ($fp === false) {
                    throw new \Exception('无法保存文件，请检查文件写入权限。', 10);
                }
                $client = new Client(['verify' => false, 'stream' => true]);
                $response = $client->get($res['video_url']);
                $body = $response->getBody();
                while (!$body->eof()) {
                    fwrite($fp, $body->read(1024));
                }
                fclose($fp);
            } elseif (!empty($res['binary_data_base64'])) {
                file_put_contents($fileRes['local_uri'] . $name, base64_decode($res['binary_data_base64']));
            }
        }while(empty($res['binary_data_base64']) && empty($res['video_url']));
        $this->modelObj->video_url = $fileRes['web_uri'] . $name;
    }

    public function resultByArk($name)
    {
        $obj = new VideoGenerateTask();
        $obj->id = $this->modelObj->task_id;

        $fileRes = file_uri(VisualVideo::FILE_DIR . date ("Y-m-d") . "/");
        do {
            sleep(10);
            $res = (new ArkRequestForm(['object' => $obj, 'apiKey' => $this->apiKey, 'type' => $this->modelObj->is_home]))->request();
            if ($res['status'] == 'succeeded') {
                $fp = fopen($fileRes['local_uri'] . $name, 'w+');
                if ($fp === false) {
                    throw new \Exception('无法保存文件，请检查文件写入权限。', 10);
                }
                $client = new Client(['verify' => false, 'stream' => true]);
                $response = $client->get($res['content']['video_url']);
                $body = $response->getBody();
                while (!$body->eof()) {
                    fwrite($fp, $body->read(1024));
                }
                fclose($fp);
            }
        }while(in_array($res['status'], ['queued', 'running']));
        $this->modelObj->video_url = $fileRes['web_uri'] . $name;
    }

    public function getSetting()
    {
        $init = [];
        if($this->modelObj && $this->modelObj->type == 2){
            $init = ['tab' => $this->modelObj->is_home == 2 ? SettingForm::TAB_ARK_GLOBAL : SettingForm::TAB_ARK];
        }
        return (new SettingForm($init))->config();
    }

    public function pay($id, $price)
    {
        $rsd = $this->modelObj->is_home == 2 ? '国际版' : '';
        $currency = \Yii::$app->currency->setUser($this->modelObj->user);
        $amount = floatval($price ?? 0);
        $currency->integral->sub(
            $amount,
            "账户积分支付：" . $amount,
            \Yii::$app->serializer->encode([
                'id' => $id,
                'name' => ($this->modelObj->type == 2 ? "火山方舟{$rsd}" : '即梦AI') . '-视频生成消耗'
            ])
        );
    }

    public function down($id)
    {
        $model = VisualVideo::findOne(['id' => $id, 'mall_id' => \Yii::$app->mall->id]);
        if(!$model){
            throw new \Exception('视频不存在');
        }
        $model->video_url = (new AvData())->localFile($model->video_url);
        if(!$model->video_url){
            throw new \Exception('视频不存在');
        }

        // 发送文件到客户端
        return \Yii::$app->response->sendFile($model->video_url);
    }

    public function del($id)
    {
        $model = VisualVideo::findOne(['id' => $id, 'mall_id' => \Yii::$app->mall->id]);
        if(!$model){
            return;
        }
        $model->deleteData();
    }

    public function isPublic($id)
    {
        $model = VisualVideo::findOne(['id' => $id, 'mall_id' => \Yii::$app->mall->id]);
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

    /**
     * 判断图片最接近哪个标准比例
     *
     * @param string $imagePath 图片路径
     * @param array $standardRatios 标准比例数组
     * @return string|false 最接近的比例，失败返回false
     */
    function getClosestRatio($imagePath, $standardRatios = ['1:1']) {
        // 获取图片信息
        $imageInfo = getimagesize($imagePath);
        if (!$imageInfo) {
            return false;
        }

        // 提取宽度和高度
        $width = $imageInfo[0];
        $height = $imageInfo[1];

        // 计算图片的宽高比（宽度/高度）
        $imageRatio = $width / $height;

        $minDifference = PHP_INT_MAX;
        $closestRatio = '';

        // 遍历所有标准比例，计算差异
        foreach ($standardRatios as $ratio) {
            // 解析比例
            list($stdWidth, $stdHeight) = explode(':', $ratio);
            $stdRatio = $stdWidth / $stdHeight;

            // 计算差异（使用绝对值）
            $difference = abs($imageRatio - $stdRatio);

            // 找到差异最小的比例
            if ($difference < $minDifference) {
                $minDifference = $difference;
                $closestRatio = $ratio;
            }
        }

        return $closestRatio;
    }
}
