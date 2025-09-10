<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\data;

use app\helpers\ArrayHelper;
use app\models\VoiceList;
use yii\helpers\Json;

class VoiceForm extends BaseForm
{
    /** @var integer 1：国内站；2：国际站 */
    public $is_home;
    static $voiceList;

    public function rules()
    {
        return [
            [['is_home'], 'integer'],
            [['is_home'], 'default', 'value' => 1],
        ];
    }

    public function abroad()
    {
        return [
            'zh_female_shaoergushi_mars_bigtts', 'zh_male_silang_mars_bigtts', 'zh_male_jieshuonansheng_mars_bigtts',
            'zh_female_jitangmeimei_mars_bigtts', 'zh_female_tiexinnvsheng_mars_bigtts',
            'zh_female_qiaopinvsheng_mars_bigtts', 'zh_female_mengyatou_mars_bigtts', 'zh_female_cancan_mars_bigtts',
            'zh_female_qingxinnvsheng_mars_bigtts', 'zh_female_linjia_mars_bigtts', 'zh_male_wennuanahu_moon_bigtts',
            'zh_male_shaonianzixin_moon_bigtts', 'zh_female_shuangkuaisisi_moon_bigtts',
            'zh_male_jingqiangkanye_moon_bigtts', 'en_female_anna_mars_bigtts', 'en_male_adam_mars_bigtts',
            'en_female_sarah_mars_bigtts', 'en_male_dryw_mars_bigtts', 'en_male_smith_mars_bigtts',
            'zh_male_baqiqingshu_mars_bigtts', 'zh_female_wenroushunv_mars_bigtts',
            'zh_female_gaolengyujie_moon_bigtts', 'zh_female_linjianvhai_moon_bigtts',
            'zh_male_yuanboxiaoshu_moon_bigtts', 'zh_male_yangguangqingnian_moon_bigtts',
            'zh_male_guozhoudege_moon_bigtts', 'zh_female_wanqudashu_moon_bigtts',
            'zh_female_daimengchuanmei_moon_bigtts', 'zh_female_wanwanxiaohe_moon_bigtts',
            'multi_male_jingqiangkanye_moon_bigtts', 'multi_female_shuangkuaisisi_moon_bigtts',
            'multi_male_wanqudashu_moon_bigtts', 'multi_female_gaolengyujie_moon_bigtts'
        ];
    }

