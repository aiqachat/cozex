<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\api\visual;

use app\bootstrap\Pagination;
use app\bootstrap\response\ApiCode;
use app\forms\common\volcengine\data\VisualImgForm;
use app\jobs\CommonJob;
use app\models\Model;
use app\models\VisualImage;
use app\models\VisualVideo;
use app\models\VolcengineKeys;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Json;

class GeneratedForm extends Model
{
    // 公共字段
    public $model;
    public $prompt;
    public $seed;
    public $is_saved;
    public $image_urls;
    public $type;

    // 即梦生图的字段
    public $width;
    public $height;
    public $use_pre_llm;
    public $logo_info;
    public $scale;

    // 火山生图的字段
    public $guidance_scale;
    public $watermark;
    public $size;

    public $mode_type;
    public $function_type;

    public $sort;
    public $text;
    public $page;

    public function rules()
    {
        return [
            [['prompt', 'sort', 'text', 'model', 'mode_type', 'function_type', 'size'], 'string'],
            [['logo_info', 'image_urls'], 'safe'],
            [['scale', 'guidance_scale'], 'number'],
            [['width', 'height', 'use_pre_llm', 'seed', 'is_saved', 'type', 'page', 'watermark'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'prompt' => '提示词',
            'model' => '模型id'
        ];
    }

    public function getList()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $field = [
            'id', 'type', 'prompt', 'data', 'status', 'err_msg', 'is_permanent_public', 'is_admin_public',
            'is_user_public', 'created_at'
        ];
        if($this->function_type == VisualVideo::DREAM_NAME){
            $imgWhere = ['type' => [1, 3]];
            $videoWhere = ['type' => [1]];
        }
        if(in_array($this->function_type, [VisualVideo::ARK_NAME, VisualVideo::ARK_ABROAD_NAME])){
            $imgWhere = ['type' => [2, 4], 'is_home' => 1];
            $videoWhere = ['type' => [2], 'is_home' => 1];
            if($this->function_type == VisualVideo::ARK_ABROAD_NAME){
                $imgWhere['is_home'] = 2;
                $videoWhere['is_home'] = 2;
            }
        }
        // 构建图片查询
        $imageQuery = VisualImage::find()
            ->select(array_merge($field, [
                'image_url as url',
                'mode_type' => new Expression('\'image\''),
                'aspect_ratio' => new Expression('\'\''),
            ]))
            ->where([
                'mall_id' => \Yii::$app->mall->id,
                'is_delete' => 0,
                'user_id' => \Yii::$app->user->id,
            ])->keyword(!empty($imgWhere), $imgWhere);

        // 构建视频查询
        $videoQuery = VisualVideo::find()
            ->select(array_merge($field, [
                'video_url as url',
                'mode_type' => new Expression('\'video\''),
                'aspect_ratio',
            ]))
            ->where([
                'mall_id' => \Yii::$app->mall->id,
                'is_delete' => 0,
                'user_id' => \Yii::$app->user->id,
            ])->keyword(!empty($videoWhere), $videoWhere);
        // 使用UNION合并查询
        $unionQuery = $imageQuery->union($videoQuery, true);
        // 创建主查询来处理排序和分页
        $query = (new Query())
            ->from(['union_data' => $unionQuery]);

        if($this->sort){
            $query->orderBy(['created_at' => $this->sort == 'new' ? SORT_DESC : SORT_ASC]);
        }
        if($this->text){
            $query->andWhere(['like', 'prompt', $this->text]);
        }

        // 分页查询
        $pagination = new Pagination(['totalCount' => $query->count(), 'pageSize' => 20, 'page' => $this->page - 1]);
        $data = $query->limit($pagination->limit)->offset($pagination->offset)->all();

        foreach ($data as &$item){
            $item = array_merge($item, Json::decode($item['data']) ?: []);
            unset($item['data']);
        }
        unset($item);
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '生成成功',
            'data' => [
                'list' => $data,
                'pagination' => $pagination
            ]
        ];
    }

    public function generate()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        try {
            $is_home = 1;
            if($this->function_type == VisualVideo::ARK_ABROAD_NAME){
                $is_home = 2;
            }
            if($this->mode_type === 'image'){
                $form = new VisualImgForm();
                if($this->function_type == VisualVideo::DREAM_NAME) {
                    $config = $form->getSetting();
                    $key = VolcengineKeys::findOne(['mall_id' => \Yii::$app->mall->id, 'id' => $config['key_id'], 'is_delete' => 0]);
                    if (!$key) {
                        throw new \Exception('请先设置即梦账号');
                    }
                    $form->setKey($key);
                }
                $data = $this->attributes;
                $data['is_home'] = $is_home;
                unset($data['mode_type'], $data['function_type']);
                $obj = $form->setUserId(\Yii::$app->user->id)->saveData($data);
                \Yii::$app->queue->delay(0)->push(new CommonJob([
                    'type' => 'listen_visual_image',
                    'mall' => \Yii::$app->mall,
                    'data' => ['id' => $obj->model->id]
                ]));
            }
            if($this->mode_type === 'video'){
                if($this->type == 1){
                    $form = new VideoForm();
                    $form->attributes = \Yii::$app->request->post();
                    return $form->generate();
                }
                if($this->type == 2){
                    $form = new ArkVideoForm();
                    $form->attributes = \Yii::$app->request->post();
                    $form->is_home = $is_home;
                    return $form->generate();
                }
            }
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '生成成功',
                'sd' => $obj->model ?? []
            ];
        } catch (\Exception $e) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage()
            ];
        }
    }
}
