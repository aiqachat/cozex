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
    public $account_id;

    const TYPE_TTS_1 = 4; // 语音合成 - 一次性合成  依赖大模型  https://www.volcengine.com/docs/6561/1257584
    const TYPE_TTS_2 = 5; // 语音合成 - 异步合成服务 支持10万字的长文本  https://www.volcengine.com/docs/6561/1096680
    const TYPE_TTS_3 = 6; // 声音复刻来语音合成 - 一次性合成  https://www.volcengine.com/docs/6561/1305191
    const TYPE_TTS_4 = 7; // 语音合成 - 普通一次性合成  支持300字  https://www.volcengine.com/docs/6561/79820

    const text = [
        self::TYPE_TTS_2 => '语音合成(TTS, Text to Speech)精品长文本，适用于需要批量合成较长文本，且对返回时效性无强需求的场景，单次可支持10万字符以内文本。【普通版】支持多国语言、多风格，覆盖全年龄段的精品音色，满足不同场景需求；【情感预测版】可自动区分旁白和对话，对话可支持七大情感，为您提供沉浸式听觉盛宴，适用于有声阅读领域。',
        self::TYPE_TTS_1 => '依托新一代大模型能力，火山语音模型能够根据上下文，智能预测文本的情绪、语调等信息。并生成超自然、高保真、个性化的语音，以满足不同用户的个性化需求。相较于传统语音合成技术，语音大模型能输出在自然度、音质、韵律、气口、情感、语气词表达等方面更像真人。',
        self::TYPE_TTS_3 => '声音复刻是使用全新自研语音大模型算法打造的高效化的轻量级音色定制方案。用户在开放环境中，只需录制最短5s数据,即可即时完成对用户音色、说话风格、口音和声学环境音的复刻。',
        self::TYPE_TTS_4 => '语音合成(TTS, Text to Speech)，能将文本转换成人类声音。它运用了语音合成领域突破性的端到端合成方案，能提供高保真、个性化的音频【在线合成】单次调用支持1024字节，约等于使用UTF-8编码的300个汉字；',
    ];

    const text_name = [
        self::TYPE_TTS_2 => '语音合成TTS长文本',
        self::TYPE_TTS_1 => '大模型语音合成',
        self::TYPE_TTS_3 => '大模型声音复刻-火山引擎',
        self::TYPE_TTS_4 => '语音合成TTS',
    ];

    public function rules()
    {
        return [
            [['text', 'account_id'], 'required'],
            [['text'], 'string'],
            [['id', 'type', 'account_id'], 'integer'],
            [['data'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'account_id' => '应用',
            'text' => '文本'
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $model = new AvData();
        $model->attributes = $this->attributes;
        $model->type = $this->type ?: self::TYPE_TTS_2;
        if(!empty($this->data['voice_type'])){
            $this->data['voice_type'] = str_replace($this->repeat, "", $this->data['voice_type']);
        }
        $model->data = Json::encode ($this->data);
        if(!$model->save ()){
            return $this->getErrorResponse($model);
        }
        if($model->type == self::TYPE_TTS_4){
            $this->id = $model->id;
            $this->handle();
        }else {
            \Yii::$app->queue->delay (0)->push (new CommonJob([
                'type' => 'handle_speech',
                'data' => ['id' => $model->id]
            ]));
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功'
        ];
    }

    public function handle(){
        $model = AvData::findOne(['id' => $this->id]);
        try{
            if(!$model || !$model->account) {
                throw new \Exception('数据不存在');
            }
            $data = $model->data ? Json::decode($model->data) : [];
            $api = ApiForm::common([
                'appid' => $data['app_id'] ?? '',
                'token' => $data['access_token'] ?? '',
                'account' => $model->account
            ]);
            if(!in_array($model->type, array_keys(self::text))) {
                throw new \Exception('type 错误');
            }
            if($model->type == self::TYPE_TTS_2) {
                $obj = new TtsAsyncSubmit();
                $obj->setVersion($data['version']);
                $obj->style = $data['style'] ?? '';
                $obj->language = $data['language'] ?? '';
                $obj->speed = floatval($data['speed'] ?? 1);
            }else{
                $obj = new TtsGenerate();
                $obj->speed_ratio = floatval($data['speed'] ?? 1);
            }
            if($model->type == self::TYPE_TTS_3){
                $obj->cluster = TtsGenerate::TWO;
            }
            $obj->voice_type = $data['voice_type'];
            $obj->text = $model->text;

            $res = $api->setObject($obj)->request();

            if($model->type == self::TYPE_TTS_2) {
                $model->job_id = $res['task_id'] ?? '';
                $queryObj = new TtsAsyncQuery();
                $queryObj->setVersion($data['version']);
                $queryObj->task_id = $model->job_id;
                do {
                    sleep (1);
                    $res = $api->setObject($queryObj)->request();
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

    private $repeat = '_repeat';

    public function voiceType($type = null, $json = true)
    {
        $host = "https://lf3-static.bytednsdoc.com/obj/eden-cn/lm_hz_ihsph/ljhwZthlaukjlkulzlp/portal/bigtts/short_trial_url";
        $addr = \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/img/voice';
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
                                'id' => 'BV001_streaming',
                                'name' => '通用女声',
                                'emotion' => $this->emotion('customer_service、happy、sad、angry、scare、hate、surprise、comfort、storytelling、advertising、assistant')
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
                                'id' => 'BV700_streaming',
                                'name' => '灿灿',
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
                                'id' => 'BV407_streaming',
                                'name' => '超自然音色-燃燃',
                            ),
                        ),
                ),
                array (
                    'id' => 'yousheng',
                    'name' => '有声阅读',
                    'children' =>
                        array (
                            array (
                                'id' => 'BV701_streaming',
                                'name' => '擎苍',
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、tear、novel_dialog、narrator、narrator_immersive')
                            ),
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
                ],
                [
                    'id' => 'language',
                    'name' => '多语种',
                    'children' => [
                        ['name' => '慵懒女声-Ava', 'id' => 'BV511_streaming',
                            'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise')],
                        ['name' => '议论女声-Alicia', 'id' => 'BV505_streaming'],
                        ['name' => '情感女声-Lawrence', 'id' => 'BV138_streaming',
                            'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、novel_dialog、narrator	')],
                        ['name' => '美式女声-Amelia', 'id' => 'BV027_streaming',],
                        ['name' => '讲述女声-Amanda', 'id' => 'BV502_streaming',],
                        ['name' => '活力女声-Ariana', 'id' => 'BV503_streaming',],
                        ['name' => '活力男声-Jackson', 'id' => 'BV504_streaming',],
                        ['name' => '天才少女', 'id' => 'BV421_streaming',
                            'language' => $this->language('cn、en、ja、thth、vivn、ptbr、esmx、id')],
                        ['name' => 'Stefan', 'id' => 'BV702_streaming',
                            'language' => $this->language('cn、en、ja、ptbr、esmx、id')],
                        ['name' => '天真萌娃-Lily', 'id' => 'BV506_streaming',],
                        ['name' => '亲切女声-Anna', 'id' => 'BV040_streaming',
                            'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise')],
                        ['name' => '澳洲男声-Henry', 'id' => 'BV516_streaming'],
                        ['name' => '元气少女', 'id' => 'BV520_streaming'],
                        ['name' => '萌系少女', 'id' => 'BV521_streaming'],
                        ['name' => '气质女声', 'id' => 'BV522_streaming'],
                        ['name' => '日语男声', 'id' => 'BV524_streaming'],
                        ['name' => '活力男声Carlos（巴西地区）', 'id' => 'BV531_streaming'],
                        ['name' => '活力女声（巴西地区）', 'id' => 'BV530_streaming'],
                        ['name' => '气质御姐（墨西哥地区）', 'id' => 'BV065_streaming'],
                    ]
                ]
            ],
            self::TYPE_TTS_1 => [
                [
                    'id' => 'common',
                    'name' => '通用场景',
                    'children' => [
                        [
                            'name' => '爽快思思',
                            'id' => 'zh_female_shuangkuaisisi_moon_bigtts',
                            'audition' => "{$host}/爽快思思.mp3",
                            'sex' => '2', // 1男；2女
                            'age' => '1', // 1青年；2少年/少女；3中年；4老年
                            'pic' => "{$addr}/1.png",
                        ],
                        [
                            'name' => '温暖阿虎',
                            'id' => 'zh_male_wennuanahu_moon_bigtts',
                            'audition' => "{$host}/温暖阿虎.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/2.png",
                        ],
                        [
                            'name' => '少年梓辛',
                            'id' => 'zh_male_shaonianzixin_moon_bigtts',
                            'audition' => "{$host}/少年梓辛.mp3",
                            'sex' => '1',
                            'age' => '2',
                            'pic' => "{$addr}/3.png",
                        ],
                        [
                            'name' => '邻家女孩',
                            'id' => 'zh_female_linjianvhai_moon_bigtts',
                            'audition' => "{$host}/邻家女孩.mp3",
                            'sex' => '2',
                            'age' => '2',
                            'pic' => "{$addr}/8.png",
                        ],
                        [
                            'name' => '渊博小叔',
                            'id' => 'zh_male_yuanboxiaoshu_moon_bigtts',
                            'audition' => "{$host}/渊博小叔.mp3",
                            'sex' => '1',
                            'age' => '3',
                            'pic' => "{$addr}/9.png",
                        ],
                        [
                            'name' => '阳光青年',
                            'id' => 'zh_male_yangguangqingnian_moon_bigtts',
                            'audition' => "{$host}/阳光青年.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/10.png",
                        ],
                        [
                            'name' => '甜美小源',
                            'id' => 'zh_female_tianmeixiaoyuan_moon_bigtts',
                            'audition' => "{$host}/甜美小源.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/11.png",
                        ],
                        [
                            'name' => '清澈梓梓',
                            'id' => 'zh_female_qingchezizi_moon_bigtts',
                            'audition' => "{$host}/清澈梓梓.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/12.png",
                        ],
                        [
                            'name' => '解说小明',
                            'id' => 'zh_male_jieshuoxiaoming_moon_bigtts',
                            'audition' => "{$host}/解说小明.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/13.png",
                        ],
                        [
                            'name' => '开朗姐姐',
                            'id' => 'zh_female_kailangjiejie_moon_bigtts',
                            'audition' => "{$host}/开朗姐姐.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/14.png",
                        ],
                        [
                            'name' => '邻家男孩',
                            'id' => 'zh_male_linjiananhai_moon_bigtts',
                            'audition' => "{$host}/邻家男孩.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/15.png",
                        ],
                        [
                            'name' => '甜美悦悦',
                            'id' => 'zh_female_tianmeiyueyue_moon_bigtts',
                            'audition' => "{$host}/甜美悦悦.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/16.png",
                        ],
                        [
                            'name' => '心灵鸡汤',
                            'id' => 'zh_female_xinlingjitang_moon_bigtts',
                            'audition' => "{$host}/心灵鸡汤.mp3",
                            'sex' => '2',
                            'age' => '3',
                            'pic' => "{$addr}/17.png",
                        ],
                    ]
                ],
                [
                    'id' => 'fanyang',
                    'name' => '趣味方言',
                    'children' => [
                        [
                            'name' => '京腔侃爷',
                            'id' => 'zh_male_jingqiangkanye_moon_bigtts',
                            'audition' => "{$host}/京腔侃爷.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/18.png",
                        ],
                        [
                            'name' => '湾湾小何',
                            'id' => 'zh_female_wanwanxiaohe_moon_bigtts',
                            'audition' => "{$host}/湾湾小何.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/19.png",
                        ],
                        [
                            'name' => '湾区大叔',
                            'id' => 'zh_female_wanqudashu_moon_bigtts',
                            'audition' => "{$host}/湾区大叔.mp3",
                            'sex' => '1',
                            'age' => '3',
                            'pic' => "{$addr}/20.png",
                        ],
                        [
                            'name' => '呆萌川妹',
                            'id' => 'zh_female_daimengchuanmei_moon_bigtts',
                            'audition' => "{$host}/呆萌川妹.mp3",
                            'sex' => '2',
                            'age' => '2',
                            'pic' => "{$addr}/21.png",
                        ],
                        [
                            'name' => '广州德哥',
                            'id' => 'zh_male_guozhoudege_moon_bigtts',
                            'audition' => "{$host}/广州德哥.mp3",
                            'sex' => '1',
                            'age' => '3',
                            'pic' => "{$addr}/22.png",
                        ],
                        [
                            'name' => '北京小爷',
                            'id' => 'zh_male_beijingxiaoye_moon_bigtts',
                            'audition' => "{$host}/北京小爷.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/23.png",
                        ],
                        [
                            'name' => '浩宇小哥',
                            'id' => 'zh_male_haoyuxiaoge_moon_bigtts',
                            'audition' => "{$host}/浩宇小哥.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/24.png",
                        ],
                        [
                            'name' => '广西远舟',
                            'id' => 'zh_male_guangxiyuanzhou_moon_bigtts',
                            'audition' => "{$host}/广西远舟.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/25.png",
                        ],
                        [
                            'name' => '妹坨洁儿',
                            'id' => 'zh_female_meituojieer_moon_bigtts',
                            'audition' => "{$host}/妹坨洁儿.mp3",
                            'sex' => '2',
                            'age' => '2',
                            'pic' => "{$addr}/26.png",
                        ],
                        [
                            'name' => '豫州子轩',
                            'id' => 'zh_male_yuzhouzixuan_moon_bigtts',
                            'audition' => "{$host}/豫州子轩.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/27.png",
                        ],
                    ]
                ],
                [
                    'id' => 'juese',
                    'name' => '角色扮演',
                    'children' => [
                        [
                            'name' => '高冷御姐',
                            'id' => 'zh_female_gaolengyujie_moon_bigtts',
                            'audition' => "{$host}/高冷御姐.mp3",
                            'sex' => '2',
                            'age' => '3',
                            'pic' => "{$addr}/28.png",
                        ],
                        [
                            'name' => '傲娇霸总',
                            'id' => 'zh_male_aojiaobazong_moon_bigtts',
                            'audition' => "{$host}/傲娇霸总.mp3",
                            'sex' => '1',
                            'age' => '3',
                            'pic' => "{$addr}/29.png",
                        ],
                        [
                            'name' => '魅力女友',
                            'id' => 'zh_female_meilinvyou_moon_bigtts',
                            'audition' => "{$host}/魅力女友.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/30.png",
                        ],
                        [
                            'name' => '深夜播客',
                            'id' => 'zh_male_shenyeboke_moon_bigtts',
                            'audition' => "{$host}/深夜播客.mp3",
                            'sex' => '1',
                            'age' => '3',
                            'pic' => "{$addr}/31.png",
                        ],
                        [
                            'name' => '柔美女友',
                            'id' => 'zh_female_sajiaonvyou_moon_bigtts',
                            'audition' => "{$host}/柔美女友.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/32.png",
                        ],
                        [
                            'name' => '撒娇学妹',
                            'id' => 'zh_female_yuanqinvyou_moon_bigtts',
                            'audition' => "{$host}/撒娇学妹.mp3",
                            'sex' => '2',
                            'age' => '2',
                            'pic' => "{$addr}/33.png",
                        ],
                        [
                            'name' => '病弱少女',
                            'id' => 'ICL_zh_female_bingruoshaonv_tob',
                            'audition' => "{$host}/病弱少女.mp3",
                            'sex' => '2',
                            'age' => '2',
                            'pic' => "{$addr}/34.png",
                        ],
                        [
                            'name' => '活泼女孩',
                            'id' => 'ICL_zh_female_huoponvhai_tob',
                            'audition' => "{$host}/活泼女孩.mp3",
                            'sex' => '2',
                            'age' => '2',
                            'pic' => "{$addr}/35.png",
                        ],
                        [
                            'name' => '和蔼奶奶',
                            'id' => 'ICL_zh_female_heainainai_tob',
                            'audition' => "{$host}/和蔼奶奶.mp3",
                            'sex' => '2',
                            'age' => '4',
                            'pic' => "{$addr}/36.png",
                        ],
                        [
                            'name' => '邻居阿姨',
                            'id' => 'ICL_zh_female_linjuayi_tob',
                            'audition' => "{$host}/邻居阿姨.mp3",
                            'sex' => '2',
                            'age' => '3',
                            'pic' => "{$addr}/37.png",
                        ],
                        [
                            'name' => '温柔小雅',
                            'id' => 'zh_female_wenrouxiaoya_moon_bigtts',
                            'audition' => "{$host}/温柔小雅.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/38.png",
                        ],
                        [
                            'name' => '东方浩然',
                            'id' => 'zh_male_dongfanghaoran_moon_bigtts',
                            'audition' => "{$host}/东方浩然.mp3",
                            'sex' => '1',
                            'age' => '3',
                            'pic' => "{$addr}/39.png",
                        ],
                    ]
                ],
                [
                    'id' => 'yuzhong',
                    'name' => '多语种',
                    'children' => [
                        [
                            'name' => 'Skye',
                            'id' => 'zh_female_shuangkuaisisi_moon_bigtts' . $this->repeat,
                            'audition' => "{$host}/Skye.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/Skye.png",
                        ],
                        [
                            'name' => 'Alvin',
                            'id' => 'zh_male_wennuanahu_moon_bigtts' . $this->repeat,
                            'audition' => "{$host}/Alvin.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/Alvin.png",
                        ],
                        [
                            'name' => 'Brayan',
                            'id' => 'zh_male_shaonianzixin_moon_bigtts' . $this->repeat,
                            'audition' => "{$host}/Brayan.mp3",
                            'sex' => '1',
                            'age' => '2',
                            'pic' => "{$addr}/Brayan.png",
                        ],
                        [
                            'name' => 'かずね（和音）',
                            'id' => 'multi_male_jingqiangkanye_moon_bigtts',
                            'audition' => "{$host}/和音.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/4.png",
                        ],
                        [
                            'name' => 'Javier or Álvaro',
                            'id' => 'multi_male_jingqiangkanye_moon_bigtts' . $this->repeat,
                            'audition' => "{$host}/Javier.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/Javier.png",
                        ],
                        [
                            'name' => 'はるこ（晴子）',
                            'id' => 'multi_female_shuangkuaisisi_moon_bigtts',
                            'audition' => "{$host}/晴子.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/5.png",
                        ],
                        [
                            'name' => 'Esmeralda',
                            'id' => 'multi_female_shuangkuaisisi_moon_bigtts' . $this->repeat,
                            'audition' => "{$host}/Esmeralda.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/Esmeralda.png",
                        ],
                        [
                            'name' => 'あけみ（朱美）',
                            'id' => 'multi_female_gaolengyujie_moon_bigtts',
                            'audition' => "{$host}/朱美.mp3",
                            'sex' => '2',
                            'age' => '3',
                            'pic' => "{$addr}/6.png",
                        ],
                        [
                            'name' => 'ひろし（広志）',
                            'id' => 'multi_male_wanqudashu_moon_bigtts',
                            'audition' => "{$host}/広志.mp3",
                            'sex' => '1',
                            'age' => '3',
                            'pic' => "{$addr}/7.png",
                        ],
                        [
                            'name' => 'Roberto',
                            'id' => 'multi_male_wanqudashu_moon_bigtts' . $this->repeat,
                            'audition' => "{$host}/Roberto.mp3",
                            'sex' => '1',
                            'age' => '3',
                            'pic' => "{$addr}/Roberto.png",
                        ],
                        [
                            'name' => 'Harmony',
                            'id' => 'zh_male_jingqiangkanye_moon_bigtts' . $this->repeat,
                            'audition' => "{$host}/Harmony.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/Harmony.png",
                        ],
                    ]
                ],
            ]
        ];
        if($type){
            $list = $list[$type] ?? $list;
        }
        return $json ? Json::encode($list, JSON_UNESCAPED_UNICODE) : $list;
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
            'surprise' => '惊讶', 'tear' => '哭腔', 'novel_dialog' => '平和',
            'customer_service' => '客服', 'professional' => '专业', 'serious' => '严肃',
            'narrator' => '旁白-舒缓', 'narrator_immersive' => '旁白-沉浸', 'comfort' => '安慰鼓励',
            'lovey-dovey' => '撒娇', 'energetic' => '可爱元气', 'conniving' => '绿茶',
            'tsundere' => '傲娇', 'charming' => '娇媚', 'storytelling' => '讲故事', 'radio' => '情感电台',
            'yoga' => '瑜伽', 'advertising' => '广告', 'assistant' => '助手', 'chat' => '自然对话',
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