    public function voiceType($type = null, $json = true)
    {
        $localAddr = \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/voice';
        $list = [
            // 最近更新时间：2025.03.25 11:59:57
            $this->ttsLong => [
                array(
                    'id' => '通用场景',
                    'name' => \Yii::t('voice', '通用场景'),
                    'children' =>
                        array(
                            array(
                                'id' => 'BV001_V2_streaming',
                                'name' => \Yii::t('voice', '通用女声2.0'),
                                'audition' => "{$localAddr}/通用女声 2.0.mp3",
                            ),
                            array(
                                'id' => 'BV001_streaming',
                                'name' => \Yii::t('voice', '通用女声'),
                                'audition' => "{$localAddr}/通用女声.mp3",
                                'emotion' => $this->emotion('customer_service,happy,sad,angry,scare,hate,surprise,comfort,storytelling,advertising,assistant')
                            ),
                            array(
                                'id' => 'BV002_streaming',
                                'name' => \Yii::t('voice', '通用男声'),
                                'audition' => "{$localAddr}/通用男声.mp3",
                            ),
                            array(
                                'id' => 'BV700_V2_streaming',
                                'name' => \Yii::t('voice', '灿灿2.0'),
                                'audition' => "{$localAddr}/灿灿 2.0.mp3",
                                'emotion' => $this->emotion('pleased,sorry,annoyed,customer_service,professional,serious,happy,sad,angry,scare,hate,surprise,tear,conniving,comfort,radio,lovey-dovey,tsundere,charming,yoga,storytelling')
                            ),
                            array(
                                'id' => 'BV700_streaming',
                                'name' => \Yii::t('voice', '灿灿'),
                                'audition' => "{$localAddr}/灿灿.mp3",
                                'emotion' => $this->emotion('pleased,sorry,annoyed,customer_service,professional,serious,happy,sad,angry,scare,hate,surprise,tear,conniving,comfort,radio,lovey-dovey,tsundere,charming,yoga,storytelling')
                            ),
                            array(
                                'id' => 'BV705_streaming',
                                'name' => \Yii::t('voice', '炀炀'),
                                'audition' => "{$localAddr}/炀炀.mp3",
                                'emotion' => $this->emotion('chat,pleased,sorry,annoyed,comfort,storytelling')
                            ),
                            array(
                                'id' => 'BV701_V2_streaming',
                                'name' => \Yii::t('voice', '擎苍2.0'),
                                'audition' => "{$localAddr}/擎苍 2.0.mp3",
                                'emotion' => $this->emotion('happy,sad,angry,scare,hate,surprise,tear,novel_dialog,narrator,narrator_immersive')
                            ),
                            array(
                                'id' => 'BV406_V2_streaming',
                                'name' => \Yii::t('voice', '梓梓2.0'),
                                'audition' => "{$localAddr}/超自然音色-梓梓2.0.mp3",
                            ),
                            array(
                                'id' => 'BV407_V2_streaming',
                                'name' => \Yii::t('voice', '燃燃2.0'),
                                'audition' => "{$localAddr}/超自然音色-燃燃2.0.mp3",
                            ),
                            array(
                                'id' => 'BV407_streaming',
                                'name' => \Yii::t('voice', '燃燃'),
                                'audition' => "{$localAddr}/超自然音色-燃燃.mp3",
                            ),
                        ),
                ),
                array(
                    'id' => '有声阅读',
                    'name' => \Yii::t('voice', '有声阅读'),
                    'children' =>
                        array(
                            array(
                                'id' => 'BV701_streaming',
                                'name' => \Yii::t('voice', '擎苍'),
                                'audition' => "{$localAddr}/擎苍.mp3",
                                'emotion' => $this->emotion('happy,sad,angry,scare,hate,surprise,tear,novel_dialog,narrator,narrator_immersive')
                            ),
                            array(
                                'id' => 'BV123_streaming',
                                'name' => \Yii::t('voice', '阳光青年'),
                                'audition' => "{$localAddr}/阳光青年.mp3",
                                'emotion' => $this->emotion('happy,sad,angry,scare,hate,surprise,novel_dialog')
                            ),
                            array(
                                'id' => 'BV120_streaming',
                                'name' => \Yii::t('voice', '反卷青年'),
                                'audition' => "{$localAddr}/反卷青年.mp3",
                                'emotion' => $this->emotion('happy,sad,angry,scare,hate,surprise,novel_dialog')
                            ),
                            array(
                                'id' => 'BV119_streaming',
                                'name' => \Yii::t('voice', '通用赘婿'),
                                'audition' => "{$localAddr}/通用赘婿.mp3",
                                'emotion' => $this->emotion('happy,sad,angry,scare,hate,surprise,novel_dialog,narrator')
                            ),
                            array(
                                'id' => 'BV115_streaming',
                                'name' => \Yii::t('voice', '古风少御'),
                                'audition' => "{$localAddr}/古风少御.mp3",
                                'emotion' => $this->emotion('happy,sad,angry,scare,hate,surprise,novel_dialog,narrator')
                            ),
                            array(
                                'id' => 'BV107_streaming',
                                'name' => \Yii::t('voice', '霸气青叔'),
                                'audition' => "{$localAddr}/霸气青叔.mp3",
                                'emotion' => $this->emotion('happy,sad,angry,scare,hate,surprise,novel_dialog,narrator')
                            ),
                            array(
                                'id' => 'BV100_streaming',
                                'name' => \Yii::t('voice', '质朴青年'),
                                'audition' => "{$localAddr}/质朴青年.mp3",
                                'emotion' => $this->emotion('happy,sad,angry,scare,hate,surprise,novel_dialog,narrator')
                            ),
                            array(
                                'id' => 'BV104_streaming',
                                'name' => \Yii::t('voice', '温柔淑女'),
                                'audition' => "{$localAddr}/温柔淑女.mp3",
                                'emotion' => $this->emotion('happy,sad,angry,scare,hate,surprise,novel_dialog,narrator')
                            ),
                            array(
                                'id' => 'BV004_streaming',
                                'name' => \Yii::t('voice', '开朗青年'),
                                'audition' => "{$localAddr}/开朗青年.mp3",
                                'emotion' => $this->emotion('happy,sad,angry,scare,hate,surprise,novel_dialog,narrator')
                            ),
                            array(
                                'id' => 'BV113_streaming',
                                'name' => \Yii::t('voice', '甜宠少御'),
                                'audition' => "{$localAddr}/甜宠少御.mp3",
                                'emotion' => $this->emotion('happy,sad,angry,scare,hate,surprise,novel_dialog,narrator')
                            ),
                            array(
                                'id' => 'BV102_streaming',
                                'name' => \Yii::t('voice', '儒雅青年'),
                                'audition' => "{$localAddr}/儒雅青年.mp3",
                                'emotion' => $this->emotion('happy,sad,angry,scare,hate,surprise,novel_dialog,narrator')
                            ),
                        ),
                ),
                array(
                    'id' => '智能助手',
                    'name' => \Yii::t('voice', '智能助手'),
                    'children' =>
                        array(
                            array(
                                'id' => 'BV405_streaming',
                                'name' => \Yii::t('voice', '甜美小源'),
                                'audition' => "{$localAddr}/甜美小源.mp3",
                                'emotion' => $this->emotion('pleased,sorry,professional,serious')
                            ),
                            array(
                                'id' => 'BV007_streaming',
                                'name' => \Yii::t('voice', '亲切女声'),
                                'audition' => "{$localAddr}/亲切女声.mp3",
                            ),
                            array(
                                'id' => 'BV009_streaming',
                                'name' => \Yii::t('voice', '知性女声'),
                                'audition' => "{$localAddr}/知性女声.mp3",
                                'emotion' => $this->emotion('pleased,sorry,professional,serious')
                            ),
                            array(
                                'id' => 'BV419_streaming',
                                'name' => \Yii::t('voice', '诚诚'),
                                'audition' => "{$localAddr}/诚诚.mp3",
                            ),
                            array(
                                'id' => 'BV415_streaming',
                                'name' => \Yii::t('voice', '童童'),
                                'audition' => "{$localAddr}/童童.mp3",
                            ),
                            array(
                                'id' => 'BV008_streaming',
                                'name' => \Yii::t('voice', '亲切男声'),
                                'audition' => "{$localAddr}/亲切男声.mp3",
                                'emotion' => $this->emotion('pleased,sorry,professional,serious')
                            ),
                        ),
                ),
                array(
                    'id' => '视频配音',
                    'name' => \Yii::t('voice', '视频配音'),
                    'children' =>
                        array(
                            array(
                                'id' => 'BV408_streaming',
                                'name' => \Yii::t('voice', '译制片男声'),
                                'audition' => "{$localAddr}/译制片男声.mp3",
                            ),
                            array(
                                'id' => 'BV426_streaming',
                                'name' => \Yii::t('voice', '懒小羊'),
                                'audition' => "{$localAddr}/懒小羊.mp3",
                            ),
                            array(
                                'id' => 'BV428_streaming',
                                'name' => \Yii::t('voice', '清新文艺女声'),
                                'audition' => "{$localAddr}/清新文艺女声.mp3",
                            ),
                            array(
                                'id' => 'BV403_streaming',
                                'name' => \Yii::t('voice', '鸡汤女声'),
                                'audition' => "{$localAddr}/鸡汤女声.mp3",
                            ),
                            array(
                                'id' => 'BV158_streaming',
                                'name' => \Yii::t('voice', '智慧老者'),
                                'audition' => "{$localAddr}/智慧老者.mp3",
                            ),
                            array(
                                'id' => 'BR001_streaming',
                                'name' => \Yii::t('voice', '说唱小哥'),
                                'audition' => "{$localAddr}/说唱小哥.mp3",
                            ),
                            array(
                                'id' => 'BV410_streaming',
                                'name' => \Yii::t('voice', '活力解说男'),
                                'audition' => "{$localAddr}/活力解说男.mp3",
                            ),
                            ['name' => \Yii::t('voice', '影视解说小帅'), 'id' => 'BV411_streaming', 'audition' => "{$localAddr}/影视解说小帅.mp3"],
                            [
                                'name' => \Yii::t('voice', '解说小帅多情感'),
                                'id' => 'BV437_streaming',
                                'audition' => "{$localAddr}/解说小帅-多情感.mp3",
                                'emotion' => $this->emotion('happy,sad,angry,scare,hate,surprise')
                            ],
                            ['name' => \Yii::t('voice', '影视解说小美'), 'id' => 'BV412_streaming', 'audition' => "{$localAddr}/影视解说小美.mp3"],
                            ['name' => \Yii::t('voice', '纨绔青年'), 'id' => 'BV159_streaming', 'audition' => "{$localAddr}/纨绔青年.mp3"],
                            ['name' => \Yii::t('voice', '直播一姐'), 'id' => 'BV418_streaming', 'audition' => "{$localAddr}/直播一姐.mp3"],
                            ['name' => \Yii::t('voice', '沉稳解说男'), 'id' => 'BV142_streaming', 'audition' => "{$localAddr}/沉稳解说男.mp3"],
                            ['name' => \Yii::t('voice', '潇洒青年'), 'id' => 'BV143_streaming', 'audition' => "{$localAddr}/潇洒青年.mp3"],
                            ['name' => \Yii::t('voice', '阳光男声'), 'id' => 'BV056_streaming', 'audition' => "{$localAddr}/阳光男声.mp3"],
                            ['name' => \Yii::t('voice', '活泼女声'), 'id' => 'BV005_streaming', 'audition' => "{$localAddr}/活泼女声.mp3"],
                            [
                                'name' => \Yii::t('voice', '小萝莉'),
                                'id' => 'BV064_streaming',
                                'audition' => "{$localAddr}/小萝莉.mp3",
                                'emotion' => $this->emotion('happy,sad,angry,scare,hate,surprise')
                            ],
                        ),
                ),
                [
                    'id' => '特色音色',
                    'name' => \Yii::t('voice', '特色音色'),
                    'children' => [
                        ['name' => \Yii::t('voice', '奶气萌娃'), 'id' => 'BV051_streaming', 'audition' => "{$localAddr}/奶气萌娃.mp3"],
                        ['name' => \Yii::t('voice', '动漫海绵'), 'id' => 'BV063_streaming', 'audition' => "{$localAddr}/动漫海绵.mp3"],
                        ['name' => \Yii::t('voice', '动漫海星'), 'id' => 'BV417_streaming', 'audition' => "{$localAddr}/动漫海星.mp3"],
                        ['name' => \Yii::t('voice', '动漫小新'), 'id' => 'BV050_streaming', 'audition' => "{$localAddr}/动漫小新.mp3"],
                        ['name' => \Yii::t('voice', '天才童声'), 'id' => 'BV061_streaming', 'audition' => "{$localAddr}/天才童声.mp3"],
                    ]
                ],
                [
                    'id' => '广告配音',
                    'name' => \Yii::t('voice', '广告配音'),
                    'children' => [
                        ['name' => \Yii::t('voice', '促销男声'), 'id' => 'BV401_streaming', 'audition' => "{$localAddr}/促销男声.mp3"],
                        ['name' => \Yii::t('voice', '促销女声'), 'id' => 'BV402_streaming', 'audition' => "{$localAddr}/促销女声.mp3"],
                        ['name' => \Yii::t('voice', '磁性男声'), 'id' => 'BV006_streaming', 'audition' => "{$localAddr}/磁性男声.mp3"],
                    ]
                ],
                [
                    'id' => '新闻播报',
                    'name' => \Yii::t('voice', '新闻播报'),
                    'children' => [
                        ['name' => \Yii::t('voice', '新闻女声'), 'id' => 'BV011_streaming', 'audition' => "{$localAddr}/新闻女声.mp3"],
                        ['name' => \Yii::t('voice', '新闻男声'), 'id' => 'BV012_streaming', 'audition' => "{$localAddr}/新闻男声.mp3"],
                    ]
                ],
                [
                    'id' => '教育场景',
                    'name' => \Yii::t('voice', '教育场景'),
                    'children' => [
                        ['name' => \Yii::t('voice', '知性姐姐双语'), 'id' => 'BV034_streaming', 'audition' => "{$localAddr}/知性姐姐-双语.mp3"],
                        ['name' => \Yii::t('voice', '温柔小哥'), 'id' => 'BV033_streaming', 'audition' => "{$localAddr}/温柔小哥.mp3"],
                    ]
                ],
                [
                    'id' => '方言',
                    'name' => \Yii::t('voice', '方言'),
                    'children' => [
                        ['name' => \Yii::t('voice', '东北老铁'), 'id' => 'BV021_streaming', 'audition' => "{$localAddr}/东北老铁.mp3"],
                        ['name' => \Yii::t('voice', '东北丫头'), 'id' => 'BV020_streaming', 'audition' => "{$localAddr}/东北丫头.mp3"],
                        [
                            'name' => \Yii::t('voice', '方言灿灿'),
                            'id' => 'BV704_streaming',
                            'audition' => "{$localAddr}/方言灿灿.mp3",
                            'language' => $this->language('cn,zh_dongbei,zh_yueyu,zh_shanghai,zh_xian,zh_chengdu,zh_taipu,zh_guangxi')
                        ],
                        ['name' => \Yii::t('voice', '西安佟掌柜'), 'id' => 'BV210_streaming', 'audition' => "{$localAddr}/西安佟掌柜.mp3"],
                        ['name' => \Yii::t('voice', '沪上阿姐'), 'id' => 'BV217_streaming', 'audition' => "{$localAddr}/沪上阿姐.mp3"],
                        ['name' => \Yii::t('voice', '广西表哥'), 'id' => 'BV213_streaming', 'audition' => "{$localAddr}/广西表哥.mp3"],
                        ['name' => \Yii::t('voice', '甜美台妹'), 'id' => 'BV025_streaming', 'audition' => "{$localAddr}/甜美台妹.mp3"],
                        ['name' => \Yii::t('voice', '台普男声'), 'id' => 'BV227_streaming', 'audition' => "{$localAddr}/台普男声.mp3"],
                        ['name' => \Yii::t('voice', '港剧男神'), 'id' => 'BV026_streaming', 'audition' => "{$localAddr}/港剧男神.mp3"],
                        ['name' => \Yii::t('voice', '广东女仔'), 'id' => 'BV424_streaming', 'audition' => "{$localAddr}/广东女仔.mp3"],
                        ['name' => \Yii::t('voice', '相声演员'), 'id' => 'BV212_streaming', 'audition' => "{$localAddr}/相声演员.mp3"],
                        ['name' => \Yii::t('voice', '重庆小伙'), 'id' => 'BV019_streaming', 'audition' => "{$localAddr}/重庆小伙.mp3"],
                        ['name' => \Yii::t('voice', '四川甜妹儿'), 'id' => 'BV221_streaming', 'audition' => "{$localAddr}/四川甜妹儿.mp3"],
                        ['name' => \Yii::t('voice', '重庆幺妹儿'), 'id' => 'BV423_streaming', 'audition' => "{$localAddr}/重庆幺妹儿.mp3"],
                        ['name' => \Yii::t('voice', '乡村企业家'), 'id' => 'BV214_streaming', 'audition' => "{$localAddr}/乡村企业家.mp3"],
                        ['name' => \Yii::t('voice', '湖南妹坨'), 'id' => 'BV226_streaming', 'audition' => "{$localAddr}/湖南妹坨.mp3"],
                        ['name' => \Yii::t('voice', '长沙靓女'), 'id' => 'BV216_streaming', 'audition' => "{$localAddr}/长沙靓女.mp3"],
                    ]
                ],
                [
                    'id' => '多语种',
                    'name' => \Yii::t('voice', '多语种'),
                    'children' => [
                        [
                            'name' => \Yii::t('voice', '慵懒女声Ava'),
                            'id' => 'BV511_streaming',
                            'audition' => "{$localAddr}/慵懒女声-Ava.mp3",
                            'emotion' => $this->emotion('happy,sad,angry,scare,hate,surprise')
                        ],
                        [
                            'name' => \Yii::t('voice', '议论女声Alicia'),
                            'id' => 'BV505_streaming',
                            'audition' => "{$localAddr}/议论女声-Alicia.mp3"
                        ],
                        [
                            'name' => \Yii::t('voice', '情感女声Lawrence'),
                            'id' => 'BV138_streaming',
                            'audition' => "{$localAddr}/情感女声-Lawrence.mp3",
                            'emotion' => $this->emotion('happy,sad,angry,scare,hate,surprise,novel_dialog,narrator')
                        ],
                        [
                            'name' => \Yii::t('voice', '美式女声Amelia'),
                            'id' => 'BV027_streaming',
                            'audition' => "{$localAddr}/美式女声-Amelia.mp3"
                        ],
                        [
                            'name' => \Yii::t('voice', '讲述女声Amanda'),
                            'id' => 'BV502_streaming',
                            'audition' => "{$localAddr}/讲述女声-Amanda.mp3"
                        ],
                        [
                            'name' => \Yii::t('voice', '活力女声Ariana'),
                            'id' => 'BV503_streaming',
                            'audition' => "{$localAddr}/活力女声-Ariana.mp3"
                        ],
                        [
                            'name' => \Yii::t('voice', '活力男声Jackson'),
                            'id' => 'BV504_streaming',
                            'audition' => "{$localAddr}/活力男声-Jackson.mp3"
                        ],
                        [
                            'name' => \Yii::t('voice', '天才少女'),
                            'id' => 'BV421_streaming',
                            'audition' => "{$localAddr}/天才少女.mp3",
                            'language' => $this->language('cn,en,ja,thth,vivn,ptbr,esmx,id')
                        ],
                        [
                            'name' => 'Stefan',
                            'id' => 'BV702_streaming',
                            'audition' => "{$localAddr}/Stefan.mp3",
                            'language' => $this->language('cn,en,ja,ptbr,esmx,id')
                        ],
                        [
                            'name' => \Yii::t('voice', '天真萌娃Lily'),
                            'id' => 'BV506_streaming',
                            'audition' => "{$localAddr}/天真萌娃-Lily.mp3"
                        ],
                        [
                            'name' => \Yii::t('voice', '亲切女声Anna'),
                            'id' => 'BV040_streaming',
                            'audition' => "{$localAddr}/亲切女声-Anna.mp3",
                            'emotion' => $this->emotion('happy,sad,angry,scare,hate,surprise')
                        ],
                        [
                            'name' => \Yii::t('voice', '澳洲男声Henry'),
                            'id' => 'BV516_streaming',
                            'audition' => "{$localAddr}/澳洲男声-Henry.mp3"
                        ],
                        [
                            'name' => \Yii::t('voice', '元气少女'),
                            'id' => 'BV520_streaming',
                            'audition' => "{$localAddr}/元气少女.mp3"
                        ],
                        [
                            'name' => \Yii::t('voice', '萌系少女'),
                            'id' => 'BV521_streaming',
                            'audition' => "{$localAddr}/萌系少女.mp3"
                        ],
                        [
                            'name' => \Yii::t('voice', '气质女声'),
                            'id' => 'BV522_streaming',
                            'audition' => "{$localAddr}/气质女声.mp3"
                        ],
                        [
                            'name' => \Yii::t('voice', '日语男声'),
                            'id' => 'BV524_streaming',
                            'audition' => "{$localAddr}/日语男声.mp3"
                        ],
                        [
                            'name' => \Yii::t('voice', '活力男声Carlos巴西'),
                            'id' => 'BV531_streaming',
                            'audition' => "{$localAddr}/活力男声Carlos（巴西地区）.mp3"
                        ],
                        [
                            'name' => \Yii::t('voice', '活力女声巴西'),
                            'id' => 'BV530_streaming',
                            'audition' => "{$localAddr}/活力女声（巴西地区）.mp3"
                        ],
                        [
                            'name' => \Yii::t('voice', '气质御姐墨西哥'),
                            'id' => 'BV065_streaming',
                            'audition' => "{$localAddr}/气质御姐（墨西哥地区）.mp3"
                        ]
                    ]
                ]
            ],
        ];
        $list[$this->tts] = $list[$this->ttsLong];
        if ($type) {
            $list = $list[$type] ?? [];
        }

        if (!$type || $type == $this->ttsBig) {
            if (!self::$voiceList) {
                self::$voiceList = VoiceList::find()->where(['status' => 1])->all();
            }
            $voiceList = ArrayHelper::index(self::$voiceList, null, 'voice_type');
            $itemList = [];
            foreach ($voiceList as $name => $item) {
                $child = [];
                /** @var VoiceList $voice */
                foreach ($item as $voice) {
                    if($this->is_home == 2 && !in_array($voice->voice_id, $this->abroad())){
                        continue;
                    }
                    if (strpos($voice->pic, 'http') === false) {
                        $voice->pic = \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . $voice->pic;
                    }
                    $voice->switchData();
                    $child[] = [
                        'primary_id' => $voice->id,
                        'id' => $voice->voice_id,
                        'name' => $voice->name,
                        'audition' => $voice->audio,
                        'emotion' => $this->emotion($voice->emotion),
                        'sex' => $voice->sex,
                        'age' => $voice->age,
                        'pic' => $voice->pic,
                        'language' => \Yii::t('voice', $voice->language)
                    ];
                }
                if(!empty($child)) {
                    $itemList[] = [
                        'id' => $name,
                        'name' => \Yii::t ('voice', $name),
                        'children' => $child,
                    ];
                }
            }
            if (!$list) {
                $list = $itemList;
            } else {
                $list[$this->ttsBig] = $itemList;
            }
        }

        return $json ? Json::encode($list, JSON_UNESCAPED_UNICODE) : $list;
    }

