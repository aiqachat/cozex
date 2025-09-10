<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms;

use app\models\UserIdentity;
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
        'voice_manage',
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
                    [
                        'name' => '基础设置',
                        'route' => 'netb/setting/voice',
                    ],
                    [
                        'name' => '价格设置',
                        'route' => 'netb/setting/price',
                    ],
                ],
            ],
            [
                'name' => '视觉智能',
                'key' => 'visual',
                'icon' => 'statics/img/mall/nav/plugins.png',
                'children' => [
                    [
                        'name' => '基础设置',
                        'route' => 'netb/visual/setting',
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
                    [
                        'name' => '字幕配置',
                        'route' => 'netb/setting/subtitle',
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
                            [
                                'name' => '用户等级',
                                'route' => 'netb/level/index',
                                'action' => [
                                    [
                                        'name' => '编辑',
                                        'route' => 'netb/level/edit',
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
                        'name' => '资金管理',
                        'route' => '',
                        'children' => [
                            [
                                'name' => '充值设置',
                                'route' => 'netb/recharge/config',
                            ],
                            [
                                'name' => '余额明细',
                                'route' => 'netb/user/balance-log',
                            ],
                        ],
                    ],
                    [
                        'name' => '积分管理',
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
                    [
                        'name' => '会员管理',
                        'route' => '',
                        'children' => [
                            [
                                'name' => '会员等级',
                                'route' => 'netb/member/level-index',
                            ],
                            [
                                'name' => '会员权限',
                                'route' => 'netb/member/permission-index',
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
            [
                'name' => '内容审核',
                'icon' => 'statics/img/mall/nav/mall-manage.png',
                'children' => [
                    [
                        'name' => '图片审核',
                        'route' => 'netb/content/image',
                    ],
                    [
                        'name' => '视频审核',
                        'route' => 'netb/content/video',
                    ],
                    [
                        'name' => '内容设置',
                        'route' => 'netb/content/setting',
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
                    [
                        'key' => 'voice_manage',
                        'name' => '音色管理',
                        'route' => 'admin/setting/voice',
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
        // 解释查看根目录《用户菜单创建指南.md》
        $dashboard = [
            [
                'name' => 'dashboard.person',
                'path' => '/dashboard/person',
                'view' => 'dashboard/Person',
                'meta' => [
                    'title' => Yii::t('menu', '个人中心'),
                ],
            ],
            [
                'name' => 'user.edit',
                'path' => '/user/edit',
                'view' => 'user/Edit',
                'meta' => [
                    'hidden' => true,
                ],
            ],
        ];
        if(!Yii::$app->user->isGuest){
            /** @var UserIdentity $userIdentity */
            $userIdentity = Yii::$app->user->identity->identity;
            if($userIdentity->level->promotion_status){
                $dashboard[] = [
                    'name' => 'dashboard.promote',
                    'path' => '/dashboard/promote',
                    'view' => 'dashboard/Promote',
                    'meta' => [
                        'title' => Yii::t('menu', '推广中心'),
                    ],
                ];
            }
        }
        return [
            [
                'name' => 'voice',
                'key' => 'voice',
                'meta' => [
                    'title' => Yii::t('menu', '语音技术'),
                    'icon' => 'bi-mic-fill',
                ],
                'children' => [
                    [
                        'name' => 'voice.list',
                        'meta' => [
                            'title' => Yii::t('menu', '国内版'),
                        ],
                        'children' => [
                            [
                                'name' => 'voice.ttsModel',
                                'path' => '/voice/ttsModel',
                                'view' => 'voice/TtsModel',
                                'meta' => [
                                    'title' => Yii::t('menu', '大模型语音合成'),
                                ],
                            ],
                            [
                                'name' => 'voice.ttsMega',
                                'path' => '/voice/ttsMega',
                                'view' => 'voice/TtsMega',
                                'meta' => [
                                    'title' => Yii::t('menu', '大模型声音复刻'),
                                ],
                            ],
                            [
                                'name' => 'voice.ttsLong',
                                'path' => '/voice/ttsLong',
                                'view' => 'voice/TtsLong',
                                'meta' => [
                                    'title' => Yii::t('menu', '语音合成长文本'),
                                ],
                            ],
                            [
                                'name' => 'voice.tts',
                                'path' => '/voice/tts',
                                'view' => 'voice/Tts',
                                'meta' => [
                                    'title' => Yii::t('menu', '语音合成短文本'),
                                ],
                            ],
                        ]
                    ],
                    [
                        'name' => 'voice.listInter',
                        'meta' => [
                            'title' => Yii::t('menu', '国际版'),
                        ],
                        'children' => [
                            [
                                'name' => 'voice.ttsModelAbroad',
                                'path' => '/voice/ttsModelAbroad',
                                'view' => 'voice/TtsModelAbroad',
                                'meta' => [
                                    'title' => Yii::t('menu', '大模型语音合成'),
                                ],
                            ],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'visual',
                'key' => 'visual',
                'meta' => [
                    'title' => Yii::t('menu', '视觉智能'),
                    'icon' => 'bi-camera-fill',
                ],
                'children' => [
                    [
                        'name' => 'visual.dream',
                        'path' => '/visual/dream',
                        'view' => 'visual/Dream',
                        'meta' => [
                            'title' => Yii::t('menu', '即梦AI'),
                        ],
                    ],
                    [
                        'name' => 'visualArk.ark',
                        'path' => '/visual/ark',
                        'view' => 'visual/Ark',
                        'meta' => [
                            'title' => Yii::t('menu', '火山方舟'),
                        ],
                    ],
                    [
                        'name' => 'visualArk.arkAbroad',
                        'path' => '/visual/arkAbroad',
                        'view' => 'visual/ArkAbroad',
                        'meta' => [
                            'title' => Yii::t('menu', '火山方舟国际版'),
                        ],
                    ],
                    [
                        'name' => 'visual.jimeng',
                        'meta' => [
                            'title' => Yii::t('menu', '即梦AI'),
                        ],
                        'children' => [
                            [
                                'name' => 'visual.image',
                                'path' => '/visual/image',
                                'view' => 'visual/Image',
                                'meta' => [
                                    'title' => Yii::t('menu', '图片生成'),
                                ],
                            ],
                            [
                                'name' => 'visual.video',
                                'path' => '/visual/video',
                                'view' => 'visual/Video',
                                'meta' => [
                                    'title' => Yii::t('menu', '视频生成'),
                                ],
                            ],
                            [
                                'name' => 'visual.editImage',
                                'path' => '/visual/editImage',
                                'view' => 'visual/EditImage',
                                'meta' => [
                                    'title' => Yii::t('menu', '图生图'),
                                ],
                            ],
                        ]
                    ],
                    [
                        'name' => 'visual.ark',
                        'meta' => [
                            'title' => Yii::t('menu', '火山方舟'),
                        ],
                        'children' => [
                            [
                                'name' => 'visualArk.image',
                                'path' => '/visual/arkImage',
                                'view' => 'visual/ArkImage',
                                'meta' => [
                                    'title' => Yii::t('menu', '图片生成'),
                                ],
                            ],
                            [
                                'name' => 'visualArk.video',
                                'path' => '/visual/arkVideo',
                                'view' => 'visual/ArkVideo',
                                'meta' => [
                                    'title' => Yii::t('menu', '视频生成'),
                                ],
                            ],
                            [
                                'name' => 'visualArk.editImage',
                                'path' => '/visual/arkEditImage',
                                'view' => 'visual/ArkEditImage',
                                'meta' => [
                                    'title' => Yii::t('menu', '图生图'),
                                ],
                            ],
                        ]
                    ],
                    [
                        'name' => 'visual.arkInter',
                        'meta' => [
                            'title' => Yii::t('menu', '火山方舟国际版'),
                        ],
                        'children' => [
                            [
                                'name' => 'visualArk.imageAbroad',
                                'path' => '/visual/arkImageAbroad',
                                'view' => 'visual/ArkImageAbroad',
                                'meta' => [
                                    'title' => Yii::t('menu', '图片生成'),
                                ],
                            ],
                            [
                                'name' => 'visualArk.videoAbroad',
                                'path' => '/visual/arkVideoAbroad',
                                'view' => 'visual/ArkVideoAbroad',
                                'meta' => [
                                    'title' => Yii::t('menu', '视频生成'),
                                ],
                            ],
                            [
                                'name' => 'visualArk.editImageAbroad',
                                'path' => '/visual/arkEditImageAbroad',
                                'view' => 'visual/ArkEditImageAbroad',
                                'meta' => [
                                    'title' => Yii::t('menu', '图生图'),
                                ],
                            ],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'subtitles',
                'key' => 'subtitle', // 用于判断权限
                'meta' => [
                    'title' => Yii::t('menu', '字幕技术'),
                    'icon' => 'bi-chat-square-text',
                ],
                'children' => [
                    [
                        'name' => 'subtitles.generate',
                        'path' => '/subtitles/generate',
                        'view' => 'subtitles/Index',
                        'meta' => [
                            'title' => Yii::t('menu', '字幕生成'),
                        ],
                    ],
                    [
                        'name' => 'subtitles.titling',
                        'path' => '/subtitles/titling',
                        'view' => 'subtitles/Titling',
                        'meta' => [
                            'title' => Yii::t('menu', '字幕打轴'),
                        ],
                    ],
                    [
                        'name' => 'subtitles.auc',
                        'path' => '/subtitles/auc',
                        'view' => 'subtitles/Auc',
                        'meta' => [
                            'title' => Yii::t('menu', '大模型语音识别'),
                        ],
                    ],
                ]
            ],
            [
                'name' => 'finance',
                'meta' => [
                    'title' => Yii::t('menu', '财务管理'),
                    'icon' => 'bi-coin',
                ],
                'children' => [
                    [
                        'name' => 'finance.index',
                        'path' => '/finance/index',
                        'view' => 'finance/Index',
                        'meta' => [
                            'title' => Yii::t('menu', '财务信息'),
                        ],
                    ],
                    [
                        'name' => 'finance.integralRecord',
                        'path' => '/finance/integralRecord',
                        'view' => 'finance/IntegralRecord',
                        'meta' => [
                            'hidden' => true,
                        ],
                    ],
                    [
                        'name' => 'finance.balanceRecord',
                        'path' => '/finance/balanceRecord',
                        'view' => 'finance/BalanceRecord',
                        'meta' => [
                            'hidden' => true,
                        ],
                    ],
                    [
                        'name' => 'finance.integral',
                        'path' => '/finance/integral',
                        'view' => 'finance/Integral',
                        'meta' => [
                            'title' => Yii::t('menu', '积分管理'),
                        ],
                    ],
                ]
            ],
            [
                'name' => 'dashboard',
                'meta' => [
                    'title' => Yii::t('menu', '控制面板'),
                    'icon' => 'bi-speedometer2',
                ],
                'children' => $dashboard
            ],
        ];
    }
}
