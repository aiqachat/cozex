<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms;

class Menus
{
    /**
     * cozex主菜单
     * @return array
     */
    public static function getMallMenus()
    {
        return [
            [
                'name' => 'coze资源',
                'icon' => 'statics/img/mall/nav/coze.png',
                'children' => [
                    [
                        'name' => '资源库',
                        'route' => 'mall/knowledge/index',
                        'action' => [
                            [
                                'name' => '文件列表',
                                'route' => 'mall/knowledge/file-list',
                            ],
                            [
                                'name' => '添加文件',
                                'route' => 'mall/knowledge/add-file',
                            ],
                            [
                                'name' => '添加文件',
                                'route' => 'mall/knowledge/add-local',
                            ],
                        ],
                    ],
                    [
                        'name' => '智能体',
                        'route' => 'mall/bot/index',
                        'action' => [
                            [
                                'name' => '配置发布',
                                'route' => 'mall/bot/set',
                            ],
                        ],
                    ],
                    [
                        'name' => 'coze扣子配置',
                        'route' => 'mall/setting/coze',
                    ],
                ],
            ],
            [
                'name' => '语音技术',
                'icon' => 'statics/img/mall/nav/plugins.png',
                'children' => [
                    [
                        'name' => '字幕生成',
                        'route' => 'mall/volcengine/vc',
                        'action' => [
                            [
                                'name' => '更多',
                                'route' => 'mall/volcengine/generate',
                            ],
                        ],
                    ],
                    [
                        'name' => '字幕打轴',
                        'route' => 'mall/volcengine/titling',
                    ],
                    [
                        'name' => '大模型录音识别',
                        'route' => 'mall/volcengine/auc-model',
                        'action' => [
                            [
                                'name' => '语音识别',
                                'route' => 'mall/volcengine/auc',
                            ],
                        ],
                    ],
                    [
                        'name' => '语音合成TTS',
                        'route' => 'mall/volcengine/tts',
                        'action' => [
                            [
                                'name' => '更多',
                                'route' => 'mall/volcengine/three',
                            ],
                        ],
                    ],
                    [
                        'name' => '语音合成TTS长文本',
                        'route' => 'mall/volcengine/tts-long-text',
                        'action' => [
                            [
                                'name' => '更多',
                                'route' => 'mall/volcengine/one',
                            ],
                        ],
                    ],
                    [
                        'name' => '大模型语音合成',
                        'route' => 'mall/volcengine/tts-model',
                        'action' => [
                            [
                                'name' => '更多',
                                'route' => 'mall/volcengine/two',
                            ],
                        ],
                    ],
                    [
                        'name' => '大模型声音复刻',
                        'route' => 'mall/volcengine/tts-mega',
                        'action' => [
                            [
                                'name' => '更多',
                                'route' => 'mall/volcengine/record',
                            ],
                        ],
                    ],
                    [
                        'name' => '语音技术配置',
                        'route' => 'mall/setting/volcengine',
                    ],
                ],
            ],
            [
                'name' => '设置',
                'route' => '',
                'icon' => 'statics/img/mall/nav/setting.png',
                'children' => [
                    [
                        'name' => '基础设置',
                        'route' => 'mall/index/index',
                    ],
                    [
                        'name' => '火山引擎密钥',
                        'route' => 'mall/index/volcengine',
                    ],
                ],
                'action' => [
                    [
                        'name' => '清除缓存',
                        'route' => 'mall/cache/clean',
                    ],
                ],
            ],
            [
                'name' => '系统信息',
                'icon' => 'statics/img/mall/nav/statics.png',
                'children' => [
                    [
                        'name' => '系统概况',
                        'route' => 'mall/statistic/index',
                    ],
                    [
                        'name' => '队列服务',
                        'route' => 'mall/index/queue',
                    ],
                    [
                        'name' => '上传管理',
                        'route' => 'mall/attachment/attachment',
                    ],
                    [
                        'name' => '素材管理',
                        'route' => 'mall/material/index',
                    ],
                ],
            ],
        ];
    }
}
