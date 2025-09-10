<?php
/**
 * Created by PhpStorm
 * User: chenzs
 * @copyright: ©2020 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\forms\admin;

use app\bootstrap\response\ApiCode;
use app\helpers\ArrayHelper;
use app\models\Model;
use app\models\VoiceList;

class VoiceForm extends Model
{
    public $id;
    public $name;
    public $voice_id;
    public $voice_type;
    public $language;
    public $language_data;
    public $emotion;
    public $status;
    public $sex;
    public $age;
    public $pic;
    public $audio;
    public $action;

    public function rules()
    {
        return [
            [['name', 'voice_type', 'language', 'voice_id', 'pic', 'audio', 'action'], 'string'],
            [['status', 'id', 'sex', 'age'], 'integer'],
            [['language_data', 'emotion'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        if ($this->action == 'reset') {
            return $this->reset();
        }

        if ($this->id) {
            $model = VoiceList::findOne($this->id);
            if (!$model) {
                return [
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => '数据不存在或已删除'
                ];
            }
        } else {
            $model = new VoiceList();
        }
        if ($this->action == 'delete') {
            $model->delete();
        } else {
            $model->attributes = $this->attributes;
            $model->language_data = json_encode($model->language_data, JSON_UNESCAPED_UNICODE);
            $model->emotion = implode(",", $this->emotion ?: []);
            if (!$model->save()) {
                return $this->getErrorResponse($model);
            }
        }

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '保存成功'
        ];
    }

    public function reset()
    {
        // 清空wstx_voice_list表数据并重置自增主键为1
        $truncateSql = "TRUNCATE TABLE " . \Yii::$app->db->getSchema()->getRawTableName(VoiceList::tableName());
        sql_execute($truncateSql);

        sleep (1);

        // 执行原有的volcengine数据重置逻辑
        $sql = (new \app\forms\common\volcengine\data\VoiceForm())->data();
        $list = \SqlFormatter::splitQuery($sql);
        foreach ($list as $item) {
            \Yii::$app->db->createCommand($item)->execute();
        }

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功'
        ];
    }

    public function get()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $list = VoiceList::find()
            ->keyword($this->name, ['like', 'name', $this->name])
            ->keyword($this->voice_id, ['like', 'voice_id', $this->voice_id])
            ->keyword($this->voice_type, ['voice_type' => $this->voice_type])
            ->keyword($this->language, ['language' => $this->language])
            ->keyword($this->status !== null, ['status' => $this->status])
            ->page($pagination)
            ->orderBy('id DESC')
            ->all();
        /** @var VoiceList $voice */
        foreach ($list as $voice) {
            if (strpos($voice->pic, 'http') === false) {
                $voice->pic = \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . $voice->pic;
            }
            $voice->language_data = json_decode($voice->language_data, true);
            $voice->emotion = !$voice->emotion ? [] : explode(",", $voice->emotion);
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功',
            'data' => [
                'list' => array_map(function ($item) {
                    $item['age_txt'] = $item['age'] == 1 ? '青年' : ($item['age'] == 2 ? '少年/少女' : ($item['age'] == 3 ? '中年' : ($item['age'] == 5 ? '儿童' : '老年')));
                    return $item;
                }, ArrayHelper::toArray($list)),
                'pagination' => $pagination,
            ]
        ];
    }
}
