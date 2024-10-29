<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\volcengine;

use app\bootstrap\response\ApiCode;
use app\forms\common\volcengine\api\TtsAsyncQuery;
use app\forms\common\volcengine\api\TtsAsyncSubmit;
use app\forms\common\volcengine\api\TtsGenerate;
use app\forms\common\volcengine\ApiForm;
use app\jobs\CommonJob;
use app\models\AvData;
use app\models\Model;
use yii\helpers\Json;

class SpeechForm extends Model
{
    public $text;
    public $id;
    public $type;
    public $data;

    const TYPE_TTS_1 = 4; // 语音合成 - 一次性合成
    const TYPE_TTS_2 = 5; // 语音合成 - 异步合成服务

    public function rules()
    {
        return [
            [['text'], 'required'],
            [['text'], 'string'],
            [['id', 'type'], 'integer'],
            [['data'], 'safe'],
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
        $model = new AvData();
        $model->attributes = $this->attributes;
        $model->type = $this->type ?: self::TYPE_TTS_2;
        $model->data = Json::encode ($this->data);
        if(!$model->save ()){
            return $this->getErrorResponse($model);
        }
        \Yii::$app->queue->delay (0)->push (new CommonJob([
            'type' => 'handle_speech',
            'data' => ['id' => $model->id]
        ]));
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功'
        ];
    }

