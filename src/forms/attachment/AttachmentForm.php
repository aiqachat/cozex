<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 */

namespace app\forms\attachment;

use app\bootstrap\response\ApiCode;
use app\forms\admin\mall\MallOverrunForm;
use app\models\Attachment;
use app\models\Mall;
use app\models\Model;
use app\models\User;
use app\models\UserIdentity;

class AttachmentForm extends Model
{
    public $attachment_group_id;
    public $is_recycle;
    public $type;
    public $keyword;
    public $limit;

    public $id;
    public $name;

    public $ids;

    /** @var Mall */
    public $mall;

    private $is_foreground;

    public function rules()
    {
        return [
            [['mall'], 'required'],
            [['attachment_group_id', 'is_recycle', 'limit', 'id'], 'integer'],
            [['type', 'keyword', 'name'], 'string'],
            [['ids'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'mall' => 'Mall',
        ];
    }

    public function setForeground()
    {
        $this->is_foreground = 1;
        return $this;
    }

    public function getList(){
        if (!$this->validate()) {
            return $this->getErrorResponse($this);
        }

        $typeMap = [
            'other' => 0,
            'image' => 1,
            'video' => 2,
            'file' => 3,
        ];

        $query = Attachment::find()->where([
            'mall_id' => $this->mall->id,
            'is_delete' => 0,
        ]);

        if(isset($typeMap[$this->type])){
            $query->andWhere(['type' => $typeMap[$this->type]]);
        }

        !is_null($this->is_recycle) && $query->andWhere(['is_recycle' => $this->is_recycle]);
        $query->keyword($this->keyword, ['like', 'name', $this->keyword]);
        $this->attachment_group_id && $query->andWhere(['attachment_group_id' => $this->attachment_group_id]);

        if(!\Yii::$app->user->isGuest){
            if($this->is_foreground) {
                $query->andWhere(['user_id' => \Yii::$app->user->id]);
            }else{
                $query->andWhere(['user_id' => 0]);
            }
        }

        $list = $query
            ->orderBy('id DESC')
            ->page($pagination, intval($this->limit ?: '20'))
            ->asArray()
            ->all();

        foreach ($list as &$item) {
            $item['thumb_url'] = $item['thumb_url'] ?: $item['url'];
        }

        if($this->is_foreground){
            $option = (new MallOverrunForm())->getSetting();
            if($this->type == 'image'){
                $overrun = $option['is_img_overrun'] ? \Yii::t('common', '无限制') : "{$option['img_overrun']}MB";
            }else{
                $overrun = $option['is_video_overrun'] ? \Yii::t('common', '无限制') : "{$option['video_overrun']}MB";
            }
        }

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $list,
                'pagination' => $pagination,
                'overrun' => $overrun ?? ''
            ],
        ];
    }

    public function rename()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse($this);
        }

        $attachment = Attachment::findOne([
            'mall_id' => $this->mall->id,
            'is_delete' => 0,
            'id' => $this->id,
        ]);
        if (!$attachment) {
            throw new \Exception('数据为空');
        }
        $attachment->name = $this->name;
        $attachment->save();
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '保存成功'
        ];
    }

    public function delete()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse($this);
        }

        if (!is_array($this->ids)) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '提交数据格式错误。',
            ];
        }
        switch ($this->type) {
            case '1':
                $edit = ['is_recycle' => 1];
                break;
            case '2':
                $edit = ['is_recycle' => 0];
                break;
            case '3':
                $edit = ['is_delete' => 1];
                break;
            default:
                $edit = [];
                break;
        }
        Attachment::updateAll($edit, [
            'id' => $this->ids,
            'mall_id' => $this->mall->id,
        ]);
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '操作成功。',
        ];
    }
}
