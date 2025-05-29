<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\data;

use yii\helpers\Json;

class VoiceForm extends BaseForm
{
    public function voiceType($type = null, $json = true)
    {
        $localAddr = \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/voice';
        $host = "https://lf3-static.bytednsdoc.com/obj/eden-cn/lm_hz_ihsph/ljhwZthlaukjlkulzlp/portal/bigtts/short_trial_url";
        $addr = \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/img/voice';
        $newHost = "https://lf3-static.bytednsdoc.com/obj/eden-cn/lm_hz_ihsph/ljhwZthlaukjlkulzlp/console/bigtts/";
        $list = [
            $this->ttsLong => [
                array(
                    'id' => 'common',
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
                                'emotion' => $this->emotion('customer_service、happy、sad、angry、scare、hate、surprise、comfort、storytelling、advertising、assistant')
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
                                'emotion' => $this->emotion('pleased、sorry、annoyed、customer_service、professional、serious、happy、sad、angry、scare、hate、surprise、tear、conniving、comfort、radio、lovey-dovey、tsundere、charming、yoga、storytelling')
                            ),
                            array(
                                'id' => 'BV700_streaming',
                                'name' => \Yii::t('voice', '灿灿'),
                                'audition' => "{$localAddr}/灿灿.mp3",
                                'emotion' => $this->emotion('pleased、sorry、annoyed、customer_service、professional、serious、happy、sad、angry、scare、hate、surprise、tear、conniving、comfort、radio、lovey-dovey、tsundere、charming、yoga、storytelling')
                            ),
                            array(
                                'id' => 'BV705_streaming',
                                'name' => \Yii::t('voice', '炀炀'),
                                'audition' => "{$localAddr}/炀炀.mp3",
                                'emotion' => $this->emotion('chat、pleased、sorry、annoyed、comfort、storytelling')
                            ),
                            array(
                                'id' => 'BV701_V2_streaming',
                                'name' => \Yii::t('voice', '擎苍2.0'),
                                'audition' => "{$localAddr}/擎苍 2.0.mp3",
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、tear、novel_dialog、narrator、narrator_immersive')
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
                    'id' => 'yousheng',
                    'name' => \Yii::t('voice', '有声阅读'),
                    'children' =>
                        array(
                            array(
                                'id' => 'BV701_streaming',
                                'name' => \Yii::t('voice', '擎苍'),
                                'audition' => "{$localAddr}/擎苍.mp3",
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、tear、novel_dialog、narrator、narrator_immersive')
                            ),
                            array(
                                'id' => 'BV123_streaming',
                                'name' => \Yii::t('voice', '阳光青年'),
                                'audition' => "{$localAddr}/阳光青年.mp3",
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、novel_dialog')
                            ),
                            array(
                                'id' => 'BV120_streaming',
                                'name' => \Yii::t('voice', '反卷青年'),
                                'audition' => "{$localAddr}/反卷青年.mp3",
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、novel_dialog')
                            ),
                            array(
                                'id' => 'BV119_streaming',
                                'name' => \Yii::t('voice', '通用赘婿'),
                                'audition' => "{$localAddr}/通用赘婿.mp3",
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、novel_dialog、narrator')
                            ),
                            array(
                                'id' => 'BV115_streaming',
                                'name' => \Yii::t('voice', '古风少御'),
                                'audition' => "{$localAddr}/古风少御.mp3",
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、novel_dialog、narrator')
                            ),
                            array(
                                'id' => 'BV107_streaming',
                                'name' => \Yii::t('voice', '霸气青叔'),
                                'audition' => "{$localAddr}/霸气青叔.mp3",
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、novel_dialog、narrator')
                            ),
                            array(
                                'id' => 'BV100_streaming',
                                'name' => \Yii::t('voice', '质朴青年'),
                                'audition' => "{$localAddr}/质朴青年.mp3",
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、novel_dialog、narrator')
                            ),
                            array(
                                'id' => 'BV104_streaming',
                                'name' => \Yii::t('voice', '温柔淑女'),
                                'audition' => "{$localAddr}/温柔淑女.mp3",
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、novel_dialog、narrator')
                            ),
                            array(
                                'id' => 'BV004_streaming',
                                'name' => \Yii::t('voice', '开朗青年'),
                                'audition' => "{$localAddr}/开朗青年.mp3",
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、novel_dialog、narrator')
                            ),
                            array(
                                'id' => 'BV113_streaming',
                                'name' => \Yii::t('voice', '甜宠少御'),
                                'audition' => "{$localAddr}/甜宠少御.mp3",
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、novel_dialog、narrator')
                            ),
                            array(
                                'id' => 'BV102_streaming',
                                'name' => \Yii::t('voice', '儒雅青年'),
                                'audition' => "{$localAddr}/儒雅青年.mp3",
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、novel_dialog、narrator')
                            ),
                        ),
                ),
                array(
                    'id' => 'zhushou',
                    'name' => \Yii::t('voice', '智能助手'),
                    'children' =>
                        array(
                            array(
                                'id' => 'BV405_streaming',
                                'name' => \Yii::t('voice', '甜美小源'),
                                'audition' => "{$localAddr}/甜美小源.mp3",
                                'emotion' => $this->emotion('pleased、sorry、professional、serious')
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
                                'emotion' => $this->emotion('pleased、sorry、professional、serious')
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
                                'emotion' => $this->emotion('pleased、sorry、professional、serious')
                            ),
                        ),
                ),
                array(
                    'id' => 'peiyin',
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
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise')
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
                                'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise')
                            ],
                        ),
                ),
                [
                    'id' => 'tese',
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
                    'id' => 'guangao',
                    'name' => \Yii::t('voice', '广告配音'),
                    'children' => [
                        ['name' => \Yii::t('voice', '促销男声'), 'id' => 'BV401_streaming', 'audition' => "{$localAddr}/促销男声.mp3"],
                        ['name' => \Yii::t('voice', '促销女声'), 'id' => 'BV402_streaming', 'audition' => "{$localAddr}/促销女声.mp3"],
                        ['name' => \Yii::t('voice', '磁性男声'), 'id' => 'BV006_streaming', 'audition' => "{$localAddr}/磁性男声.mp3"],
                    ]
                ],
                [
                    'id' => 'xinwen',
                    'name' => \Yii::t('voice', '新闻播报'),
                    'children' => [
                        ['name' => \Yii::t('voice', '新闻女声'), 'id' => 'BV011_streaming', 'audition' => "{$localAddr}/新闻女声.mp3"],
                        ['name' => \Yii::t('voice', '新闻男声'), 'id' => 'BV012_streaming', 'audition' => "{$localAddr}/新闻男声.mp3"],
                    ]
                ],
                [
                    'id' => 'jiaoyu',
                    'name' => \Yii::t('voice', '教育场景'),
                    'children' => [
                        ['name' => \Yii::t('voice', '知性姐姐双语'), 'id' => 'BV034_streaming', 'audition' => "{$localAddr}/知性姐姐-双语.mp3"],
                        ['name' => \Yii::t('voice', '温柔小哥'), 'id' => 'BV033_streaming', 'audition' => "{$localAddr}/温柔小哥.mp3"],
                    ]
                ],
                [
                    'id' => 'fangyan',
                    'name' => \Yii::t('voice', '方言'),
                    'children' => [
                        ['name' => \Yii::t('voice', '东北老铁'), 'id' => 'BV021_streaming', 'audition' => "{$localAddr}/东北老铁.mp3"],
                        ['name' => \Yii::t('voice', '东北丫头'), 'id' => 'BV020_streaming', 'audition' => "{$localAddr}/东北丫头.mp3"],
                        [
                            'name' => \Yii::t('voice', '方言灿灿'),
                            'id' => 'BV704_streaming',
                            'audition' => "{$localAddr}/方言灿灿.mp3",
                            'language' => $this->language('cn、zh_dongbei、zh_yueyu、zh_shanghai、zh_xian、zh_chengdu、zh_taipu、zh_guangxi')
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
                    'id' => 'language',
                    'name' => \Yii::t('voice', '多语种'),
                    'children' => [
                        [
                            'name' => \Yii::t('voice', '慵懒女声Ava'),
                            'id' => 'BV511_streaming',
                            'audition' => "{$localAddr}/慵懒女声-Ava.mp3",
                            'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise')
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
                            'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise、novel_dialog、narrator')
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
                            'language' => $this->language('cn、en、ja、thth、vivn、ptbr、esmx、id')
                        ],
                        [
                            'name' => 'Stefan',
                            'id' => 'BV702_streaming',
                            'audition' => "{$localAddr}/Stefan.mp3",
                            'language' => $this->language('cn、en、ja、ptbr、esmx、id')
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
                            'emotion' => $this->emotion('happy、sad、angry、scare、hate、surprise')
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
            $this->ttsBig => [
                [
                    'id' => 'common',
                    'name' => \Yii::t('voice', '通用场景'),
                    'children' => [
                        [
                            'name' => \Yii::t('voice', '爽快思思'),
                            'id' => 'zh_female_shuangkuaisisi_moon_bigtts',
                            'audition' => "{$host}/爽快思思.mp3",
                            'sex' => '2', // 1男；2女
                            'age' => '1', // 1青年；2少年/少女；3中年；4老年
                            'pic' => "{$addr}/1.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '温暖阿虎'),
                            'id' => 'zh_male_wennuanahu_moon_bigtts',
                            'audition' => "{$host}/温暖阿虎.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/2.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '少年梓辛'),
                            'id' => 'zh_male_shaonianzixin_moon_bigtts',
                            'audition' => "{$host}/少年梓辛.mp3",
                            'sex' => '1',
                            'age' => '2',
                            'pic' => "{$addr}/3.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '邻家女孩'),
                            'id' => 'zh_female_linjianvhai_moon_bigtts',
                            'audition' => "{$host}/邻家女孩.mp3",
                            'sex' => '2',
                            'age' => '2',
                            'pic' => "{$addr}/8.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '渊博小叔'),
                            'id' => 'zh_male_yuanboxiaoshu_moon_bigtts',
                            'audition' => "{$host}/渊博小叔.mp3",
                            'sex' => '1',
                            'age' => '3',
                            'pic' => "{$addr}/9.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '阳光青年'),
                            'id' => 'zh_male_yangguangqingnian_moon_bigtts',
                            'audition' => "{$host}/阳光青年.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/10.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '甜美小源'),
                            'id' => 'zh_female_tianmeixiaoyuan_moon_bigtts',
                            'audition' => "{$host}/甜美小源.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/11.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '清澈梓梓'),
                            'id' => 'zh_female_qingchezizi_moon_bigtts',
                            'audition' => "{$host}/清澈梓梓.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/12.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '解说小明'),
                            'id' => 'zh_male_jieshuoxiaoming_moon_bigtts',
                            'audition' => "{$host}/解说小明.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/13.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '开朗姐姐'),
                            'id' => 'zh_female_kailangjiejie_moon_bigtts',
                            'audition' => "{$host}/开朗姐姐.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/14.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '邻家男孩'),
                            'id' => 'zh_male_linjiananhai_moon_bigtts',
                            'audition' => "{$host}/邻家男孩.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/15.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '甜美悦悦'),
                            'id' => 'zh_female_tianmeiyueyue_moon_bigtts',
                            'audition' => "{$host}/甜美悦悦.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/16.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '心灵鸡汤'),
                            'id' => 'zh_female_xinlingjitang_moon_bigtts',
                            'audition' => "{$host}/心灵鸡汤.mp3",
                            'sex' => '2',
                            'age' => '3',
                            'pic' => "{$addr}/17.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '灿灿'),
                            'id' => 'zh_female_cancan_mars_bigtts',
                            'audition' => "{$newHost}/zh_female_cancan_mars_bigtts.mp3",
                            'sex' => '2', // 1男；2女
                            'age' => '2', // 1青年；2少年/少女；3中年；4老年
                            'pic' => "{$addr}/1.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '邻家小妹'),
                            'id' => 'zh_female_linjia_mars_bigtts',
                            'audition' => "{$newHost}/zh_female_linjia_mars_bigtts.mp3",
                            'sex' => '2',
                            'age' => '2',
                            'pic' => "{$addr}/1.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '清新女声'),
                            'id' => 'zh_female_qingxinnvsheng_mars_bigtts',
                            'audition' => "{$newHost}/zh_female_qingxinnvsheng_mars_bigtts.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/1.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '知性女声'),
                            'id' => 'zh_female_zhixingnvsheng_mars_bigtts',
                            'audition' => "{$newHost}/zh_female_zhixingnvsheng_mars_bigtts.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/1.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '清爽男大'),
                            'id' => 'zh_male_qingshuangnanda_mars_bigtts',
                            'audition' => "{$newHost}/zh_male_qingshuangnanda_mars_bigtts.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/2.png",
                        ],
                    ]
                ],
                [
                    'id' => 'fanyang',
                    'name' => \Yii::t('voice', '趣味方言'),
                    'children' => [
                        [
                            'name' => \Yii::t('voice', '京腔侃爷'),
                            'id' => 'zh_male_jingqiangkanye_moon_bigtts',
                            'audition' => "{$host}/京腔侃爷.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/18.png",
                            'other' => \Yii::t('voice', '北京口音'),
                        ],
                        [
                            'name' => \Yii::t('voice', '湾湾小何'),
                            'id' => 'zh_female_wanwanxiaohe_moon_bigtts',
                            'audition' => "{$host}/湾湾小何.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/19.png",
                            'other' => \Yii::t('voice', '台湾口音'),
                        ],
                        [
                            'name' => \Yii::t('voice', '湾区大叔'),
                            'id' => 'zh_female_wanqudashu_moon_bigtts',
                            'audition' => "{$host}/湾区大叔.mp3",
                            'sex' => '1',
                            'age' => '3',
                            'pic' => "{$addr}/20.png",
                            'other' => \Yii::t('voice', '广东口音'),
                        ],
                        [
                            'name' => \Yii::t('voice', '呆萌川妹'),
                            'id' => 'zh_female_daimengchuanmei_moon_bigtts',
                            'audition' => "{$host}/呆萌川妹.mp3",
                            'sex' => '2',
                            'age' => '2',
                            'pic' => "{$addr}/21.png",
                            'other' => \Yii::t('voice', '四川口音'),
                        ],
                        [
                            'name' => \Yii::t('voice', '广州德哥'),
                            'id' => 'zh_male_guozhoudege_moon_bigtts',
                            'audition' => "{$host}/广州德哥.mp3",
                            'sex' => '1',
                            'age' => '3',
                            'pic' => "{$addr}/22.png",
                            'other' => \Yii::t('voice', '广东口音'),
                        ],
                        [
                            'name' => \Yii::t('voice', '北京小爷'),
                            'id' => 'zh_male_beijingxiaoye_moon_bigtts',
                            'audition' => "{$host}/北京小爷.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/23.png",
                            'other' => \Yii::t('voice', '北京口音'),
                        ],
                        [
                            'name' => \Yii::t('voice', '浩宇小哥'),
                            'id' => 'zh_male_haoyuxiaoge_moon_bigtts',
                            'audition' => "{$host}/浩宇小哥.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/24.png",
                            'other' => \Yii::t('voice', '青岛口音'),
                        ],
                        [
                            'name' => \Yii::t('voice', '广西远舟'),
                            'id' => 'zh_male_guangxiyuanzhou_moon_bigtts',
                            'audition' => "{$host}/广西远舟.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/25.png",
                            'other' => \Yii::t('voice', '广西口音'),
                        ],
                        [
                            'name' => \Yii::t('voice', '妹坨洁儿'),
                            'id' => 'zh_female_meituojieer_moon_bigtts',
                            'audition' => "{$host}/妹坨洁儿.mp3",
                            'sex' => '2',
                            'age' => '2',
                            'pic' => "{$addr}/26.png",
                            'other' => \Yii::t('voice', '长沙口音'),
                        ],
                        [
                            'name' => \Yii::t('voice', '豫州子轩'),
                            'id' => 'zh_male_yuzhouzixuan_moon_bigtts',
                            'audition' => "{$host}/豫州子轩.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/27.png",
                            'other' => \Yii::t('voice', '河南口音'),
                        ],
                    ]
                ],
                [
                    'id' => 'juese',
                    'name' => \Yii::t('voice', '角色扮演'),
                    'children' => [
                        [
                            'name' => \Yii::t('voice', '高冷御姐'),
                            'id' => 'zh_female_gaolengyujie_moon_bigtts',
                            'audition' => "{$host}/高冷御姐.mp3",
                            'sex' => '2',
                            'age' => '3',
                            'pic' => "{$addr}/28.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '傲娇霸总'),
                            'id' => 'zh_male_aojiaobazong_moon_bigtts',
                            'audition' => "{$host}/傲娇霸总.mp3",
                            'sex' => '1',
                            'age' => '3',
                            'pic' => "{$addr}/29.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '魅力女友'),
                            'id' => 'zh_female_meilinvyou_moon_bigtts',
                            'audition' => "{$host}/魅力女友.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/30.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '深夜播客'),
                            'id' => 'zh_male_shenyeboke_moon_bigtts',
                            'audition' => "{$host}/深夜播客.mp3",
                            'sex' => '1',
                            'age' => '3',
                            'pic' => "{$addr}/31.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '柔美女友'),
                            'id' => 'zh_female_sajiaonvyou_moon_bigtts',
                            'audition' => "{$host}/柔美女友.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/32.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '撒娇学妹'),
                            'id' => 'zh_female_yuanqinvyou_moon_bigtts',
                            'audition' => "{$host}/撒娇学妹.mp3",
                            'sex' => '2',
                            'age' => '2',
                            'pic' => "{$addr}/33.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '病弱少女'),
                            'id' => 'ICL_zh_female_bingruoshaonv_tob',
                            'audition' => "{$host}/病弱少女.mp3",
                            'sex' => '2',
                            'age' => '2',
                            'pic' => "{$addr}/34.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '活泼女孩'),
                            'id' => 'ICL_zh_female_huoponvhai_tob',
                            'audition' => "{$host}/活泼女孩.mp3",
                            'sex' => '2',
                            'age' => '2',
                            'pic' => "{$addr}/35.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '东方浩然'),
                            'id' => 'zh_male_dongfanghaoran_moon_bigtts',
                            'audition' => "{$host}/东方浩然.mp3",
                            'sex' => '1',
                            'age' => '3',
                            'pic' => "{$addr}/39.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '婆婆'),
                            'id' => 'zh_female_popo_mars_bigtts',
                            'audition' => "{$newHost}/zh_female_popo_mars_bigtts.mp3",
                            'sex' => '2',
                            'age' => '4', // 1青年；2少年/少女；3中年；4老年
                            'pic' => "{$addr}/36.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '奶气萌娃'),
                            'id' => 'zh_male_naiqimengwa_mars_bigtts',
                            'audition' => "{$newHost}/zh_male_naiqimengwa_mars_bigtts.mp3",
                            'sex' => '1',
                            'age' => '5',
                            'pic' => "{$addr}/15.png",
                        ],
                    ]
                ],
                [
                    'id' => 'yuzhong',
                    'name' => \Yii::t('voice', '多语种'),
                    'children' => [
                        [
                            'name' => 'Skye',
                            'id' => 'zh_female_shuangkuaisisi_moon_bigtts' . $this->repeat,
                            'audition' => "{$host}/Skye.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/Skye.png",
                            'other' => \Yii::t('voice', '美式英语'),
                        ],
                        [
                            'name' => 'Alvin',
                            'id' => 'zh_male_wennuanahu_moon_bigtts' . $this->repeat,
                            'audition' => "{$host}/Alvin.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/Alvin.png",
                            'other' => \Yii::t('voice', '美式英语'),
                        ],
                        [
                            'name' => 'Brayan',
                            'id' => 'zh_male_shaonianzixin_moon_bigtts' . $this->repeat,
                            'audition' => "{$host}/Brayan.mp3",
                            'sex' => '1',
                            'age' => '2',
                            'pic' => "{$addr}/Brayan.png",
                            'other' => \Yii::t('voice', '美式英语'),
                        ],
                        [
                            'name' => 'かずね（和音）',
                            'id' => 'multi_male_jingqiangkanye_moon_bigtts',
                            'audition' => "{$host}/和音.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/4.png",
                            'other' => \Yii::t('voice', '日语'),
                        ],
                        [
                            'name' => 'javier_alvaro',
                            'id' => 'multi_male_jingqiangkanye_moon_bigtts' . $this->repeat,
                            'audition' => "{$host}/Javier.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/Javier.png",
                            'other' => \Yii::t('voice', '西班牙语'),
                        ],
                        [
                            'name' => 'はるこ（晴子）',
                            'id' => 'multi_female_shuangkuaisisi_moon_bigtts',
                            'audition' => "{$host}/晴子.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/5.png",
                            'other' => \Yii::t('voice', '日语'),
                        ],
                        [
                            'name' => 'Esmeralda',
                            'id' => 'multi_female_shuangkuaisisi_moon_bigtts' . $this->repeat,
                            'audition' => "{$host}/Esmeralda.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/Esmeralda.png",
                            'other' => \Yii::t('voice', '西班牙语'),
                        ],
                        [
                            'name' => 'あけみ（朱美）',
                            'id' => 'multi_female_gaolengyujie_moon_bigtts',
                            'audition' => "{$host}/朱美.mp3",
                            'sex' => '2',
                            'age' => '3',
                            'pic' => "{$addr}/6.png",
                            'other' => \Yii::t('voice', '日语'),
                        ],
                        [
                            'name' => 'ひろし（広志）',
                            'id' => 'multi_male_wanqudashu_moon_bigtts',
                            'audition' => "{$host}/広志.mp3",
                            'sex' => '1',
                            'age' => '3',
                            'pic' => "{$addr}/7.png",
                            'other' => \Yii::t('voice', '日语'),
                        ],
                        [
                            'name' => 'Roberto',
                            'id' => 'multi_male_wanqudashu_moon_bigtts' . $this->repeat,
                            'audition' => "{$host}/Roberto.mp3",
                            'sex' => '1',
                            'age' => '3',
                            'pic' => "{$addr}/Roberto.png",
                            'other' => \Yii::t('voice', '西班牙语'),
                        ],
                        [
                            'name' => 'Harmony',
                            'id' => 'zh_male_jingqiangkanye_moon_bigtts' . $this->repeat,
                            'audition' => "{$host}/Harmony.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/Harmony.png",
                            'other' => \Yii::t('voice', '美式英语'),
                        ],
                        [
                            'name' => 'Adam',
                            'id' => 'en_male_adam_mars_bigtts',
                            'audition' => "{$newHost}/en_male_adam_mars_bigtts.mp3",
                            'sex' => '1',
                            'age' => '3', // 1青年；2少年/少女；3中年；4老年；5儿童
                            'pic' => "{$addr}/Javier.png",
                            'other' => \Yii::t('voice', '美式英语'),
                        ],
                        [
                            'name' => 'Morgan',
                            'id' => 'zh_male_jieshuonansheng_mars_bigtts' . $this->repeat,
                            'audition' => "{$newHost}/Morgan.mp3",
                            'sex' => '1',
                            'age' => '3',
                            'pic' => "{$addr}/Javier.png",
                            'other' => \Yii::t('voice', '美式英语')
                        ],
                        [
                            'name' => 'Smith',
                            'id' => 'en_male_smith_mars_bigtts',
                            'audition' => "{$newHost}/en_male_smith_mars_bigtts.mp3",
                            'sex' => '1',
                            'age' => '3', // 1青年；2少年/少女；3中年；4老年；5儿童
                            'pic' => "{$addr}/Javier.png",
                            'other' => \Yii::t('voice', '英式英语')
                        ],
                        [
                            'name' => 'Dryw',
                            'id' => 'en_male_dryw_mars_bigtts',
                            'audition' => "{$newHost}/en_male_dryw_mars_bigtts.mp3",
                            'sex' => '1',
                            'age' => '3',
                            'pic' => "{$addr}/Javier.png",
                            'other' => \Yii::t('voice', '澳洲英语')
                        ],
                        [
                            'name' => 'shiny',
                            'id' => 'zh_female_cancan_mars_bigtts' . $this->repeat,
                            'audition' => "{$newHost}/shiny.mp3",
                            'sex' => '2',
                            'age' => '2',
                            'pic' => "{$addr}/Esmeralda.png",
                            'other' => \Yii::t('voice', '美式英语')
                        ],
                        [
                            'name' => 'Cutey',
                            'id' => 'zh_female_mengyatou_mars_bigtts' . $this->repeat,
                            'audition' => "{$newHost}/Cutey.mp3",
                            'sex' => '2',
                            'age' => '2',
                            'pic' => "{$addr}/Esmeralda.png",
                            'other' => \Yii::t('voice', '美式英语')
                        ],
                        [
                            'name' => 'Candy',
                            'id' => 'zh_female_tiexinnvsheng_mars_bigtts' . $this->repeat,
                            'audition' => "{$newHost}/Candy.mp3",
                            'sex' => '2',
                            'age' => '1', // 1青年；2少年/少女；3中年；4老年；5儿童
                            'pic' => "{$addr}/Esmeralda.png",
                            'other' => \Yii::t('voice', '美式英语')
                        ],
                        [
                            'name' => 'Sarah',
                            'id' => 'en_female_sarah_mars_bigtts',
                            'audition' => "{$newHost}/en_female_sarah_mars_bigtts.mp3",
                            'sex' => '2',
                            'age' => '3',
                            'pic' => "{$addr}/Esmeralda.png",
                            'other' => \Yii::t('voice', '澳洲英语')
                        ],
                        [
                            'name' => 'Anna',
                            'id' => 'en_female_anna_mars_bigtts',
                            'audition' => "{$newHost}/en_female_anna_mars_bigtts.mp3",
                            'sex' => '2',
                            'age' => '3',
                            'pic' => "{$addr}/Esmeralda.png",
                            'other' => \Yii::t('voice', '英式英语')
                        ],
                        [
                            'name' => 'Hope',
                            'id' => 'zh_female_jitangmeimei_mars_bigtts' . $this->repeat,
                            'audition' => "{$newHost}/Hope.mp3",
                            'sex' => '2',
                            'age' => '3', // 1青年；2少年/少女；3中年；4老年；5儿童
                            'pic' => "{$addr}/Esmeralda.png",
                            'other' => \Yii::t('voice', '美式英语')
                        ],
                    ]
                ],
                [
                    'id' => 'peiyin',
                    'name' => \Yii::t('voice', '视频配音'),
                    'children' => [
                        [
                            'name' => \Yii::t('voice', '和蔼奶奶'),
                            'id' => 'ICL_zh_female_heainainai_tob',
                            'audition' => "{$host}/和蔼奶奶.mp3",
                            'sex' => '2',
                            'age' => '4',
                            'pic' => "{$addr}/36.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '邻居阿姨'),
                            'id' => 'ICL_zh_female_linjuayi_tob',
                            'audition' => "{$host}/邻居阿姨.mp3",
                            'sex' => '2',
                            'age' => '3',
                            'pic' => "{$addr}/37.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '温柔小雅'),
                            'id' => 'zh_female_wenrouxiaoya_moon_bigtts',
                            'audition' => "{$host}/温柔小雅.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/38.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '天才童声'),
                            'id' => 'zh_male_tiancaitongsheng_mars_bigtts',
                            'audition' => "{$newHost}/zh_male_tiancaitongsheng_mars_bigtts.mp3",
                            'sex' => '1',
                            'age' => '5',
                            'pic' => "{$addr}/2.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '猴哥'),
                            'id' => 'zh_male_sunwukong_mars_bigtts',
                            'audition' => "{$newHost}/zh_male_sunwukong_mars_bigtts.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/2.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '熊二'),
                            'id' => 'zh_male_xionger_mars_bigtts',
                            'audition' => "{$newHost}/zh_male_xionger_mars_bigtts.mp3",
                            'sex' => '1',
                            'age' => '2',
                            'pic' => "{$addr}/2.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '佩奇猪'),
                            'id' => 'zh_female_peiqi_mars_bigtts',
                            'audition' => "{$newHost}/zh_female_peiqi_mars_bigtts.mp3",
                            'sex' => '2',
                            'age' => '5',
                            'pic' => "{$addr}/1.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '武则天'),
                            'id' => 'zh_female_wuzetian_mars_bigtts',
                            'audition' => "{$newHost}/zh_female_wuzetian_mars_bigtts.mp3",
                            'sex' => '2',
                            'age' => '3',
                            'pic' => "{$addr}/1.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '顾姐'),
                            'id' => 'zh_female_gujie_mars_bigtts',
                            'audition' => "{$newHost}/zh_female_gujie_mars_bigtts.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/1.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '樱桃丸子'),
                            'id' => 'zh_female_yingtaowanzi_mars_bigtts',
                            'audition' => "{$newHost}/zh_female_yingtaowanzi_mars_bigtts.mp3",
                            'sex' => '2',
                            'age' => '5',
                            'pic' => "{$addr}/1.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '广告解说'),
                            'id' => 'zh_male_chunhui_mars_bigtts',
                            'audition' => "{$newHost}/zh_male_chunhui_mars_bigtts.mp3",
                            'sex' => '1',
                            'age' => '3',
                            'pic' => "{$addr}/2.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '少儿故事'),
                            'id' => 'zh_female_shaoergushi_mars_bigtts',
                            'audition' => "{$newHost}/zh_female_shaoergushi_mars_bigtts.mp3",
                            'sex' => '2',
                            'age' => '2',
                            'pic' => "{$addr}/1.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '四郎'),
                            'id' => 'zh_male_silang_mars_bigtts',
                            'audition' => "{$newHost}/zh_male_silang_mars_bigtts.mp3",
                            'sex' => '1',
                            'age' => '3',
                            'pic' => "{$addr}/2.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '磁性解说男声'),
                            'id' => 'zh_male_jieshuonansheng_mars_bigtts',
                            'audition' => "{$newHost}/zh_male_jieshuonansheng_mars_bigtts.mp3",
                            'sex' => '1',
                            'age' => '3',
                            'pic' => "{$addr}/2.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '鸡汤妹妹'),
                            'id' => 'zh_female_jitangmeimei_mars_bigtts',
                            'audition' => "{$newHost}/zh_female_jitangmeimei_mars_bigtts.mp3",
                            'sex' => '2',
                            'age' => '3', // 1青年；2少年/少女；3中年；4老年；5儿童
                            'pic' => "{$addr}/1.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '贴心女声'),
                            'id' => 'zh_female_tiexinnvsheng_mars_bigtts',
                            'audition' => "{$newHost}/zh_female_tiexinnvsheng_mars_bigtts.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/1.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '俏皮女声'),
                            'id' => 'zh_female_qiaopinvsheng_mars_bigtts',
                            'audition' => "{$newHost}/zh_female_qiaopinvsheng_mars_bigtts.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/1.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '萌丫头'),
                            'id' => 'zh_female_mengyatou_mars_bigtts',
                            'audition' => "{$newHost}/zh_female_mengyatou_mars_bigtts.mp3",
                            'sex' => '2',
                            'age' => '2',
                            'pic' => "{$addr}/1.png",
                        ],
                    ]
                ],
                [
                    'id' => 'yousheng',
                    'name' => \Yii::t('voice', '有声阅读'),
                    'children' => [
                        [
                            'name' => \Yii::t('voice', '悬疑解说'),
                            'id' => 'zh_male_changtianyi_mars_bigtts',
                            'audition' => "{$newHost}/zh_male_changtianyi_mars_bigtts.mp3",
                            'sex' => '1',
                            'age' => '3',
                            'pic' => "{$addr}/2.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '儒雅青年'),
                            'id' => 'zh_male_ruyaqingnian_mars_bigtts',
                            'audition' => "{$newHost}/zh_male_ruyaqingnian_mars_bigtts.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/2.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '霸气青叔'),
                            'id' => 'zh_male_baqiqingshu_mars_bigtts',
                            'audition' => "{$newHost}/zh_male_baqiqingshu_mars_bigtts.mp3",
                            'sex' => '1',
                            'age' => '3',
                            'pic' => "{$addr}/2.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '擎苍'),
                            'id' => 'zh_male_qingcang_mars_bigtts',
                            'audition' => "{$newHost}/zh_male_qingcang_mars_bigtts.mp3",
                            'sex' => '1',
                            'age' => '3',
                            'pic' => "{$addr}/2.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '阳光青年'),
                            'id' => 'zh_male_yangguangqingnian_mars_bigtts',
                            'audition' => "{$newHost}/zh_male_yangguangqingnian_mars_bigtts.mp3",
                            'sex' => '1',
                            'age' => '1',
                            'pic' => "{$addr}/2.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '古风少御'),
                            'id' => 'zh_female_gufengshaoyu_mars_bigtts',
                            'audition' => "{$newHost}/zh_female_gufengshaoyu_mars_bigtts.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/1.png",
                        ],
                        [
                            'name' => \Yii::t('voice', '温柔淑女'),
                            'id' => 'zh_female_wenroushunv_mars_bigtts',
                            'audition' => "{$newHost}/zh_female_wenroushunv_mars_bigtts.mp3",
                            'sex' => '2',
                            'age' => '1',
                            'pic' => "{$addr}/1.png",
                        ],
                    ]
                ]
            ]
        ];
        $list[$this->tts] = $list[$this->ttsLong];
        if ($type) {
            $list = $list[$type] ?? [];
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
        foreach (explode("、", $data) as $item) {
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
        ];
        $res = [];
        foreach (explode("、", $data) as $item) {
            if (!isset($emotion[$item])) {
                continue;
            }
            $res[] = ['label' => $emotion[$item], 'value' => $item];
        }
        return $res;
    }
}