    public function handle(){
        $model = AvData::findOne (['id' => $this->id]);
        if(!$model) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '数据不存在'
            ];
        }
        try{
            $data = $model->data ? Json::decode($model->data) : [];
            if(!in_array($model->type, [self::TYPE_TTS_1, self::TYPE_TTS_2])) {
                throw new \Exception('type 错误');
            }
            if($model->type == self::TYPE_TTS_2) {
                $obj = new TtsAsyncSubmit();
                $obj->setVersion ($data['version']);
                $obj->style = $data['style'] ?? '';
            }else{
                $obj = new TtsGenerate();
                $obj->emotion = $data['style'] ?? '';
            }
            $obj->voice_type = $data['voice_type'];
            $obj->language = $data['language'] ?? '';
            $obj->text = $model->text;

            $api = ApiForm::common([
                'object' => $obj,
                'appid' => $data['app_id'] ?? '',
                'token' => $data['access_token'] ?? '',
            ]);
            $res = $api->request();

            if($model->type == self::TYPE_TTS_2) {
                $model->job_id = $res['task_id'] ?? '';
                $queryObj = new TtsAsyncQuery();
                $queryObj->setVersion($data['version']);
                $queryObj->task_id = $model->job_id;
                $api->object = $queryObj;
                do {
                    sleep (1);
                    $res = $api->request ();
                } while (empty($res['audio_url']));
                $ext = $obj->format;
                $content = @file_get_contents($res['audio_url']);
            }else{
                $ext = $obj->encoding;
                $content = base64_decode($res['data']);
            }

            $fileRes = file_uri('/web/uploads/av_file/');
            $file = $fileRes['local_uri'] . "{$model->id}.{$ext}";
            file_put_contents($file, $content);
            $model->result = $fileRes['web_uri'] . "{$model->id}.{$ext}";
            $model->status = 2;
            $return = [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '成功'
            ];
        }catch (\Exception $e){
            $model->status = 3;
            $model->err_msg = $e->getMessage();
            $return = [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage() . $e->getLine() . $e->getFile()
            ];
        }
        if(!$model->save()){
            \Yii::error ("model 保存失败");
            \Yii::error ($model->attributes);
            return $this->getErrorResponse($model);
        }
        return $return;
    }

    public function voiceType()
    {
        $list = [
            self::TYPE_TTS_2 => [
                array (
                    'id' => 'common',
                    'name' => '通用场景',
                    'children' =>
                        array (
                            array (
                                'id' => 'BV001_V2_streaming',
                                'name' => '通用女声 2.0',
                            ),
                            array (
                                'id' => 'BV002_streaming',
                                'name' => '通用男声',
                            ),
                            array (
                                'id' => 'BV700_V2_streaming',
                                'name' => '灿灿 2.0',
                                'emotion' => $this->emotion('pleased、sorry、annoyed、customer_service、professional、serious、happy、sad、angry、scare、hate、surprise、tear、conniving、comfort、radio、lovey-dovey、tsundere、charming、yoga、storytelling')
                            ),
                            array (
                                'id' => 'BV705_streaming',
                                'name' => '炀炀',
                                'emotion' => $this->emotion('chat、pleased、sorry、annoyed、comfort、storytelling')
                            ),
                            array (
                                'id' => 'BV701_V2_streaming',
                                'name' => '擎苍 2.0',
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、tear、novel_dialog、narrator、narrator_immersive')
                            ),
                            array (
                                'id' => 'BV406_V2_streaming',
                                'name' => '超自然音色-梓梓2.0',
                            ),
                            array (
                                'id' => 'BV407_V2_streaming',
                                'name' => '超自然音色-燃燃2.0',
                            ),
                            array (
                                'id' => 'zh_male_wennuanahu_moon_bigtts',
                                'name' => '温暖阿虎/Alvin',
                            ),
                        ),
                ),
                array (
                    'id' => 'yousheng',
                    'name' => '有声阅读',
                    'children' =>
                        array (
                            array (
                                'id' => 'BV123_streaming',
                                'name' => '阳光青年',
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、novel_dialog')
                            ),
                            array (
                                'id' => 'BV120_streaming',
                                'name' => '反卷青年',
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、novel_dialog')
                            ),
                            array (
                                'id' => 'BV119_streaming',
                                'name' => '通用赘婿',
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、novel_dialog、narrator')
                            ),
                            array (
                                'id' => 'BV115_streaming',
                                'name' => '古风少御',
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、novel_dialog、narrator')
                            ),
                            array (
                                'id' => 'BV107_streaming',
                                'name' => '霸气青叔',
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、novel_dialog、narrator')
                            ),
                            array (
                                'id' => 'BV100_streaming	',
                                'name' => '质朴青年',
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、novel_dialog、narrator')
                            ),
                            array (
                                'id' => 'BV104_streaming',
                                'name' => '温柔淑女',
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、novel_dialog、narrator')
                            ),
                            array (
                                'id' => 'BV004_streaming',
                                'name' => '开朗青年',
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、novel_dialog、narrator')
                            ),
                            array (
                                'id' => 'BV113_streaming',
                                'name' => '甜宠少御',
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、novel_dialog、narrator')
                            ),
                            array (
                                'id' => 'BV102_streaming',
                                'name' => '儒雅青年',
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、novel_dialog、narrator')
                            ),
                        ),
                ),
                array (
                    'id' => 'zhushou',
                    'name' => '智能助手',
                    'children' =>
                        array (
                            array (
                                'id' => 'BV405_streaming',
                                'name' => '甜美小源',
                                'emotion' => $this->emotion('pleased、sorry、professional、serious')
                            ),
                            array (
                                'id' => 'BV007_streaming',
                                'name' => '亲切女声',
                            ),
                            array (
                                'id' => 'BV009_streaming',
                                'name' => '知性女声',
                                'emotion' => $this->emotion('pleased、sorry、professional、serious')
                            ),
                            array (
                                'id' => 'BV419_streaming',
                                'name' => '诚诚',
                            ),
                            array (
                                'id' => 'BV415_streaming',
                                'name' => '童童',
                            ),
                            array (
                                'id' => 'BV008_streaming',
                                'name' => '亲切男声',
                                'emotion' => $this->emotion('pleased、sorry、professional、serious')
                            ),
                        ),
                ),
                array (
                    'id' => 'peiyin',
                    'name' => '视频配音',
                    'children' =>
                        array (
                            array (
                                'id' => 'BV408_streaming',
                                'name' => '译制片男声',
                            ),
                            array (
                                'id' => 'BV426_streaming',
                                'name' => '懒小羊',
                            ),
                            array (
                                'id' => 'BV428_streaming',
                                'name' => '清新文艺女声',
                            ),
                            array (
                                'id' => 'BV403_streaming',
                                'name' => '鸡汤女声',
                            ),
                            array (
                                'id' => 'BV158_streaming',
                                'name' => '智慧老者',
                            ),
                            array (
                                'id' => 'BR001_streaming',
                                'name' => '说唱小哥',
                            ),
                            array (
                                'id' => 'BV410_streaming',
                                'name' => '活力解说男',
                            ),
                            ['name' => '影视解说小帅', 'id' => 'BV411_streaming'],
                            ['name' => '解说小帅-多情感', 'id' => 'BV437_streaming',
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise')],
                            ['name' => '影视解说小美', 'id' => 'BV412_streaming'],
                            ['name' => '纨绔青年', 'id' => 'BV159_streaming'],
                            ['name' => '直播一姐', 'id' => 'BV418_streaming'],
                            ['name' => '沉稳解说男', 'id' => 'BV142_streaming'],
                            ['name' => '潇洒青年', 'id' => 'BV143_streaming'],
                            ['name' => '阳光男声', 'id' => 'BV056_streaming'],
                            ['name' => '活泼女声', 'id' => 'BV005_streaming'],
                            ['name' => '小萝莉', 'id' => 'BV064_streaming',
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise')],
                        ),
                ),
                [
                    'id' => 'tese',
                    'name' => '特色音色',
                    'children' => [
                        ['name' => '奶气萌娃', 'id' => 'BV051_streaming'],
                        ['name' => '动漫海绵', 'id' => 'BV063_streaming'],
                        ['name' => '动漫海星', 'id' => 'BV417_streaming'],
                        ['name' => '动漫小新', 'id' => 'BV050_streaming'],
                        ['name' => '天才童声', 'id' => 'BV061_streaming'],
                    ]
                ],
                [
                    'id' => 'guangao',
                    'name' => '广告配音',
                    'children' => [
                        ['name' => '促销男声', 'id' => 'BV401_streaming'],
                        ['name' => '促销女声', 'id' => 'BV402_streaming'],
                        ['name' => '磁性男声', 'id' => 'BV006_streaming'],
                    ]
                ],
                [
                    'id' => 'xinwen',
                    'name' => '新闻播报',
                    'children' => [
                        ['name' => '新闻女声', 'id' => 'BV011_streaming'],
                        ['name' => '新闻男声', 'id' => 'BV012_streaming'],
                    ]
                ],
                [
                    'id' => 'jiaoyu',
                    'name' => '教育场景',
                    'children' => [
                        ['name' => '知性姐姐-双语', 'id' => 'BV034_streaming'],
                        ['name' => '温柔小哥', 'id' => 'BV033_streaming'],
                    ]
                ],
                [
                    'id' => 'fangyan',
                    'name' => '方言',
                    'children' => [
                        ['name' => '东北老铁', 'id' => 'BV021_streaming'],
                        ['name' => '东北丫头', 'id' => 'BV020_streaming'],
                        ['name' => '方言灿灿', 'id' => 'BV704_streaming',
                            'language' => $this->language('cn、zh_dongbei、zh_yueyu、zh_shanghai、zh_xian、zh_chengdu、zh_taipu、zh_guangxi')],
                        ['name' => '西安佟掌柜', 'id' => 'BV210_streaming'],
                        ['name' => '沪上阿姐', 'id' => 'BV217_streaming'],
                        ['name' => '广西表哥', 'id' => 'BV213_streaming'],
                        ['name' => '甜美台妹', 'id' => 'BV025_streaming'],
                        ['name' => '台普男声', 'id' => 'BV227_streaming'],
                        ['name' => '港剧男神', 'id' => 'BV026_streaming'],
                        ['name' => '广东女仔', 'id' => 'BV424_streaming'],
                        ['name' => '相声演员', 'id' => 'BV212_streaming'],
                        ['name' => '重庆小伙', 'id' => 'BV019_streaming'],
                        ['name' => '四川甜妹儿', 'id' => 'BV221_streaming'],
                        ['name' => '重庆幺妹儿', 'id' => 'BV423_streaming'],
                        ['name' => '乡村企业家', 'id' => 'BV214_streaming'],
                        ['name' => '湖南妹坨', 'id' => 'BV226_streaming'],
                        ['name' => '长沙靓女', 'id' => 'BV216_streaming'],
                    ]
                ]
            ],
            self::TYPE_TTS_1 => [
                [
                    'id' => 'common',
                    'name' => '通用场景',
                    'children' => [
                        ['name' => '爽快思思/Skye', 'id' => 'zh_female_shuangkuaisisi_moon_bigtts',
                            'language' => $this->language('cn、en')],
                        ['name' => '温暖阿虎/Alvin', 'id' => 'zh_male_wennuanahu_moon_bigtts',
                            'language' => $this->language('cn、en')],
                        ['name' => '少年梓辛/Brayan', 'id' => 'zh_male_shaonianzixin_moon_bigtts',
                            'language' => $this->language('cn、en')],
                        ['name' => 'かずね（和音）/Javier or Álvaro', 'id' => 'multi_male_jingqiangkanye_moon_bigtts',
                            'language' => $this->language('ja、esmx')],
                        ['name' => 'はるこ（晴子）/Esmeralda', 'id' => 'multi_female_shuangkuaisisi_moon_bigtts',
                            'language' => $this->language('ja、esmx')],
                        ['name' => 'あけみ（朱美）', 'id' => 'multi_female_gaolengyujie_moon_bigtts',
                            'language' => $this->language('ja')],
                        ['name' => 'ひろし（広志）/Roberto', 'id' => 'multi_male_wanqudashu_moon_bigtts',
                            'language' => $this->language('ja、esmx')],
                        ['name' => '邻家女孩', 'id' => 'zh_female_linjianvhai_moon_bigtts',
                            'language' => $this->language('cn')],
                        ['name' => '渊博小叔', 'id' => 'zh_male_yuanboxiaoshu_moon_bigtts',
                            'language' => $this->language('cn')],
                        ['name' => '阳光青年', 'id' => 'zh_male_yangguangqingnian_moon_bigtts',
                            'language' => $this->language('cn')],
                        ['name' => '甜美小源', 'id' => 'zh_female_tianmeixiaoyuan_moon_bigtts',
                            'language' => $this->language('cn')],
                        ['name' => '清澈梓梓', 'id' => 'zh_female_qingchezizi_moon_bigtts',
                            'language' => $this->language('cn')],
                        ['name' => '解说小明', 'id' => 'zh_male_jieshuoxiaoming_moon_bigtts',
                            'language' => $this->language('cn')],
                        ['name' => '开朗姐姐', 'id' => 'zh_female_kailangjiejie_moon_bigtts',
                            'language' => $this->language('cn')],
                        ['name' => '邻家男孩', 'id' => 'zh_male_linjiananhai_moon_bigtts',
                            'language' => $this->language('cn')],
                        ['name' => '甜美悦悦', 'id' => 'zh_female_tianmeiyueyue_moon_bigtts',
                            'language' => $this->language('cn')],
                        ['name' => '心灵鸡汤', 'id' => 'zh_female_xinlingjitang_moon_bigtts',
                            'language' => $this->language('cn')],
                    ]
                ],
                [
                    'id' => 'fanyang',
                    'name' => '趣味方言',
                    'children' => [
                        ['name' => '京腔侃爷/Harmony', 'id' => 'zh_male_jingqiangkanye_moon_bigtts',
                            'language' => $this->language('cn、en')],
                        ['name' => '湾湾小何', 'id' => 'zh_female_wanwanxiaohe_moon_bigtts',
                            'language' => $this->language('cn')],
                        ['name' => '湾区大叔', 'id' => 'zh_female_wanqudashu_moon_bigtts',
                            'language' => $this->language('cn')],
                        ['name' => '呆萌川妹', 'id' => 'zh_female_daimengchuanmei_moon_bigtts',
                            'language' => $this->language('cn')],
                        ['name' => '广州德哥', 'id' => 'zh_male_guozhoudege_moon_bigtts',
                            'language' => $this->language('cn')],
                        ['name' => '北京小爷', 'id' => 'zh_male_beijingxiaoye_moon_bigtts',
                            'language' => $this->language('cn')],
                        ['name' => '浩宇小哥', 'id' => 'zh_male_haoyuxiaoge_moon_bigtts',
                            'language' => $this->language('cn')],
                        ['name' => '广西远舟', 'id' => 'zh_male_guangxiyuanzhou_moon_bigtts',
                            'language' => $this->language('cn')],
                        ['name' => '妹坨洁儿', 'id' => 'zh_female_meituojieer_moon_bigtts',
                            'language' => $this->language('cn')],
                        ['name' => '豫州子轩', 'id' => 'zh_male_yuzhouzixuan_moon_bigtts',
                            'language' => $this->language('cn')],
                    ]
                ],
                [
                    'id' => 'juese',
                    'name' => '角色扮演',
                    'children' => [
                        ['name' => '高冷御姐', 'id' => 'zh_female_gaolengyujie_moon_bigtts',
                            'language' => $this->language('cn')],
                        ['name' => '傲娇霸总', 'id' => 'zh_male_aojiaobazong_moon_bigtts',
                            'language' => $this->language('cn')],
                        ['name' => '魅力女友', 'id' => 'zh_female_meilinvyou_moon_bigtts',
                            'language' => $this->language('cn')],
                        ['name' => '深夜播客', 'id' => 'zh_male_shenyeboke_moon_bigtts',
                            'language' => $this->language('cn')],
                        ['name' => '柔美女友', 'id' => 'zh_female_sajiaonvyou_moon_bigtts',
                            'language' => $this->language('cn')],
                        ['name' => '撒娇学妹', 'id' => 'zh_female_yuanqinvyou_moon_bigtts',
                            'language' => $this->language('cn')],
                        ['name' => '病弱少女', 'id' => 'ICL_zh_female_bingruoshaonv_tob',
                            'language' => $this->language('cn')],
                        ['name' => '活泼女孩', 'id' => 'ICL_zh_female_huoponvhai_tob',
                            'language' => $this->language('cn')],
                        ['name' => '和蔼奶奶', 'id' => 'ICL_zh_female_heainainai_tob',
                            'language' => $this->language('cn')],
                        ['name' => '邻居阿姨', 'id' => 'ICL_zh_female_linjuayi_tob',
                            'language' => $this->language('cn')],
                        ['name' => '温柔小雅', 'id' => 'zh_female_wenrouxiaoya_moon_bigtts',
                            'language' => $this->language('cn')],
                        ['name' => '东方浩然', 'id' => 'zh_male_dongfanghaoran_moon_bigtts',
                            'language' => $this->language('cn')],
                    ]
                ],
            ]
        ];
        return Json::encode($list, JSON_UNESCAPED_UNICODE);
    }

    protected function language($data)
    {
        $language = [
            'cn' => '中文', 'en' => '英语', 'ja' => '日语', 'thth' => '泰语',
            'vivn' => '越南语', 'id' => '印尼语', 'ptbr' => '葡萄牙语',
            'esmx' => '西班牙语', 'zh_dongbei' => '东北', 'zh_yueyu' => '粤语',
            'zh_shanghai' => '上海', 'zh_xian' => '西安', 'zh_chengdu' => '成都',
            'zh_taipu' => '台湾普通话', 'zh_guangxi' => '广西普通话',
        ];
        $res = [];
        foreach (explode("、", $data) as $item){
            if(!isset($language[$item])){
                continue;
            }
            $res[] = ['label' => $language[$item], 'value' => $item];
        }
        return $res;
    }

    protected function emotion($data)
    {
        $emotion = [
            'pleased' => '愉悦', 'sorry' => '抱歉', 'annoyed' => '嗔怪', 'happy' => '开心',
            'sad' => '悲伤', 'angry' => '愤怒', 'scare' => '害怕', 'hate' => '厌恶',
            'surprise' => '惊讶', 'tear' => '哭腔', 'novel_dialog' => '平和', '客服' => 'customer_service',
            '专业' => 'professional', '严肃' => 'serious', '旁白-舒缓' => 'narrator',
            '旁白-沉浸' => 'narrator_immersive', '安慰鼓励' => 'comfort', '撒娇' => 'lovey-dovey',
            '可爱元气' => 'energetic', '绿茶' => 'conniving', '傲娇' => 'tsundere', '娇媚' => 'charming',
            '讲故事' => 'storytelling', '情感电台' => 'radio', '瑜伽' => 'yoga', '广告' => 'advertising',
            '助手' => 'assistant', '自然对话' => 'chat'
        ];
        $res = [];
        foreach (explode("、", $data) as $item){
            if(!isset($emotion[$item])){
                continue;
            }
            $res[] = ['label' => $emotion[$item], 'value' => $item];
        }
        return $res;
    }
}
