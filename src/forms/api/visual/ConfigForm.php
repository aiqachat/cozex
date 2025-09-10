<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\api\visual;

use app\forms\common\volcengine\ark\ImageGenerate;
use app\forms\common\volcengine\sdk\CVSync2AsyncSubmitTask;
use app\forms\mall\visual\SettingForm;
use app\helpers\ArrayHelper;
use app\models\Model;
use app\models\VisualImage;
use app\models\VisualVideo;

class ConfigForm extends Model
{
    /**
     * @see VisualImage::$is_home
     */
    public $is_home;
    public $function_type;
    public $get;

    public function rules()
    {
        return [
            [['is_home'], 'integer'],
            [['function_type'], 'string'],
            [['is_home'], 'default', 'value' => 1],
            [['get'], 'default', 'value' => 'video'],
        ];
    }

    public function videoAll($isFore = false)
    {
        if($this->function_type == VisualVideo::DREAM_NAME){
            $videoModels = $this->dreamVideo();
            $tab = SettingForm::TAB_BASIC;
        }else {
            // 获取火山方舟的模型
            $videoModels = $this->arkVideo();
            $tab = $this->is_home == 1 ? SettingForm::TAB_ARK : SettingForm::TAB_ARK_GLOBAL;
        }
        if ($isFore) {
            $newItem = ArrayHelper::index ($videoModels, null, 'label');
            $config = (new SettingForm(['tab' => $tab]))->config();
            foreach ((array)$config['video_model'] as $k => $item) {
                if (!isset($newItem[$k])) {
                    continue;
                }
                if (!$item['open']) {
                    unset($newItem[$k]);
                    continue;
                }
                foreach ($newItem[$k] as &$i) {
                    $i = array_merge($i, $item);
                }
                unset($i);
                if ($item['only']) {
                    $newItem = [$k => $newItem[$k]];
                    break;
                }
            }
            $videoModels = [];
            foreach ($newItem as $item) {
                $videoModels = array_merge($videoModels, $item);
            }
        }
        return $videoModels;
    }

    public function config($isFore = false)
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        if($this->get === 'video'){ // 只要视频
            return [
                'code' => 0,
                'message' => 'success',
                'data' => [
                    'model_data' => $this->videoAll($isFore)
                ]
            ];
        }

