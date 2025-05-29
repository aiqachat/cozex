<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms;

use Yii;

class Menus
{
    /**
     * 只允许 超级管理员 访问的商城路由KEY
     */
    const MALL_SUPER_ADMIN_KEY = [
        'upload_admin',
        'add_account',
        'register_audit',
        'system_setting',
        'account_list',
        'overrun',
        'queue_service',
        'message_remind',
    ];

    /**
     * cozex主菜单
     * @return array
     */
    public static function getMallMenus()
    {
        return [
            [
                'name' => '语音技术',
                'key' => 'voice',
                'icon' => 'statics/img/mall/nav/plugins.png',
                'children' => [
                    [
                        'name' => '大模型语音合成',
                        'route' => 'netb/volcengine/tts-model',
                        'action' => [
                            [
                                'name' => '更多',
                                'route' => 'netb/volcengine/two',
                            ],
                        ],
                    ],
                    [
                        'name' => '大模型声音复刻',
                        'route' => 'netb/volcengine/tts-mega',
                        'action' => [
                            [
                                'name' => '更多',
                                'route' => 'netb/volcengine/record',
                            ],
                        ],
                    ],
                    [
                        'name' => '语音合成短文本',
                        'route' => 'netb/volcengine/tts',
                        'action' => [
                            [
                                'name' => '更多',
                                'route' => 'netb/volcengine/three',
                            ],
                        ],
                    ],
                    [
                        'name' => '语音合成长文本',
                        'route' => 'netb/volcengine/tts-long-text',
                        'action' => [
                            [
                                'name' => '更多',
                                'route' => 'netb/volcengine/one',
                            ],
                        ],
                    ],
                    [
                        'name' => '语音技术配置',
                        'route' => 'netb/setting/volcengine',
                    ],
                ],
            ],
            [
                'name' => '字幕技术',
                'key' => 'subtitle',
                'icon' => 'statics/img/mall/nav/plugins.png',
                'children' => [
                    [
                        'name' => '字幕生成',
                        'route' => 'netb/volcengine/vc',
                        'action' => [
                            [
                                'name' => '更多',
                                'route' => 'netb/volcengine/generate',
                            ],
                        ],
                    ],
                    [
                        'name' => '字幕打轴',
                        'route' => 'netb/volcengine/titling',
                    ],
                    [
                        'name' => '大模型语音识别',
                        'route' => 'netb/volcengine/auc-model',
                        'action' => [
                            [
                                'name' => '语音识别',
                                'route' => 'netb/volcengine/auc',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'coze资源',
                'key' => 'coze',
                'icon' => 'statics/img/mall/nav/coze.png',
                'children' => [
                    [
                        'name' => '资源库',
                        'route' => 'netb/knowledge/index',
                        'action' => [
                            [
                                'name' => '文件列表',
                                'route' => 'netb/knowledge/file-list',
                            ],
                            [
                                'name' => '添加文件',
                                'route' => 'netb/knowledge/add-file',
                            ],
                            [
                                'name' => '添加文件',
                                'route' => 'netb/knowledge/add-local',
                            ],
                        ],
                    ],
                    [
                        'name' => '智能体',
                        'route' => 'netb/bot/index',
                        'action' => [
                            [
                                'name' => '配置发布',
                                'route' => 'netb/bot/set',
                            ],
                        ],
                    ],
                    [
                        'name' => 'coze扣子配置',
                        'route' => 'netb/setting/coze',
                    ],
                ],
            ],
            [
                'name' => '用户管理',
                'key' => 'user',
                'route' => 'netb/user/index',
                'icon' => 'statics/img/mall/nav/user.png',
                'children' => [
                    [
                        'name' => '用户管理',
                        'route' => 'netb/user/index',
                        'children' => [
                            [
                                'name' => '用户列表',
                                'route' => 'netb/user/index',
                                'action' => [
                                    [
                                        'name' => '用户积分充值',
                                        'route' => 'netb/user/rechange',
                                    ],
                                    [
                                        'name' => '用户金额充值',
                                        'route' => 'netb/user/recharge-money',
                                    ],
                                    [
                                        'name' => '用户编辑',
                                        'route' => 'netb/user/edit',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => '用户端管理',
                        'route' => 'netb/user/index',
                        'children' => [
                            [
                                'name' => '基础设置',
                                'route' => 'netb/setting/user',
                            ],
                            [
                                'name' => '菜单设置',
                                'route' => 'netb/user/menu',
                            ],
                            [
                                'name' => '价格设置',
                                'route' => 'netb/setting/price',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => '营销中心',
                'route' => 'netb/plugin/index',
                'icon' => 'statics/img/mall/nav/plugins.png',
                'children' => [
                    [
                        'name' => '余额',
                        'route' => '',
                        'children' => [
                            [
                                'name' => '充值设置',
                                'route' => 'netb/recharge/config',
                            ],
//                            [
//                                'name' => '充值管理',
//                                'route' => 'netb/recharge/index',
//                                'action' => [
//                                    [
//                                        'name' => '充值编辑',
//                                        'route' => 'netb/recharge/edit',
//                                    ],
//                                ],
//                            ],
                            [
                                'name' => '余额收支',
                                'route' => 'netb/user/balance-log',
                            ],
                        ],
                    ],
                    [
                        'name' => '积分',
                        'route' => '',
                        'children' => [
                            [
                                'name' => '积分设置',
                                'route' => 'netb/integral/setting',
                            ],
                            [
                                'name' => '积分兑换',
                                'route' => 'netb/integral/exchange',
                            ],
                            [
                                'name' => '积分记录',
                                'route' => 'netb/integral/log',
                            ],
                            [
                                'name' => '积分收支',
                                'route' => 'netb/statistic/integral',
                            ],
                        ],
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
                        'route' => 'netb/setting/index',
                    ],
                    [
                        'name' => '火山引擎密钥',
                        'key' => 'voice',
                        'route' => 'netb/index/volcengine',
                    ],
                    [
                        'name' => '短信通知',
                        'route' => 'netb/index/sms',
                    ],
                    [
                        'name' => '邮件通知',
                        'route' => 'netb/index/mail',
                    ],
                    [
                        'name' => '支付设置',
                        'route' => 'netb/setting/pay',
                    ],
                    [
                        'name' => '授权登录',
                        'route' => 'netb/setting/oauth',
                    ],
                ],
                'action' => [
                    [
                        'name' => '清除缓存',
                        'route' => 'netb/cache/clean',
                    ],
                ],
            ],
            [
                'name' => '系统信息',
                'icon' => 'statics/img/mall/nav/statics.png',
                'children' => [
                    [
                        'name' => '系统概况',
                        'route' => 'netb/statistic/index',
                    ],
                    [
                        'key' => 'attachment',
                        'name' => '上传管理',
                        'route' => 'netb/attachment/attachment',
                    ],
                    [
                        'name' => '素材管理',
                        'route' => 'netb/material/index',
                    ],
                ],
            ],
        ];
    }

    /**
     * 独立版
     * @return array
     */
    public static function getAdminMenus()
    {
        return [
            [
                'name' => '账户管理',
                'route' => '',
                'icon' => 'bi-person-fill-gear',
                'children' => [
                    [
                        'name' => '我的账户',
                        'route' => 'admin/user/me',
                    ],
                    [
                        'key' => 'account_list', // 超级管理员 显示
                        'name' => '账户列表',
                        'route' => 'admin/user/index',
                    ],
                    [
                        'key' => 'add_account', // 超级管理员 显示
                        'name' => '新增子账户',
                        'route' => 'admin/user/edit',
                    ],
                    [
                        'key' => 'register_audit', // 超级管理员 显示
                        'name' => '注册审核',
                        'route' => 'admin/user/register',
                    ],
                    [
                        'key' => 'message_remind',
                        'name' => '子账户到期提醒设置',
                        'route' => 'admin/setting/message-remind',
                        'params' => [
                            '_layout' => 'admin',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'cozex',
                'route' => '',
                'icon' => 'bi-grid-fill',
                'children' => [
                    [
                        'key' => 'small_procedure',
                        'name' => 'cozex',
                        'route' => 'admin/mall/index',
                        'params' => [
                            '_layout' => 'admin',
                        ],
                        'action' => [
                            [
                                'name' => '添加编辑',
                                'route' => 'admin/app/edit',
                            ],
                            [
                                'name' => '进入后台',
                                'route' => 'admin/app/entry',
                            ],
                            [
                                'name' => '删除商城',
                                'route' => 'admin/app/delete',
                            ],
                            [
                                'name' => '回收站',
                                'route' => 'admin/app/recycle',
                            ],
                            [
                                'name' => '设置回收站',
                                'route' => 'admin/app/set-recycle',
                            ],
                            [
                                'name' => '禁用',
                                'route' => 'admin/app/disabled',
                            ],
                        ],
                    ],
                    [
                        'name' => '回收站',
                        'route' => 'admin/app/recycle',
                    ],
                ],
            ],
            [
                'name' => '设置',
                'route' => '',
                'icon' => 'bi-gear-fill',
                'children' => [
                    [
                        'key' => 'system_setting', // 超级管理员 显示
                        'name' => '系统设置',
                        'route' => 'admin/setting/index',
                    ],
                    [
                        'key' => 'attachment',
                        'name' => '账户上传管理',
                        'route' => 'admin/setting/attachment',
                        'params' => [
                            '_layout' => 'admin',
                        ],
                    ],
                    [
                        'key' => 'overrun',
                        'name' => '超限设置',
                        'route' => 'admin/setting/overrun',
                        'params' => [
                            '_layout' => 'admin',
                        ],
                    ],
                    [
                        'key' => 'queue_service',
                        'name' => '队列服务',
                        'route' => 'admin/setting/queue-service',
                        'params' => [
                            '_layout' => 'admin',
                        ],
                    ],
                ],
            ],
            [
                'key' => 'upload_admin', // 超级管理员 显示
                'name' => '更新',
                'route' => 'admin/update/index',
                'icon' => 'bi-cloud-arrow-up-fill',
                'params' => [
                    '_layout' => 'admin',
                ],
            ],
            [
                'name' => '清理缓存',
                'route' => 'admin/cache/clean',
                'icon' => 'bi-trash2-fill',
                'params' => [
                    '_layout' => 'admin',
                ],
            ],
            [
                'name' => '基本信息',
                'route' => 'admin/index/info',
                'icon' => 'bi-info-circle-fill',
                'params' => [
                    '_layout' => 'admin',
                ],
            ],
        ];
    }

    /**
     * 用户端菜单
     * @return array
     */
    public static function getUserMenus()
    {
        return [
            [
                'name' => 'voice', // 路由名称
                'key' => 'voice', // 用于判断权限
                'meta' => [
                    'title' => Yii::t('menu', 'voice'), // 菜单名
                    'icon' => 'bi-1-circle-fill',
                ],
                'children' => [
                    [
                        'name' => 'voice.ttsModel', // 路由名称
                        'path' => '/voice/ttsModel', // url地址，再#/后面
                        'view' => 'voice/TtsModel', // 页面目录views下目录名+文件名
                        'meta' => [
                            'title' => Yii::t('menu', 'voice.ttsModel'), // 菜单名
                            'icon' => 'bi-house-fill',
                        ],
                    ],
                    [
                        'name' => 'voice.ttsMega', // 路由名称
                        'path' => '/voice/ttsMega', // url地址，再#/后面
                        'view' => 'voice/TtsMega', // 页面目录views下目录名+文件名
                        'meta' => [
                            'title' => Yii::t('menu', 'voice.ttsMega'), // 菜单名
                            'icon' => 'bi-house-fill',
                        ],
                    ],
                    [
                        'name' => 'voice.ttsLong', // 路由名称
                        'path' => '/voice/ttsLong', // url地址，再#/后面
                        'view' => 'voice/TtsLong', // 页面目录views下目录名+文件名
                        'meta' => [
                            'title' => Yii::t('menu', 'voice.ttsLong'), // 菜单名
                            'icon' => 'bi-house-fill',
                        ],
                    ],
                    [
                        'name' => 'voice.tts', // 路由名称
                        'path' => '/voice/tts', // url地址，再#/后面
                        'view' => 'voice/Tts', // 页面目录views下目录名+文件名
                        'meta' => [
                            'title' => Yii::t('menu', 'voice.tts'), // 菜单名
                            'icon' => 'bi-house-fill',
                        ],
                    ],
                ]
            ],
            [
                'name' => 'subtitles', // 路由名称
                'key' => 'subtitle', // 用于判断权限
                'meta' => [
                    'title' => Yii::t('menu', 'subtitles'), // 菜单名
                    'icon' => 'bi-2-circle-fill',
                ],
                'children' => [
                    [
                        'name' => 'subtitles.generate', // 路由名称
                        'path' => '/subtitles/generate', // url地址，再#/后面
                        'view' => 'subtitles/Index', // 页面目录views下目录名+文件名
                        'meta' => [
                            'title' => Yii::t('menu', 'subtitles.generate'), // 菜单名
                            'icon' => 'bi-house-fill',
                        ],
                    ],
                    [
                        'name' => 'subtitles.titling', // 路由名称
                        'path' => '/subtitles/titling', // url地址，再#/后面
                        'view' => 'subtitles/Titling', // 页面目录views下目录名+文件名
                        'meta' => [
                            'title' => Yii::t('menu', 'subtitles.titling'), // 菜单名
                            'icon' => 'bi-house-fill',
                        ],
                    ],
                    [
                        'name' => 'subtitles.auc', // 路由名称
                        'path' => '/subtitles/auc', // url地址，再#/后面
                        'view' => 'subtitles/Auc', // 页面目录views下目录名+文件名
                        'meta' => [
                            'title' => Yii::t('menu', 'subtitles.auc'), // 菜单名
                            'icon' => 'bi-house-fill',
                        ],
                    ],
                ]
            ],
            [
                'name' => 'finance', // 路由名称
                'meta' => [
                    'title' => Yii::t('menu', 'finance'), // 菜单名
                    'icon' => 'bi-coin',
                ],
                'children' => [
                    [
                        'name' => 'finance.index', // 路由名称
                        'path' => '/finance/index', // url地址，再#/后面
                        'view' => 'finance/Index', // 页面目录views下目录名+文件名
                        'meta' => [
                            'title' => Yii::t('menu', 'finance.index'), // 菜单名
                        ],
                    ],
                    [
                        'name' => 'finance.integral',
                        'path' => '/finance/integral',
                        'view' => 'finance/Integral',
                        'meta' => [
                            'title' => Yii::t('menu', 'finance.integral'),
                        ],
                    ],
                ]
            ],
            [
                'name' => 'dashboard', // 路由名称
                'meta' => [
                    'title' => Yii::t('menu', 'dashboard'), // 菜单名
                    'icon' => 'bi-house-fill',
                ],
                'children' => [
                    [
                        'name' => 'dashboard.person', // 路由名称
                        'path' => '/dashboard/person', // url地址，再#/后面
                        'view' => 'dashboard/Person', // 页面目录views下目录名+文件名
                        'meta' => [
                            'title' => Yii::t('menu', 'dashboard.person'), // 菜单名
                        ],
                    ],
                    [
                        'name' => 'user.edit', // 路由名称
                        'path' => '/user/edit', // url地址，再#/后面
                        'view' => 'user/Edit', // 页面目录views下目录名+文件名
                        'meta' => [
                            'hidden' => true,
                        ],
                    ]
                ]
            ],
        ];
    }
}