    protected function language($data)
    {
        $language = [
            'cn' => \Yii::t('voice', '中文'),
            'en' => \Yii::t('voice', '英语'),
            'ja' => \Yii::t('voice', '日语'),
            'thth' => \Yii::t('voice', '泰语'),
            'vivn' => \Yii::t('voice', '越南语'),
            'id' => \Yii::t('voice', '印尼语'),
            'ptbr' => \Yii::t('voice', '葡萄牙语'),
            'esmx' => \Yii::t('voice', '西班牙语'),
            'zh_dongbei' => \Yii::t('voice', '东北'),
            'zh_yueyu' => \Yii::t('voice', '粤语'),
            'zh_shanghai' => \Yii::t('voice', '上海'),
            'zh_xian' => \Yii::t('voice', '西安'),
            'zh_chengdu' => \Yii::t('voice', '成都'),
            'zh_taipu' => \Yii::t('voice', '台湾普通话'),
            'zh_guangxi' => \Yii::t('voice', '广西普通话'),
        ];
        $res = [];
        foreach (explode(",", $data) as $item) {
            if (!isset($language[$item])) {
                continue;
            }
            $res[] = ['label' => $language[$item], 'value' => $item];
        }
        return $res;
    }

    protected function emotion($data)
    {
        $emotion = [
            'pleased' => \Yii::t('voice', '愉悦'),
            'sorry' => \Yii::t('voice', '抱歉'),
            'annoyed' => \Yii::t('voice', '嗔怪'),
            'happy' => \Yii::t('voice', '开心'),
            'sad' => \Yii::t('voice', '悲伤'),
            'angry' => \Yii::t('voice', '愤怒'),
            'scare' => \Yii::t('voice', '害怕'),
            'hate' => \Yii::t('voice', '厌恶'),
            'surprise' => \Yii::t('voice', '惊讶'),
            'tear' => \Yii::t('voice', '哭腔'),
            'novel_dialog' => \Yii::t('voice', '平和'),
            'customer_service' => \Yii::t('voice', '客服'),
            'professional' => \Yii::t('voice', '专业'),
            'serious' => \Yii::t('voice', '严肃'),
            'narrator' => \Yii::t('voice', '旁白-舒缓'),
            'narrator_immersive' => \Yii::t('voice', '旁白-沉浸'),
            'comfort' => \Yii::t('voice', '安慰鼓励'),
            'lovey-dovey' => \Yii::t('voice', '撒娇'),
            'energetic' => \Yii::t('voice', '可爱元气'),
            'conniving' => \Yii::t('voice', '绿茶'),
            'tsundere' => \Yii::t('voice', '傲娇'),
            'charming' => \Yii::t('voice', '娇媚'),
            'storytelling' => \Yii::t('voice', '讲故事'),
            'radio' => \Yii::t('voice', '情感电台'),
            'yoga' => \Yii::t('voice', '瑜伽'),
            'advertising' => \Yii::t('voice', '广告'),
            'assistant' => \Yii::t('voice', '助手'),
            'chat' => \Yii::t('voice', '自然对话'),
            'surprised' => \Yii::t('voice', '惊讶'),
            'fear' => \Yii::t('voice', '恐惧'),
            'excited' => \Yii::t('voice', '激动'),
            'coldness' => \Yii::t('voice', '冷漠'),
            'neutral' => \Yii::t('voice', '中性'),
        ];
        $res = [];
        foreach (explode(",", $data) as $item) {
            if (!isset($emotion[$item])) {
                continue;
            }
            $res[] = ['label' => $emotion[$item], 'value' => $item];
        }
        return $res;
    }

