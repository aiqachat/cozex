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
                ],
            ],
            [
                'name' => '火山引擎',
                'icon' => 'statics/img/mall/nav/plugins.png',
                'children' => [
                    [
                        'name' => '字幕生成',
                        'route' => 'mall/volcengine/generate',
                    ],
                    [
                        'name' => '字幕打轴',
                        'route' => 'mall/volcengine/titling',
                    ],
                    [
                        'name' => '语音识别',
                        'route' => 'mall/volcengine/auc',
                    ],
                ],
            ],
            [
                'name' => '设置',
                'route' => '',
                'icon' => 'statics/img/mall/nav/setting.png',
                'children' => [
                    [
                        'name' => 'coze授权',
                        'route' => 'mall/setting/coze',
                    ],
                    [
                        'name' => '火山引擎授权',
                        'route' => 'mall/setting/volcengine',
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
                        'name' => '基础设置',
                        'route' => 'mall/index/index',
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