        // 用户端统一页面后使用的数据
        $models = [
            [
                'value' => 'image',
                'label' => \Yii::t('common', '图片生成'),
                'icon' => 'bi-image',
                'list' => [],
            ],
            [
                'value' => 'video',
                'label' => \Yii::t('common', '视频生成'),
                'icon' => 'bi-camera-video',
                'list' => [],
            ],
        ];
        if($this->function_type == VisualVideo::DREAM_NAME){ // 即梦ai
            $models[0]['list'] = $this->dreamImage();
            $models[1]['list'] = $this->videoAll($isFore);
        }
        if($this->function_type == VisualVideo::ARK_NAME){ // 火山方舟
            $models[0]['list'] = $this->arkImage();
            $models[1]['list'] = $this->videoAll($isFore);
        }
        if($this->function_type == VisualVideo::ARK_ABROAD_NAME){ // 火山方舟 - 国际版
            $this->is_home = 2;
            $models[0]['list'] = $this->arkImage();
            $models[1]['list'] = $this->videoAll($isFore);
        }
        return [
            'code' => 0,
            'message' => 'success',
            'data' => [
                'model_data' => $models,
            ]
        ];
    }

    /**
     * @return array[]
     * 即梦的图片生成模型列表
     * @see VisualImage::$type
     */
    public function dreamImage()
    {
        return [
            [
                'label' => \Yii::t('common', '文生图'),
                'value' => CVSync2AsyncSubmitTask::TI_GEN_SERVICE,
                'desc' => '',
                'size_config' => [
                    'quality' => [
                        'sd' => \Yii::t('common', '标清'),
                        'hd' => \Yii::t('common', '高清')
                    ],
                    'presets' => [
                        'sd' => [
                            ['name' => '1:1', 'width' => 1328, 'height' => 1328],
                            ['name' => '4:3', 'width' => 1472, 'height' => 1104],
                            ['name' => '3:2', 'width' => 1584, 'height' => 1056],
                            ['name' => '16:9', 'width' => 1664, 'height' => 936],
                            ['name' => '21:9', 'width' => 2016, 'height' => 864],
                        ],
                        'hd' => [
                            ['name' => '1:1', 'width' => 2048, 'height' => 2048],
                            ['name' => '4:3', 'width' => 2304, 'height' => 1728],
                            ['name' => '3:2', 'width' => 2496, 'height' => 1664],
                            ['name' => '16:9', 'width' => 2560, 'height' => 1440],
                            ['name' => '21:9', 'width' => 3024, 'height' => 1296],
                        ],
                    ],
                    'min_width' => 512,
                    'min_height' => 512,
                ],
                'type' => 1,
                'price_field' => 'image_generate_price' // 价格字段
            ],
            [
                'label' => \Yii::t('common', '图生图'),
                'value' => CVSync2AsyncSubmitTask::IMAGE_G_SERVICE,
                'desc' => '',
                'need_img' => 1,
                'img_config' => [
                    'format'=> 'image/png,image/jpeg',
                    'max_size'=> 4.71, // MB， 最大 4.7 MB。
                    'ratio_range'=> [0.3, 3],
                    'min_side' => 320,
                    'max_side' => 4096,
                ],
                'type' => 3,
                'price_field' => 'img_to_img_generate_price'
            ],
        ];
    }

    /**
     * @return array[]
     * 即梦的视频生成模型列表
     * @see VisualVideo::$type
     */
    public function dreamVideo()
    {
        return [
            [
                'value' => CVSync2AsyncSubmitTask::V_GEN_SERVICE, // 模型名称，需要传给接口使用
                'label' => 'jimeng_v30_pro',
                'modes' => ['text', 'image'],
                'resolutions' => ['1080p'],
                'hide_resolutions' => 1,
                'hide_watermark' => 1,
                'durations' => [
                    ['value' => 5, 'label' => '5' . \Yii::t('common', '秒')],
                    ['value' => 10, 'label' => '10' . \Yii::t('common', '秒')],
                ],
                'resolution_details' => [
                    '1080p' => ['1:1', '3:4', '4:3', '9:16', '16:9', '21:9'],
                ],
                'img_config' => [
                    'format'=> 'image/png,image/jpeg',
                    'max_size'=> 4.71, // MB， 最大 4.7 MB。
                    'ratio_range'=> [0.3, 3],
                    'min_side' => 320,
                    'max_side' => 4096,
                ],
                'type' => 1,
            ],
            [
                'value' => CVSync2AsyncSubmitTask::V_GEN_1_SERVICE,
                'label' => 'jimeng_v30_720',
                'modes' => ['text'],
                'resolutions' => ['720p'],
                'hide_resolutions' => 1, // 1 隐藏分辨率，0 不隐藏
                'hide_watermark' => 1, // 1 隐藏水印，0 不隐藏
                'durations' => [
                    ['value' => 5, 'label' => '5' . \Yii::t('common', '秒')],
                    ['value' => 10, 'label' => '10' . \Yii::t('common', '秒')],
                ],
                'resolution_details' => [
                    '720p' => ['1:1', '3:4', '4:3', '9:16', '16:9', '21:9'],
                ],
                'type' => 1,
            ],
            [
                'value' => CVSync2AsyncSubmitTask::V_GEN_2_SERVICE,
                'label' => 'jimeng_v30_1080',
                'modes' => ['text'],
                'resolutions' => ['1080p'],
                'hide_resolutions' => 1,
                'hide_watermark' => 1,
                'durations' => [
                    ['value' => 5, 'label' => '5' . \Yii::t('common', '秒')],
                    ['value' => 10, 'label' => '10' . \Yii::t('common', '秒')],
                ],
                'resolution_details' => [
                    '1080p' => ['1:1', '3:4', '4:3', '9:16', '16:9', '21:9'],
                ],
                'type' => 1,
            ],
        ];
    }

    /**
     * @return array[]
     * 即梦的图片生成模型列表
     * @see VisualImage::$type
     * @see VisualImage::$is_home
     */
    public function arkImage()
    {
        $itemList = [
            [
                'label' => \Yii::t('common', '文生图'),
                'value' => '',
                'desc' => '',
                'size_config' => [
                    'presets' => [
                        ['name' => '1:1', 'value' => '1024x1024'],
                        ['name' => '3:4', 'value' => '864x1152'],
                        ['name' => '4:3', 'value' => '1152x864'],
                        ['name' => '2:3', 'value' => '832x1248'],
                        ['name' => '3:2', 'value' => '1248x832'],
                        ['name' => '16:9', 'value' => '1280x720'],
                        ['name' => '9:16', 'value' => '720x1280'],
                        ['name' => '21:9', 'value' => '1512x648'],
                    ],
                    'min_width' => 512,
                    'min_height' => 512,
                ],
                'type' => 0,
                'is_home' => 0,
                'price_field' => 'image_generate_price' // 价格字段
            ],
            [
                'label' => \Yii::t('common', '图生图'),
                'value' => '',
                'desc' => '',
                'need_img' => 1,
                'img_config' => [
                    'format' => 'image/png,image/jpeg',
                    'max_size' => 10.01, // MB， 最大 10 MB。
                    'ratio_range' => [0.3, 3],
                    'min_side' => 14,
                    'max_side' => 4096,
                ],
                'type' => 0,
                'is_home' => 0,
                'price_field' => 'img_to_img_generate_price'
            ],
        ];
        if ($this->is_home == 2) { // 国际版
            $itemList[0] = array_merge($itemList[0], [
                'value' => ImageGenerate::GLOBAL_MODEL,
                'type' => 2,
                'is_home' => 2,
            ]);
            $itemList[1] = array_merge($itemList[1], [
                'value' => ImageGenerate::GLOBAL_IMAGE_G_SERVICE,
                'type' => 4,
                'is_home' => 2,
            ]);
        }else {
            $itemList[0] = array_merge($itemList[0], [
                'value' => ImageGenerate::CN_MODEL,
                'type' => 2,
                'is_home' => 1,
            ]);
            $itemList[1] = array_merge($itemList[1], [
                'value' => ImageGenerate::IMAGE_G_SERVICE,
                'type' => 4,
                'is_home' => 1,
            ]);
        }
        return $itemList;
    }

    /**
     * @return array[]
     * 火山方舟的视频生成模型列表
     * @see VisualVideo::$type
     */
    public function arkVideo()
    {
        $list = [
            [
                'value' => $this->is_home == 1 ? 'doubao-seedance-1-0-pro-250528' : 'seedance-1-0-pro-250528',
                'label' => $this->is_home == 1 ? 'Artsdance-Pro' : 'Artsdance-pro-int',
                'modes' => ['text', 'image'],
                'resolutions' => $this->is_home == 1 ? ['480p', '720p', '1080p'] : ['480p', '1080p'],
                'durations' => [
                    ['value' => 5, 'label' => '5' . \Yii::t('common', '秒')],
                    ['value' => 10, 'label' => '10' . \Yii::t('common', '秒')],
                ],
                'camera_fixed' => true,
                'resolution_details' => $this->is_home == 1 ? [
                    '480p' => ['1:1', '3:4', '4:3', '9:16', '16:9', '21:9'],
                    '720p' => ['1:1', '3:4', '4:3', '9:16', '16:9', '21:9'],
                    '1080p' => ['1:1', '3:4', '4:3', '9:16', '16:9', '21:9'],
                ] : [
                    '480p' => ['1:1', '3:4', '4:3', '9:16', '16:9', '21:9'],
                    '1080p' => ['1:1', '3:4', '4:3', '9:16', '16:9', '21:9'],
                ],
            ],

            [
                'value' => $this->is_home == 1 ? 'doubao-seedance-1-0-lite-t2v-250428' : 'seedance-1-0-lite-t2v-250428',
                'label' => $this->is_home == 1 ? 'Artsdance-Lite' : 'Artsdance-Lite-int',
                'modes' => ['text'],
                'resolutions' => ['480p', '720p', '1080p'],
                'durations' => [
                    ['value' => 5, 'label' => '5' . \Yii::t('common', '秒')],
                    ['value' => 10, 'label' => '10' . \Yii::t('common', '秒')],
                ],
                'camera_fixed' => true,
                'resolution_details' => [
                    '480p' => ['1:1', '3:4', '4:3', '9:16', '16:9', '21:9'],
                    '720p' => ['1:1', '3:4', '4:3', '9:16', '16:9', '21:9'],
                    '1080p' => ['1:1', '3:4', '4:3', '9:16', '16:9', '21:9'],
                ]
            ],

            [
                'value' => $this->is_home == 1 ? 'doubao-seedance-1-0-lite-i2v-250428' : 'seedance-1-0-lite-i2v-250428',
                'label' => $this->is_home == 1 ? 'Artsdance-Lite' : 'Artsdance-Lite-int',
                'modes' => ['image'],
                'resolutions' => ['480p', '720p', '1080p'],
                'durations' => [
                    ['value' => 5, 'label' => '5' . \Yii::t('common', '秒')],
                    ['value' => 10, 'label' => '10' . \Yii::t('common', '秒')],
                ],
                'camera_fixed' => true,
                'resolution_details' => [
                    '480p' => ['1:1', '3:4', '4:3', '9:16', '16:9', '21:9'],
                    '720p' => ['1:1', '3:4', '4:3', '9:16', '16:9', '21:9'],
                    '1080p' => ['1:1', '3:4', '4:3', '9:16', '16:9', '21:9'],
                ],
                'is_frame' => 1, // 首尾帧图片，1：首帧或首尾帧
            ],

            [
                'value' => 'wan2-1-14b-t2v-250225',
                'label' => 'Wan2.1-14B',
                'modes' => ['text'],
                'resolutions' => ['480p', '720p'],
                'durations' => [
                    ['value' => 5, 'label' => '5' . \Yii::t('common', '秒')],
                ],
                'camera_fixed' => false,
                'resolution_details' => [
                    '480p' => ['16:9', '9:16'],
                    '720p' => ['16:9', '9:16'],
                ]
            ],

            [
                'value' => 'wan2-1-14b-i2v-250225',
                'label' => 'Wan2.1-14B',
                'modes' => ['image'],
                'resolutions' => ['480p', '720p'],
                'durations' => [
                    ['value' => 5, 'label' => '5' . \Yii::t('common', '秒')],
                ],
                'camera_fixed' => false,
                'resolution_details' => [
                    '480p' => ['16:9', '9:16'],
                    '720p' => ['16:9', '9:16'],
                ],
            ],

            [
                'value' => 'wan2-1-14b-flf2v-250417',
                'label' => 'Wan2.1-14B',
                'modes' => ['image'],
                'resolutions' => ['720p'],
                'durations' => [
                    ['value' => 5, 'label' => '5' . \Yii::t('common', '秒')],
                ],
                'camera_fixed' => false,
                'resolution_details' => [
                    '720p' => ['16:9', '9:16'],
                ],
                'is_frame' => 2, // 首尾帧图片，2：首尾帧必须要
            ],
        ];
        if ($this->is_home == 2) { // 国际版
            $list = ArrayHelper::index($list, 'value');
            unset($list['wan2-1-14b-flf2v-250417'], $list['wan2-1-14b-i2v-250225'],
                $list['wan2-1-14b-t2v-250225']);
        }
        return array_map(function($item) {
            $img_config = [
                'format' => 'image/jpeg,image/png,image/webp,image/gif,image/bmp,image/tiff',
                'max_size' => 30, // MB， 最大 30 MB。
                'ratio_range' => [0.4, 2.5],
                'min_side' => 300,
                'max_side' => 6000,
            ];
            $item['type'] = 2;
            if(in_array('image', $item['modes'])){
                $item['img_config'] = $img_config;
            }
            return $item;
        }, $list);
    }
}