    public function data()
    {
        $hostOne = "https://lf3-static.bytednsdoc.com/obj/eden-cn/lm_hz_ihsph/ljhwZthlaukjlkulzlp/portal/bigtts/short_trial_url";
        $hostTwo = "https://lf3-static.bytednsdoc.com/obj/eden-cn/lm_hz_ihsph/ljhwZthlaukjlkulzlp/console/bigtts";
        $avatarOne = "https://lf3-static.bytednsdoc.com/obj/eden-cn/lm_hz_ihsph/ljhwZthlaukjlkulzlp/portal/bigtts/avatar";
        $sql = <<<EOF
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('爽快思思', 'zh_female_shuangkuaisisi_moon_bigtts', '通用场景', '$hostOne/爽快思思.mp3', '/statics/img/voice/1.png', 2, 1, '中文', '{"en":{"name":"Cheerful SiSi"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('温暖阿虎', 'zh_male_wennuanahu_moon_bigtts', '通用场景', '$hostOne/温暖阿虎.mp3', '/statics/img/voice/2.png', 1, 1, '中文', '{"en":{"name":"Warm AhHu"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('少年梓辛', 'zh_male_shaonianzixin_moon_bigtts', '通用场景', '$hostOne/少年梓辛.mp3', '/statics/img/voice/3.png', 1, 2, '中文', '{"en":{"name":"Young ZiXin"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('邻家女孩', 'zh_female_linjianvhai_moon_bigtts', '通用场景', '$hostOne/邻家女孩.mp3', '/statics/img/voice/8.png', 2, 2, '中文', '{"en":{"name":"Girl Next Door"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('渊博小叔', 'zh_male_yuanboxiaoshu_moon_bigtts', '通用场景', '$hostOne/渊博小叔.mp3', '/statics/img/voice/9.png', 1, 3, '中文', '{"en":{"name":"Knowledgeable Uncle"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('阳光青年', 'zh_male_yangguangqingnian_moon_bigtts', '通用场景', '$hostOne/阳光青年.mp3', '/statics/img/voice/10.png', 1, 1, '中文', '{"en":{"name":"Sunny Youth"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('甜美小源', 'zh_female_tianmeixiaoyuan_moon_bigtts', '通用场景', '$hostOne/甜美小源.mp3', '/statics/img/voice/11.png', 2, 1, '中文', '{"en":{"name":"Sweet XiaoYuan"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('清澈梓梓', 'zh_female_qingchezizi_moon_bigtts', '通用场景', '$hostOne/清澈梓梓.mp3', '/statics/img/voice/12.png', 2, 1, '中文', '{"en":{"name":"Qing ZiZi"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('解说小明', 'zh_male_jieshuoxiaoming_moon_bigtts', '通用场景', '$hostOne/解说小明.mp3', '/statics/img/voice/13.png', 1, 1, '中文', '{"en":{"name":"Narrator XiaoMing"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('开朗姐姐', 'zh_female_kailangjiejie_moon_bigtts', '通用场景', '$hostOne/开朗姐姐.mp3', '/statics/img/voice/14.png', 2, 1, '中文', '{"en":{"name":"Cheerful Sister"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('邻家男孩', 'zh_male_linjiananhai_moon_bigtts', '通用场景', '$hostOne/邻家男孩.mp3', '/statics/img/voice/15.png', 1, 1, '中文', '{"en":{"name":"Boy Next Door"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('甜美悦悦', 'zh_female_tianmeiyueyue_moon_bigtts', '通用场景', '$hostOne/甜美悦悦.mp3', '/statics/img/voice/16.png', 2, 1, '中文', '{"en":{"name":"Sweet YueYue"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('心灵鸡汤', 'zh_female_xinlingjitang_moon_bigtts', '通用场景', '$hostOne/心灵鸡汤.mp3', '/statics/img/voice/17.png', 2, 3, '中文', '{"en":{"name":"Inspirational"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('灿灿', 'zh_female_cancan_mars_bigtts', '通用场景', '$hostTwo/zh_female_cancan_mars_bigtts.mp3', '$avatarOne/灿灿.png', 2, 2, '中文', '{"en":{"name":"CanCan"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('清新女声', 'zh_female_qingxinnvsheng_mars_bigtts', '通用场景', '$hostTwo/zh_female_qingxinnvsheng_mars_bigtts.mp3', '$avatarOne/清新女声.png', 2, 1, '中文', '{"en":{"name":"Fresh Female Voice"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('知性女声', 'zh_female_zhixingnvsheng_mars_bigtts', '通用场景', '$hostTwo/zh_female_zhixingnvsheng_mars_bigtts.mp3', '$avatarOne/知性女声.png', 2, 1, '中文', '{"en":{"name":"Intellectual Female Voice"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('清爽男大', 'zh_male_qingshuangnanda_mars_bigtts', '通用场景', '$hostTwo/zh_male_qingshuangnanda_mars_bigtts.mp3', '$avatarOne/清爽男大.png', 1, 1, '中文', '{"en":{"name":"Fresh Male Voice"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('知性温婉', 'ICL_zh_female_zhixingwenwan_tob', '通用场景', '$hostOne/知性温婉.mp3', '$avatarOne/知性温婉.png', 2, 1, '中文', '{"en":{"name":"Intellectual and gentle"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('暖心体贴', 'ICL_zh_male_nuanxintitie_tob', '通用场景', '$hostOne/暖心体贴.mp3', '$avatarOne/暖心体贴.png', 1, 1, '中文', '{"en":{"name":"Warm and considerate"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('温柔文雅', 'ICL_zh_female_wenrouwenya_tob', '通用场景', '$hostOne/温柔文雅.mp3', '$avatarOne/温柔文雅.png', 2, 1, '中文', '{"en":{"name":"Gentle and refined"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('开朗轻快', 'ICL_zh_male_kailangqingkuai_tob', '通用场景', '$hostOne/开朗轻快.mp3', '$avatarOne/开朗轻快.png', 1, 1, '中文', '{"en":{"name":"Cheerful and light"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('活泼爽朗', 'ICL_zh_male_huoposhuanglang_tob', '通用场景', '$hostOne/活泼爽朗.mp3', '$avatarOne/活泼爽朗.png', 1, 1, '中文', '{"en":{"name":"Lively and cheerful"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('率真小伙', 'ICL_zh_male_shuaizhenxiaohuo_tob', '通用场景', '$hostOne/率真小伙.mp3', '$avatarOne/率真小伙.png', 1, 1, '中文', '{"en":{"name":"Straightforward young man"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('温柔小哥', 'zh_male_wenrouxiaoge_mars_bigtts', '通用场景', '$hostOne/温柔小哥.mp3', '$avatarOne/温柔小哥.png', 1, 1, '中文', '{"en":{"name":"Gentle young man"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('甜美桃子', 'zh_female_tianmeitaozi_mars_bigtts', '通用场景', '$hostOne/甜美桃子.mp3', '$avatarOne/甜美桃子.png', 2, 1, '中文', '{"en":{"name":"Sweet Peach"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('京腔侃爷', 'zh_male_jingqiangkanye_moon_bigtts', '趣味方言', '$hostOne/京腔侃爷.mp3', '/statics/img/voice/18.png', 1, 1, '北京口音', '{"en":{"name":"Beijing Talker"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('湾湾小何', 'zh_female_wanwanxiaohe_moon_bigtts', '趣味方言', '$hostOne/湾湾小何.mp3', '/statics/img/voice/19.png', 2, 1, '台湾口音', '{"en":{"name":"Taiwanese XiaoHe"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('湾区大叔', 'zh_female_wanqudashu_moon_bigtts', '趣味方言', '$hostOne/湾区大叔.mp3', '/statics/img/voice/20.png', 1, 3, '广东口音', '{"en":{"name":"Bay Area Uncle"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('呆萌川妹', 'zh_female_daimengchuanmei_moon_bigtts', '趣味方言', '$hostOne/呆萌川妹.mp3', '/statics/img/voice/21.png', 2, 2, '四川口音', '{"en":{"name":"Cute Sichuan Girl"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('广州德哥', 'zh_male_guozhoudege_moon_bigtts', '趣味方言', '$hostOne/广州德哥.mp3', '/statics/img/voice/22.png', 1, 3, '广东口音', '{"en":{"name":"Guangzhou Brother De"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('北京小爷', 'zh_male_beijingxiaoye_moon_bigtts', '趣味方言', '$hostOne/北京小爷.mp3', '/statics/img/voice/23.png', 1, 1, '北京口音', '{"en":{"name":"Beijing Young Master"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('浩宇小哥', 'zh_male_haoyuxiaoge_moon_bigtts', '趣味方言', '$hostOne/浩宇小哥.mp3', '/statics/img/voice/24.png', 1, 1, '青岛口音', '{"en":{"name":"HaoYu Brother"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('广西远舟', 'zh_male_guangxiyuanzhou_moon_bigtts', '趣味方言', '$hostOne/广西远舟.mp3', '/statics/img/voice/25.png', 1, 1, '广西口音', '{"en":{"name":"Guangxi YuanZhou"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('妹坨洁儿', 'zh_female_meituojieer_moon_bigtts', '趣味方言', '$hostOne/妹坨洁儿.mp3', '/statics/img/voice/26.png', 2, 2, '长沙口音', '{"en":{"name":"Sister JieEr"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('豫州子轩', 'zh_male_yuzhouzixuan_moon_bigtts', '趣味方言', '$hostOne/豫州子轩.mp3', '/statics/img/voice/27.png', 1, 1, '河南口音', '{"en":{"name":"YuZhou ZiXuan"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('高冷御姐', 'zh_female_gaolengyujie_moon_bigtts', '角色扮演', '$hostOne/高冷御姐.mp3', '/statics/img/voice/28.png', 2, 3, '中文', '{"en":{"name":"Cold Lady"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('傲娇霸总', 'zh_male_aojiaobazong_moon_bigtts', '角色扮演', '$hostOne/傲娇霸总.mp3', '/statics/img/voice/29.png', 1, 3, '中文', '{"en":{"name":"Tsundere CEO"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('魅力女友', 'zh_female_meilinvyou_moon_bigtts', '角色扮演', '$hostOne/魅力女友.mp3', '/statics/img/voice/30.png', 2, 1, '中文', '{"en":{"name":"Charming Girlfriend"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('深夜播客', 'zh_male_shenyeboke_moon_bigtts', '角色扮演', '$hostOne/深夜播客.mp3', '/statics/img/voice/31.png', 1, 3, '中文', '{"en":{"name":"Late Night Podcast"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('柔美女友', 'zh_female_sajiaonvyou_moon_bigtts', '角色扮演', '$hostOne/柔美女友.mp3', '/statics/img/voice/32.png', 2, 1, '中文', '{"en":{"name":"Gentle Girlfriend"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('撒娇学妹', 'zh_female_yuanqinvyou_moon_bigtts', '角色扮演', '$hostOne/撒娇学妹.mp3', '/statics/img/voice/33.png', 2, 2, '中文', '{"en":{"name":"Cute Junior Sister"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('病弱少女', 'ICL_zh_female_bingruoshaonv_tob', '角色扮演', '$hostOne/病弱少女.mp3', '/statics/img/voice/34.png', 2, 2, '中文', '{"en":{"name":"Sickly Girl"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('活泼女孩', 'ICL_zh_female_huoponvhai_tob', '角色扮演', '$hostOne/活泼女孩.mp3', '/statics/img/voice/35.png', 2, 2, '中文', '{"en":{"name":"Lively Girl"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('东方浩然', 'zh_male_dongfanghaoran_moon_bigtts', '角色扮演', '$hostOne/东方浩然.mp3', '/statics/img/voice/39.png', 1, 3, '中文', '{"en":{"name":"DongFang HaoRan"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('婆婆', 'zh_female_popo_mars_bigtts', '角色扮演', '$hostTwo/zh_female_popo_mars_bigtts.mp3', '/statics/img/voice/36.png', 2, 4, '中文', '{"en":{"name":"Mother-in-law"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('奶气萌娃', 'zh_male_naiqimengwa_mars_bigtts', '角色扮演', '$hostTwo/zh_male_naiqimengwa_mars_bigtts.mp3', '$avatarOne/奶气萌娃.png', 1, 5, '中文', '{"en":{"name":"Baby Voice"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('可爱女生', 'ICL_zh_female_keainvsheng_tob', '角色扮演', '$hostOne/可爱女生.mp3', '$avatarOne/可爱女生.png', 2, 2, '中文', '{"en":{"name":"Cute Girl"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('成熟姐姐', 'ICL_zh_female_chengshujiejie_tob', '角色扮演', '$hostOne/成熟姐姐.mp3', '$avatarOne/成熟姐姐.png', 2, 1, '中文', '{"en":{"name":"Mature Sister"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('病娇姐姐', 'ICL_zh_female_bingjiaojiejie_tob', '角色扮演', '$hostOne/病娇姐姐.mp3', '$avatarOne/病娇姐姐.png', 2, 1, '中文', '{"en":{"name":"Yandere Sister"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('优柔帮主', 'ICL_zh_male_youroubangzhu_tob', '角色扮演', '$hostOne/优柔帮主.mp3', '$avatarOne/优柔帮主.png', 1, 1, '中文', '{"en":{"name":"Gentle Gang Leader"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('优柔公子', 'ICL_zh_male_yourougongzi_tob', '角色扮演', '$hostOne/优柔公子.mp3', '$avatarOne/优柔公子.png', 1, 1, '中文', '{"en":{"name":"Gentle Young Master"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('妩媚御姐', 'ICL_zh_female_wumeiyujie_tob', '角色扮演', '$hostOne/妩媚御姐.mp3', '$avatarOne/妩媚御姐.png', 2, 1, '中文', '{"en":{"name":"Charming Elder Sister"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('调皮公主', 'ICL_zh_female_tiaopigongzhu_tob', '角色扮演', '$hostOne/调皮公主.mp3', '$avatarOne/调皮公主.png', 2, 2, '中文', '{"en":{"name":"Naughty Princess"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('傲娇女友', 'ICL_zh_female_aojiaonvyou_tob', '角色扮演', '$hostOne/傲娇女友.mp3', '$avatarOne/傲娇女友.png', 2, 1, '中文', '{"en":{"name":"Tsundere Girlfriend"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('贴心男友', 'ICL_zh_male_tiexinnanyou_tob', '角色扮演', '$hostOne/贴心男友.mp3', '$avatarOne/贴心男友.png', 1, 1, '中文', '{"en":{"name":"Considerate Boyfriend"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('少年将军', 'ICL_zh_male_shaonianjiangjun_tob', '角色扮演', '$hostOne/少年将军.mp3', '$avatarOne/少年将军.png', 1, 1, '中文', '{"en":{"name":"Young General"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('贴心女友', 'ICL_zh_female_tiexinnvyou_tob', '角色扮演', '$hostOne/贴心女友.mp3', '$avatarOne/贴心女友.png', 2, 1, '中文', '{"en":{"name":"Considerate Girlfriend"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('病娇哥哥', 'ICL_zh_male_bingjiaogege_tob', '角色扮演', '$hostOne/病娇哥哥.mp3', '$avatarOne/病娇哥哥.png', 1, 1, '中文', '{"en":{"name":"Yandere Brother"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('学霸男同桌', 'ICL_zh_male_xuebanantongzhuo_tob', '角色扮演', '$hostOne/学霸男同桌.mp3', '$avatarOne/学霸男同桌.png', 1, 2, '中文', '{"en":{"name":"Smart Male Deskmate"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('幽默叔叔', 'ICL_zh_male_youmoshushu_tob', '角色扮演', '$hostOne/幽默叔叔.mp3', '$avatarOne/幽默叔叔.png', 1, 3, '中文', '{"en":{"name":"Humorous Uncle"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('性感御姐', 'ICL_zh_female_xingganyujie_tob', '角色扮演', '$hostOne/性感御姐.mp3', '$avatarOne/性感御姐.png', 2, 1, '中文', '{"en":{"name":"Sexy Elder Sister"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('假小子', 'ICL_zh_female_jiaxiaozi_tob', '角色扮演', '$hostOne/假小子.mp3', '$avatarOne/假小子.png', 2, 2, '中文', '{"en":{"name":"Tomboy"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('冷峻上司', 'ICL_zh_male_lengjunshangsi_tob', '角色扮演', '$hostOne/冷峻上司.mp3', '$avatarOne/冷峻上司.png', 1, 1, '中文', '{"en":{"name":"Cold Boss"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('温柔男同桌', 'ICL_zh_male_wenrounantongzhuo_tob', '角色扮演', '$hostOne/温柔男同桌.mp3', '$avatarOne/温柔男同桌.png', 1, 2, '中文', '{"en":{"name":"Gentle Male Deskmate"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('病娇弟弟', 'ICL_zh_male_bingjiaodidi_tob', '角色扮演', '$hostOne/病娇弟弟.mp3', '$avatarOne/病娇弟弟.png', 1, 1, '中文', '{"en":{"name":"Yandere Little Brother"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('幽默大爷', 'ICL_zh_male_youmodaye_tob', '角色扮演', '$hostOne/幽默大爷.mp3', '$avatarOne/幽默大爷.png', 1, 4, '中文', '{"en":{"name":"Humorous Old Man"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('傲慢少爷', 'ICL_zh_male_aomanshaoye_tob', '角色扮演', '$hostOne/傲慢少爷.mp3', '$avatarOne/傲慢少爷.png', 1, 1, '中文', '{"en":{"name":"Arrogant Young Master"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('神秘法师', 'ICL_zh_male_shenmifashi_tob', '角色扮演', '$hostOne/神秘法师.mp3', '$avatarOne/神秘法师.png', 1, 3, '中文', '{"en":{"name":"Mysterious Mage"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('潇洒随性', 'ICL_zh_male_xiaosasuixing_tob', '角色扮演', '$hostOne/潇洒随性.mp3', '$avatarOne/潇洒随性.png', 1, 1, '中文', '{"en":{"name":"Carefree and casual"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('绿茶小哥', 'ICL_zh_male_lvchaxiaoge_tob', '角色扮演', '$hostOne/绿茶小哥.mp3', '$avatarOne/绿茶小哥.png', 1, 1, '中文', '{"en":{"name":"Green Tea Guy"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('娇弱萝莉', 'ICL_zh_female_jiaoruoluoli_tob', '角色扮演', '$hostOne/娇弱萝莉.mp3', '$avatarOne/娇弱萝莉.png', 2, 2, '中文', '{"en":{"name":"Delicate girl"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('冷淡疏离', 'ICL_zh_male_lengdanshuli_tob', '角色扮演', '$hostOne/冷淡疏离.mp3', '$avatarOne/冷淡疏离.png', 1, 1, '中文', '{"en":{"name":"Cold and distant"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('憨厚敦实', 'ICL_zh_male_hanhoudunshi_tob', '角色扮演', '$hostOne/憨厚敦实.mp3', '$avatarOne/憨厚敦实.png', 1, 1, '中文', '{"en":{"name":"Honest and down-to-earth"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('傲气凌人', 'ICL_zh_male_aiqilingren_tob', '角色扮演', '$hostOne/傲气凌人.mp3', '$avatarOne/傲气凌人.png', 1, 1, '中文', '{"en":{"name":"Arrogant and overbearing"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('活泼刁蛮', 'ICL_zh_female_huopodiaoman_tob', '角色扮演', '$hostOne/活泼刁蛮.mp3', '$avatarOne/活泼刁蛮.png', 2, 2, '中文', '{"en":{"name":"Lively and spoiled"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('固执病娇', 'ICL_zh_male_guzhibingjiao_tob', '角色扮演', '$hostOne/固执病娇.mp3', '$avatarOne/固执病娇.png', 1, 1, '中文', '{"en":{"name":"Stubborn and spoiled"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('撒娇粘人', 'ICL_zh_male_sajiaonianren_tob', '角色扮演', '$hostOne/撒娇粘人.mp3', '$avatarOne/撒娇粘人.png', 1, 1, '中文', '{"en":{"name":"Being clingy and affectionate"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('傲慢娇声', 'ICL_zh_female_aomanjiaosheng_tob', '角色扮演', '$hostOne/傲慢娇声.mp3', '$avatarOne/傲慢娇声.png', 2, 2, '中文', '{"en":{"name":"Arrogant and delicate voice"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('腹黑公子', 'ICL_zh_male_fuheigongzi_tob', '角色扮演', '$hostOne/腹黑公子.mp3', '$avatarOne/腹黑公子.png', 1, 1, '中文', '{"en":{"name":"The scheming young master"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('诡异神秘', 'ICL_zh_male_guiyishenmi_tob', '角色扮演', '$hostOne/诡异神秘.mp3', '$avatarOne/诡异神秘.png', 1, 1, '中文', '{"en":{"name":"Strange and mysterious"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('儒雅才俊', 'ICL_zh_male_ruyacaijun_tob', '角色扮演', '$hostOne/儒雅才俊.mp3', '$avatarOne/儒雅才俊.png', 1, 1, '中文', '{"en":{"name":"Refined and talented man"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('病娇白莲', 'ICL_zh_male_bingjiaobailian_tob', '角色扮演', '$hostOne/病娇白莲.mp3', '$avatarOne/病娇白莲.png', 1, 1, '中文', '{"en":{"name":"The sickly and delicate Bai Lian"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('正直青年', 'ICL_zh_male_zhengzhiqingnian_tob', '角色扮演', '$hostOne/正直青年.mp3', '$avatarOne/正直青年.png', 1, 1, '中文', '{"en":{"name":"Upright youth"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('娇憨女王', 'ICL_zh_female_jiaohannvwang_tob', '角色扮演', '$hostOne/娇憨女王.mp3', '$avatarOne/娇憨女王.png', 2, 1, '中文', '{"en":{"name":"The Charming and naive Queen"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('病娇萌妹', 'ICL_zh_female_bingjiaomengmei_tob', '角色扮演', '$hostOne/病娇萌妹.mp3', '$avatarOne/病娇萌妹.png', 2, 2, '中文', '{"en":{"name":"Sickly and cute girl"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('青涩小生', 'ICL_zh_male_qingsenaigou_tob', '角色扮演', '$hostOne/青涩小生.mp3', '$avatarOne/青涩小生.png', 1, 1, '中文', '{"en":{"name":"Young and inexperienced boy"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('纯真学弟', 'ICL_zh_male_chunzhenxuedi_tob', '角色扮演', '$hostOne/纯真学弟.mp3', '$avatarOne/纯真学弟.png', 1, 1, '中文', '{"en":{"name":"Innocent Junior"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('暖心学姐', 'ICL_zh_female_nuanxinxuejie_tob', '角色扮演', '$hostOne/暖心学姐.mp3', '$avatarOne/暖心学姐.png', 2, 1, '中文', '{"en":{"name":"Warm-hearted senior"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('Skye', 'zh_female_shuangkuaisisi_moon_bigtts', '多语种', '$hostOne/Skye.mp3', '/statics/img/voice/Skye.png', 2, 1, '美式英语', '{"en":{"name":"Skye"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('Alvin', 'zh_male_wennuanahu_moon_bigtts', '多语种', '$hostOne/Alvin.mp3', '/statics/img/voice/Alvin.png', 1, 1, '美式英语', '{"en":{"name":"Alvin"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('Brayan', 'zh_male_shaonianzixin_moon_bigtts', '多语种', '$hostOne/Brayan.mp3', '/statics/img/voice/Brayan.png', 1, 2, '美式英语', '{"en":{"name":"Brayan"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('かずね（和音）', 'multi_male_jingqiangkanye_moon_bigtts', '多语种', '$hostOne/和音.mp3', '/statics/img/voice/4.png', 1, 1, '日语', '{"en":{"name":"かずね（和音）"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('javier_alvaro', 'multi_male_jingqiangkanye_moon_bigtts', '多语种', '$hostOne/Javier.mp3', '/statics/img/voice/Javier.png', 1, 1, '西班牙语', '{"en":{"name":"javier_alvaro"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('はるこ（晴子）', 'multi_female_shuangkuaisisi_moon_bigtts', '多语种', '$hostOne/晴子.mp3', '/statics/img/voice/5.png', 2, 1, '日语', '{"en":{"name":"はるこ（晴子）"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('Esmeralda', 'multi_female_shuangkuaisisi_moon_bigtts', '多语种', '$hostOne/Esmeralda.mp3', '/statics/img/voice/Esmeralda.png', 2, 1, '西班牙语', '{"en":{"name":"Esmeralda"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('あけみ（朱美）', 'multi_female_gaolengyujie_moon_bigtts', '多语种', '$hostOne/朱美.mp3', '/statics/img/voice/6.png', 2, 3, '日语', '{"en":{"name":"あけみ（朱美）"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('ひろし（広志）', 'multi_male_wanqudashu_moon_bigtts', '多语种', '$hostOne/広志.mp3', '/statics/img/voice/7.png', 1, 3, '日语', '{"en":{"name":"ひろし（広志）"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('Roberto', 'multi_male_wanqudashu_moon_bigtts', '多语种', '$hostOne/Roberto.mp3', '/statics/img/voice/Roberto.png', 1, 3, '西班牙语', '{"en":{"name":"Roberto"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('Harmony', 'zh_male_jingqiangkanye_moon_bigtts', '多语种', '$hostOne/Harmony.mp3', '/statics/img/voice/Harmony.png', 1, 1, '美式英语', '{"en":{"name":"Harmony"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('Adam', 'en_male_adam_mars_bigtts', '多语种', '$hostTwo/en_male_adam_mars_bigtts.mp3', '$avatarOne/Adam.png', 1, 3, '美式英语', '{"en":{"name":"Adam"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('Morgan', 'zh_male_jieshuonansheng_mars_bigtts', '多语种', '$hostTwo/Morgan.mp3', '$avatarOne/Morgan.png', 1, 3, '美式英语', '{"en":{"name":"Morgan"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('Smith', 'en_male_smith_mars_bigtts', '多语种', '$hostTwo/en_male_smith_mars_bigtts.mp3', '$avatarOne/Smith.png', 1, 3, '英式英语', '{"en":{"name":"Smith"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('Dryw', 'en_male_dryw_mars_bigtts', '多语种', '$hostTwo/en_male_dryw_mars_bigtts.mp3', '$avatarOne/Dryw.png', 1, 3, '澳洲英语', '{"en":{"name":"Dryw"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('shiny', 'zh_female_cancan_mars_bigtts', '多语种', '$hostTwo/shiny.mp3', '$avatarOne/shiny.png', 2, 2, '美式英语', '{"en":{"name":"shiny"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('Cutey', 'zh_female_mengyatou_mars_bigtts', '多语种', '$hostTwo/Cutey.mp3', '$avatarOne/Cutey.png', 2, 2, '美式英语', '{"en":{"name":"Cutey"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('Candy', 'zh_female_tiexinnvsheng_mars_bigtts', '多语种', '$hostTwo/Candy.mp3', '$avatarOne/Candy.png', 2, 1, '美式英语', '{"en":{"name":"Candy"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('Sarah', 'en_female_sarah_mars_bigtts', '多语种', '$hostTwo/en_female_sarah_mars_bigtts.mp3', '$avatarOne/Sarah.png', 2, 3, '澳洲英语', '{"en":{"name":"Sarah"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('Anna', 'en_female_anna_mars_bigtts', '多语种', '$hostTwo/en_female_anna_mars_bigtts.mp3', '$avatarOne/Anna.png', 2, 3, '英式英语', '{"en":{"name":"Anna"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('Hope', 'zh_female_jitangmeimei_mars_bigtts', '多语种', '$hostTwo/Hope.mp3', '$avatarOne/Hope.png', 2, 3, '美式英语', '{"en":{"name":"Hope"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('Amanda', 'en_female_amanda_mars_bigtts', '多语种', '$hostOne/Amanda.mp3', '$avatarOne/Amanda.png', 2, 2, '美式英语', '{"en":{"name":"Amanda"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('Jackson', 'en_male_jackson_mars_bigtts', '多语种', '$hostOne/Jackson.mp3', '$avatarOne/Jackson.png', 1, 2, '美式英语', '{"en":{"name":"Jackson"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('和蔼奶奶', 'ICL_zh_female_heainainai_tob', '视频配音', '$hostOne/和蔼奶奶.mp3', '/statics/img/voice/36.png', 2, 4, '中文', '{"en":{"name":"Kind Grandma"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('邻居阿姨', 'ICL_zh_female_linjuayi_tob', '视频配音', '$hostOne/邻居阿姨.mp3', '/statics/img/voice/37.png', 2, 3, '中文', '{"en":{"name":"Neighbor Auntie"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('温柔小雅', 'zh_female_wenrouxiaoya_moon_bigtts', '视频配音', '$hostOne/温柔小雅.mp3', '/statics/img/voice/38.png', 2, 1, '中文', '{"en":{"name":"Gentle XiaoYa"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('天才童声', 'zh_male_tiancaitongsheng_mars_bigtts', '视频配音', '$hostTwo/zh_male_tiancaitongsheng_mars_bigtts.mp3', '$avatarOne/天才童声.png', 1, 5, '中文', '{"en":{"name":"Genius Child Voice"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('猴哥', 'zh_male_sunwukong_mars_bigtts', '视频配音', '$hostTwo/zh_male_sunwukong_mars_bigtts.mp3', '$avatarOne/猴哥.png', 1, 1, '中文', '{"en":{"name":"Monkey Brother"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('熊二', 'zh_male_xionger_mars_bigtts', '视频配音', '$hostTwo/zh_male_xionger_mars_bigtts.mp3', '$avatarOne/熊二.png', 1, 2, '中文', '{"en":{"name":"Bear Two"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('佩奇猪', 'zh_female_peiqi_mars_bigtts', '视频配音', '$hostTwo/zh_female_peiqi_mars_bigtts.mp3', '$avatarOne/佩奇猪.png', 2, 5, '中文', '{"en":{"name":"Peppa Pig"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('武则天', 'zh_female_wuzetian_mars_bigtts', '视频配音', '$hostTwo/zh_female_wuzetian_mars_bigtts.mp3', '$avatarOne/武则天.png', 2, 3, '中文', '{"en":{"name":"Wu ZeTian"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('顾姐', 'zh_female_gujie_mars_bigtts', '视频配音', '$hostTwo/zh_female_gujie_mars_bigtts.mp3', '$avatarOne/顾姐.png', 2, 1, '中文', '{"en":{"name":"Sister Gu"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('樱桃丸子', 'zh_female_yingtaowanzi_mars_bigtts', '视频配音', '$hostTwo/zh_female_yingtaowanzi_mars_bigtts.mp3', '$avatarOne/樱桃丸子.png', 2, 5, '中文', '{"en":{"name":"Cherry Maruko"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('广告解说', 'zh_male_chunhui_mars_bigtts', '视频配音', '$hostTwo/zh_male_chunhui_mars_bigtts.mp3', '$avatarOne/广告解说.png', 1, 3, '中文', '{"en":{"name":"Advertisement Narration"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('少儿故事', 'zh_female_shaoergushi_mars_bigtts', '视频配音', '$hostTwo/zh_female_shaoergushi_mars_bigtts.mp3', '$avatarOne/少儿故事.png', 2, 2, '中文', '{"en":{"name":"Children\'s Story"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('四郎', 'zh_male_silang_mars_bigtts', '视频配音', '$hostTwo/zh_male_silang_mars_bigtts.mp3', '$avatarOne/四郎.png', 1, 3, '中文', '{"en":{"name":"SiLang"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('磁性解说男声', 'zh_male_jieshuonansheng_mars_bigtts', '视频配音', '$hostTwo/zh_male_jieshuonansheng_mars_bigtts.mp3', '$avatarOne/磁性解说男声.png', 1, 3, '中文', '{"en":{"name":"Magnetic Male Narrator"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('鸡汤妹妹', 'zh_female_jitangmeimei_mars_bigtts', '视频配音', '$hostTwo/zh_female_jitangmeimei_mars_bigtts.mp3', '$avatarOne/鸡汤妹妹.png', 2, 3, '中文', '{"en":{"name":"Inspirational Sister"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('贴心女声', 'zh_female_tiexinnvsheng_mars_bigtts', '视频配音', '$hostTwo/zh_female_tiexinnvsheng_mars_bigtts.mp3', '$avatarOne/贴心女声.png', 2, 1, '中文', '{"en":{"name":"Considerate Female Voice"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('俏皮女声', 'zh_female_qiaopinvsheng_mars_bigtts', '视频配音', '$hostTwo/zh_female_qiaopinvsheng_mars_bigtts.mp3', '$avatarOne/俏皮女声.png', 2, 1, '中文', '{"en":{"name":"Playful Female Voice"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('萌丫头', 'zh_female_mengyatou_mars_bigtts', '视频配音', '$hostTwo/zh_female_mengyatou_mars_bigtts.mp3', '$avatarOne/萌丫头.png', 2, 2, '中文', '{"en":{"name":"Cute Girl"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('懒音绵宝', 'zh_male_lanxiaoyang_mars_bigtts', '视频配音', '$hostOne/懒音绵宝.mp3', '$avatarOne/懒音绵宝.png', 1, 1, '中文', '{"en":{"name":"Lazy Voice Cute Baby"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('亮嗓萌仔', 'zh_male_dongmanhaimian_mars_bigtts', '视频配音', '$hostOne/亮嗓萌仔.mp3', '$avatarOne/亮嗓萌仔.png', 1, 1, '中文', '{"en":{"name":"Bright Voice Cutie"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('悬疑解说', 'zh_male_changtianyi_mars_bigtts', '有声阅读', '$hostTwo/zh_male_changtianyi_mars_bigtts.mp3', '$avatarOne/悬疑解说.png', 1, 3, '中文', '{"en":{"name":"Mystery Narration"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('儒雅青年', 'zh_male_ruyaqingnian_mars_bigtts', '有声阅读', '$hostTwo/zh_male_ruyaqingnian_mars_bigtts.mp3', '$avatarOne/儒雅青年.png', 1, 1, '中文', '{"en":{"name":"Elegant Youth"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('霸气青叔', 'zh_male_baqiqingshu_mars_bigtts', '有声阅读', '$hostTwo/zh_male_baqiqingshu_mars_bigtts.mp3', '$avatarOne/霸气青叔.png', 1, 3, '中文', '{"en":{"name":"Dominant Uncle"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('擎苍', 'zh_male_qingcang_mars_bigtts', '有声阅读', '$hostTwo/zh_male_qingcang_mars_bigtts.mp3', '$avatarOne/擎苍.png', 1, 3, '中文', '{"en":{"name":"QingCang"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('古风少御', 'zh_female_gufengshaoyu_mars_bigtts', '有声阅读', '$hostTwo/zh_female_gufengshaoyu_mars_bigtts.mp3', '$avatarOne/古风少御.png', 2, 1, '中文', '{"en":{"name":"Ancient Style Young Lady"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('温柔淑女', 'zh_female_wenroushunv_mars_bigtts', '有声阅读', '$hostTwo/zh_female_wenroushunv_mars_bigtts.mp3', '$avatarOne/温柔淑女.png', 2, 1, '中文', '{"en":{"name":"Gentle Lady"}}');    
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('反卷青年', 'zh_male_fanjuanqingnian_mars_bigtts', '有声阅读', '$hostOne/反卷青年.mp3', '$avatarOne/反卷青年.png', 1, 1, '中文', '{"en":{"name":"Anti-studying Youth"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('活力小哥', 'zh_male_yangguangqingnian_mars_bigtts', '有声阅读', '$hostOne/活力小哥.mp3', '$avatarOne/活力小哥.png', 1, 1, '中文', '{"en":{"name":"Energetic Young Man"}}');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`, `emotion`) VALUES 
('北京小爷（多情感）', 'zh_male_beijingxiaoye_emo_v2_mars_bigtts', '多情感', '$hostOne/北京小爷.mp3', '$avatarOne/北京小爷.png', 1, 1, '中文', '{"en":{"name":"Beijing Young Master (Multi-emotion)"}}', 'angry,surprised,fear,excited,coldness,neutral');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`, `emotion`) VALUES 
('柔美女友（多情感）', 'zh_female_roumeinvyou_emo_v2_mars_bigtts', '多情感', '$hostOne/柔美女友.mp3', '$avatarOne/柔美女友.png', 2, 1, '中文', '{"en":{"name":"Gentle Girlfriend (Multi-emotion)"}}', 'happy,sad,angry,surprised,fear,hate,excited,coldness,neutral');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`, `emotion`) VALUES 
('阳光青年（多情感）', 'zh_male_yangguangqingnian_emo_v2_mars_bigtts', '多情感', '$hostOne/阳光青年.mp3', '$avatarOne/阳光青年.png', 1, 1, '中文', '{"en":{"name":"Sunny Youth (Multi-emotion)"}}', 'happy,sad,angry,fear,excited,coldness,neutral');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`, `emotion`) VALUES 
('魅力女友（多情感）', 'zh_female_meilinvyou_emo_v2_mars_bigtts', '多情感', '$hostOne/魅力女友.mp3', '$avatarOne/魅力女友.png', 2, 1, '中文', '{"en":{"name":"Charming Girlfriend (Multi-emotion)"}}', 'sad,fear,neutral');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`, `emotion`) VALUES 
('爽快思思（多情感）', 'zh_female_shuangkuaisisi_emo_v2_mars_bigtts', '多情感', '$hostOne/爽快思思.mp3', '$avatarOne/爽快思思.png', 2, 1, '中文', '{"en":{"name":"Cheerful SiSi (Multi-emotion)"}}', 'sad,happy,angry,surprised,excited,coldness,neutral');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`, `emotion`) VALUES 
('高冷御姐（多情感）', 'zh_female_gaolengyujie_emo_v2_mars_bigtts', '多情感', '$hostOne/高冷御姐.mp3', '$avatarOne/高冷御姐.png', 2, 3, '中文', '{"en":{"name":"Cold Lady (Multi-emotion)"}}', 'happy,sad,angry,surprised,fear,hate,excited,coldness,neutral');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`, `emotion`) VALUES 
('傲娇霸总（多情感）', 'zh_male_aojiaobazong_emo_v2_mars_bigtts', '多情感', '$hostOne/傲娇霸总.mp3', '$avatarOne/傲娇霸总.png', 1, 3, '中文', '{"en":{"name":"Tsundere CEO (Multi-emotion)"}}', 'neutral,happy,angry,hate');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`, `emotion`) VALUES 
('广州德哥（多情感）', 'zh_male_guangzhoudege_emo_mars_bigtts', '多情感', '$hostOne/广州德哥.mp3', '$avatarOne/广州德哥.png', 1, 3, '中文', '{"en":{"name":"Guangzhou Brother De (Multi-emotion)"}}', 'angry,fear,neutral');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`, `emotion`) VALUES 
('京腔侃爷（多情感）', 'zh_male_jingqiangkanye_emo_mars_bigtts', '多情感', '$hostOne/京腔侃爷.mp3', '$avatarOne/京腔侃爷.png', 1, 1, '中文', '{"en":{"name":"Beijing Talker (Multi-emotion)"}}', 'happy,angry,surprised,hate,neutral');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`, `emotion`) VALUES 
('邻居阿姨（多情感）', 'zh_female_linjuayi_emo_v2_mars_bigtts', '多情感', '$hostOne/邻居阿姨.mp3', '$avatarOne/邻居阿姨.png', 2, 3, '中文', '{"en":{"name":"Neighbor Auntie (Multi-emotion)"}}', 'neutral,angry,coldness,sad,surprised');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`, `emotion`) VALUES 
('优柔公子（多情感）', 'zh_male_yourougongzi_emo_v2_mars_bigtts', '多情感', '$hostOne/优柔公子.mp3', '$avatarOne/优柔公子.png', 1, 1, '中文', '{"en":{"name":"Gentle Young Master (Multi-emotion)"}}', 'happy,angry,fear,hate,excited,neutral,sad');
INSERT INTO `wstx_voice_list` (`name`, `voice_id`, `voice_type`, `audio`, `pic`, `sex`, `age`, `language`, `language_data`) VALUES 
('暖阳女声', 'zh_female_kefunvsheng_mars_bigtts', '客服场景', '$hostOne/暖阳女声.mp3', '$avatarOne/暖阳女声.png', 2, 1, '仅中文', '{"en":{"name":"Warm Sun Female Voice"}}');
EOF;
        return $sql;
    }
}
