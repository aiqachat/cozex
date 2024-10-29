<?php
/**
 * @var \yii\web\View $this
 * @var string $content
 */

use app\forms\mall\bot\IndexForm;
use app\forms\mall\setting\ConfigForm;
use app\models\User;

$isAdmin = false;
$isSuperAdmin = false;
if (!Yii::$app->user->isGuest) {
    /** @var User $user */
    $user = Yii::$app->user->identity;
    if ($user->identity && $user->identity->is_super_admin == 1) {
        $isAdmin = true;
        $isSuperAdmin = true;
    }
    if ($user->identity && $user->identity->is_admin == 1) {
        $isAdmin = true;
    }
}
$currentRoute = Yii::$app->controller->route;
$indSetting = (new ConfigForm())->config();
if (!empty($indSetting['name'])) {
    $siteName = $indSetting['name'];
} else {
    $siteName = 'cozex管理';
}
$coze_set = (new IndexForm())->page();
?>
<?php $this->beginPage(); ?>
    <!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="renderer" content="webkit">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
        <meta name="format-detection" content="telephone=no,email=no,address=no">
        <title><?= $siteName ?></title>
        <link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ?>/statics/unpkg/element-ui@2.12.0/lib/theme-chalk/index.css">
        <link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ?>/statics/css/flex.css">
        <link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ?>/statics/css/common.css">
        <link href="//at.alicdn.com/t/font_353057_qq5xo4ymtf.css" rel="stylesheet">
        <link href="//at.alicdn.com/t/font_1861175_6hlb1v8lw9r.css" rel="stylesheet">
        <link href="<?= Yii::$app->request->baseUrl ?>/../favicon.ico" mce_href="<?= Yii::$app->request->baseUrl ?>/../favicon.ico" rel="shortcut icon"/>
        <script src="<?= Yii::$app->request->baseUrl ?>/statics/unpkg/jquery@3.3.1/dist/jquery.min.js"></script>
        <script src="<?= Yii::$app->request->baseUrl ?>/statics/unpkg/vue@2.6.10/dist/vue.js"></script>
        <script src="<?= Yii::$app->request->baseUrl ?>/statics/unpkg/element-ui@2.12.0/lib/index.js"></script>
        <script src="<?= Yii::$app->request->baseUrl ?>/statics/unpkg/qs@6.5.2/dist/qs.js"></script>
        <script src="<?= Yii::$app->request->baseUrl ?>/statics/unpkg/axios@0.18.0/dist/axios.min.js"></script>
        <script src="<?= Yii::$app->request->baseUrl ?>/statics/unpkg/vue-line-clamp@1.2.4/dist/vue-line-clamp.umd.js"></script>
        <script>
            let _layout = null;
            let _aside = null;
            const _csrf = '<?=Yii::$app->request->csrfToken?>';
            const _scriptUrl = '<?=Yii::$app->request->scriptUrl?>';
            const _baseUrl = '<?= \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl ?>';
            const _requestRoute = '<?=Yii::$app->requestedRoute?>';
            let _isAdmin = <?=$isAdmin ? 'true' : 'false'?>;
            let _isSuperAdmin = <?=$isSuperAdmin ? 'true' : 'false'?>;
        </script>
        <script src="<?= Yii::$app->request->baseUrl ?>/statics/js/common.js?v=4.3.2"></script>
        <script src="<?= Yii::$app->request->baseUrl ?>/statics/js/dayjs.min.js"></script>
        <script src="<?= Yii::$app->request->baseUrl ?>/statics/js/echarts.min.js"></script>
        <style>
            /* https://github.com/ElemeFE/element/pull/15359 */
            .el-input .el-input__count .el-input__count-inner {
                background: #FFF;
                display: inline-block;
                padding: 0 5px;
                line-height: normal;
            }

            html, body {
                height: 100%;
                padding: 0;
                margin: 0;
            }

            #app {
                height: 100%;
                min-width: 750px;
                flex: 1;
            }

            .el-header {
                padding: 0;
            }

            .el-container {
                height: calc(100% - 60px);
            }
            .el-content{
                height: 100%;
            }

            [v-cloak] {
                display: none !important;
            }

            input, textarea, select {
                appearance: none;
                outline: none !important;
                box-shadow: none;
            }

            .el-dialog {
                min-width: 600px;
            }

            /*新左侧菜单 start*/
            #_aside {
                position: relative;
            }

            #_aside .el-menu{
                border: none;
            }

            .el-popover{
                max-height: 80vh;
                overflow: auto;
                background-color: #151822;
                border-color: #151822;
                color: white;
            }

            .el-popover .el-menu{
                border: none;
            }

            #_aside .is-show-menu-2 {
                position: absolute;
                border-radius: 0 10px 10px 0;
                cursor: pointer;
                z-index: 10;
                background: #1f2d3d;
                color: white;
                padding: 8px;
                right: -32px;
                top: 32px;
                font-size: 13px;
                writing-mode:tb-rl
            }

            #_aside .menu-item {
                height: 60px;
                padding: 10px 20px;
            }

            #_aside .left-menu {
                width: 60px;
                height: 100%;
                overflow-y: auto;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }

            #_aside .menu-item-box.active {
                width: 100%;
                height: 100%;
                border-radius: 4px;
                background: white;
                cursor: pointer;
                color: #006eff;
            }

            /*一级菜单 start*/

            #_aside .aside-logo {
                width: 60px;
                height: 60px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #1e222d;
                color: #f2f2f2;
                cursor: pointer;
                font-weight: bold;
                text-align: center;
                padding: 0;
            }

            #_aside .aside-logo:hover {
                background: #30353a;
                color: #fff;
            }

            #_aside .aside-logo div {
                background: #262f3e;
                /*padding: 6px 6px;*/
                width: 100%;
                border-radius: 3px;
                /*margin: 10px 0;*/
            }

            #_aside .aside-logo img {
                height: calc(40px + 2px);
                width: calc(40px + 2px);
                border-radius: 50%;
                border: 2px solid #ffffff;
                /*display: block;*/
                /*margin-top: 10px;*/
            }

            #_aside .left-menu-1 {
                background: #1e222d;
                cursor: pointer;
            }

            #_aside .menu-item-1 {
                color: #c1c6c8;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                width: 50px;
                height: 56px;
                margin: 10px auto;
                border-radius: 10px;
                padding: 0 4px;
            }

            #_aside .menu-item-1.active {
                color: #fff;
                background: #006eff;
            }

            #_aside .menu-item-1.hover {
                background: #282f3e;
                cursor: pointer;
            }

            #_aside .menu-item-1 .icon {
                /*margin-right: 10px;*/
                width: 24px;
                height: 24px;
                margin-bottom: 4px;
                color: #fff;
            }

            /*一级菜单 end*/

            /*二级菜单 start*/

            #_aside .left-menu-2 {
                background-color: #1f2d3d;
                color: white;
                padding-top: 50px;
                left: 160px;
                z-index: 1000;
                width: 160px;
                height: 100%;
            }

            #_aside .left-menu-2-show {
                display: none;
            }

            #_aside .left-menu-2 .is-show-menu-1 {
                position: absolute;
                border-radius: 10px 0 0 10px;
                top: 20px;
                cursor: pointer;
                background: #006eff;
                font-size: 12px;
                color: white;
                padding: 5px 8px;
                right: 1px;
            }

            #_aside .left-menu-2 .is-show-menu-1:hover{
                background-color: #006eff55;
                color: white;
            }

            #_aside .menu-item-2 {
                cursor: pointer;
            }
            #_aside .menu-item-2-title {
                padding-left: 28px;
                color: white;
                background-color: #1f2d3d;
            }

            #_aside .menu-item-2:hover {
                color: #006eff;
            }

            #_aside .menu-item-2 .icon-box {
                width: 14px;
                margin-right: 5px;
            }

            /*二级菜单 end*/

            /*三级菜单 start*/
            #_aside .menu-item-3 {
                cursor: pointer;
                margin-left: 5px;
            }

            #_aside .menu-item-3:hover {
                color: #006eff;
            }

            #_aside .menu-item-3 .icon-box {
                width: 14px;
                margin-right: 5px;
            }

        </style>
    </head>
    <body>
    <?php $this->beginBody() ?>
    <div id="_layout"></div>
    <?= $this->renderFile('@app/views/components/index.php') ?>
    <?php Yii::$app->loadViewComponent('mall-header', __DIR__); ?>
    <div style="height: 100%">
        <div id="_header">
            <mall-header></mall-header>
        </div>
        <div class="el-container">
            <div v-cloak id="_aside" flex="dir:left">
                <div @click="isShowMenu = true" v-if="!isShowMenu" class="is-show-menu-2">展开菜单 ></div>
                <!-- 一级菜单 -->
                <div class="left-menu left-menu-1">
                    <div  v-for="leftMenu in leftMenus" :key="leftMenu.id"  @click="menuClick1(leftMenu)"
                          @mouseenter="mouseenterEvent(leftMenu)"
                          @mouseleave="mouseleaveEvent"
                          class="menu-item menu-item-1"
                          :class="{'active': currentMenu.opened_1 == leftMenu.id,
                     'hover':currentMenu.temporary_opened_1 == leftMenu.id && currentMenu.opened_1 != leftMenu.id ? true : false }">
                        <img class="icon" :src="leftMenu.icon">
                        <app-ellipsis :line="1" style="font-size: 18px;zoom: .5;">{{leftMenu.name}}</app-ellipsis>
                    </div>
                </div>

                <!-- 二级菜单 -->
                <div v-if="currentMenu.list && currentMenu.list.children && currentMenu.list.children.length > 0"
                     @mouseenter="mouseenterEvent2()"
                     @mouseleave="mouseleaveEvent"
                     class="left-menu left-menu-2"
                     :class="{'left-menu-2-show': !isShowMenu}">
                    <!-- 展示收起按钮 -->
                    <div @click="isShowMenu = false" v-if="isShowMenu" class="is-show-menu-1"><< 点击收起</div>
                    <div class="menu-item menu-item-2-title" flex="dir:left cross:center">
                        <app-ellipsis :line="1">{{currentMenu.list.name}}</app-ellipsis>
                    </div>
                    <div v-for="menu_1 in currentMenu.list.children" :key="menu_1.id" flex="dir:top">
                        <div @click="menuClick2(menu_1)" class="menu-item menu-item-2" flex="dir:left cross:center">
                            <div class="menu-item-box" :class="{'active': currentMenu.opened_2 == menu_1.id ? true : false}"
                                 flex="dir:left cross:center">
                                <div class="icon-box">
                                    <div v-if="menu_1.children">
                                        <i v-if="currentMenu.unfold_id_1 == menu_1.id" class="el-icon-arrow-down"></i>
                                        <i v-else class="el-icon-arrow-right"></i>
                                    </div>
                                </div>
                                <app-ellipsis :line="1">{{menu_1.name}}</app-ellipsis>
                            </div>
                        </div>

                        <!-- 三级菜单 -->
                        <div v-if="currentMenu.unfold_id_1 == menu_1.id && menu_1.children"
                             v-for="menu_2 in menu_1.children"
                             :key="menu_2.id"
                             flex="dir:top">
                            <div @click="menuClick3(menu_2)"
                                 class="menu-item menu-item-3"
                                 flex="dir:left cross:center">
                                <div class="menu-item-box"
                                     :class="{'active': currentMenu.opened_3 == menu_2.id ? true : false}"
                                     flex="dir:left cross:center">
                                    <div class="icon-box">
                                        <div v-if="menu_2.children">
                                            <i v-if="currentMenu.unfold_id_2 == menu_2.id" class="el-icon-arrow-down"></i>
                                            <i v-else class="el-icon-arrow-right"></i>
                                        </div>
                                    </div>
                                    <app-ellipsis :line="1">{{menu_2.name}}</app-ellipsis>
                                </div>
                            </div>
                            <!-- 四级菜单 -->
                            <!--                        <div v-if="currentMenu.unfold_id_2 == menu_2.id && menu_2.children"-->
                            <!--                             v-for="menuChildren in menu_2.children"-->
                            <!--                             :key="menuChildren.id"-->
                            <!--                             @click="menuClick2(menuChildren)"-->
                            <!--                             :class="{'active': currentMenu.opened_2 == menuChildren.id ? true : false}"-->
                            <!--                             class="menu-item menu-item-4"-->
                            <!--                             flex="cross:center">-->
                            <!--                            <app-ellipsis :line="1">{{menuChildren.name}}</app-ellipsis>-->
                            <!--                        </div>-->
                        </div>
                    </div>
                </div>
            </div>
            <div id="_layout_body" class="el-container el-content is-vertical" >
                <main class="el-main" style="background: #f3f3f3;display:flex;flex-direction: row;">
                    <?= $content ?>
                </main>
            </div>
        </div>
    </div>
    <script>
        _layout = new Vue({
            el: '#_layout',
            created() {
                setInterval(() => {
                    this.$request({
                        params: {
                            r: 'keep-alive/index'
                        }
                    }).then(e => {
                    }).catch(e => {
                    });
                }, 1000 * 60 * 5);
            }
        });
        _aside = new Vue({
            el: '#_aside',
            data() {
                return {
                    check: true,
                    mall: null,
                    leftMenuLoading: false,
                    leftMenus: {},
                    defaultRoute: null,
                    currentMenu: {
                        list: null,
                        opened_1: 0,
                        temporary_opened_1: 0,
                        opened_2: 0,
                        opened_3: 0,
                        unfold_id_1: 0,
                        unfold_id_2: 0
                    },
                    currentRoute: '<?= $currentRoute ?>',
                    isShowMenu: true
                };
            },
            methods: {
                getMenus() {
                    const cacheKey = '_MALL_MENUS';
                    let data = localStorage.getItem(cacheKey);
                    if (data) {
                        try {
                            data = JSON.parse(data);
                        } catch (e) {
                            data = false;
                        }
                    }
                    if (data && data.menus) {
                        this.leftMenus = data.menus;
                    } else {
                        this.leftMenuLoading = true;
                    }
                    this.currentMenu.opened_1 = localStorage.getItem('_OPENED_MENU_1_ID');
                    this.currentMenu.opened_2 = localStorage.getItem('_OPENED_MENU_2_ID');
                    this.currentMenu.opened_3 = localStorage.getItem('_OPENED_MENU_3_ID');
                    this.currentMenu.unfold_id_1 = localStorage.getItem('_UNFOLD_ID_1');
                    this.currentMenu.unfold_id_2 = localStorage.getItem('_UNFOLD_ID_2');
                    this.setMenus();

                    let self = this;
                    this.$request({
                        params: {
                            r: 'mall/menus/index'
                        },
                        method: 'post',
                        data: {
                            route: getQuery('r'),
                            url_params: JSON.stringify(getAllUrlParams())
                        }
                    }).then(e => {
                        localStorage.setItem(cacheKey, JSON.stringify(e.data.data));
                        self.leftMenuLoading = false;
                        self.leftMenus = e.data.data.menus;
                        self.leftMenus.forEach(function (item) {
                            if (item.is_active) {
                                self.currentMenu.opened_1 = item.id;
                                localStorage.setItem('_OPENED_MENU_1_ID', self.currentMenu.opened_1);
                                if (item.children) {
                                    item.children.forEach(function (cItem1) {
                                        if (cItem1.is_active) {
                                            if (cItem1.children) {
                                                self.currentMenu.unfold_id_1 = cItem1.id;
                                                localStorage.setItem('_UNFOLD_ID_1', self.currentMenu.unfold_id_1);
                                                cItem1.children.forEach(function (cItem2) {
                                                    if (cItem2.is_active) {
                                                        if (cItem2.children) {
                                                            self.currentMenu.unfold_id_2 = cItem2.id;
                                                            localStorage.setItem('_UNFOLD_ID_2', self.currentMenu.unfold_id_2);
                                                            cItem2.children.forEach(function (cItem3) {
                                                                if (cItem3.is_active) {
                                                                    self.currentMenu.opened_2 = cItem3.id;
                                                                    localStorage.setItem('_OPENED_MENU_2_ID', cItem3.id);
                                                                }
                                                            })
                                                        } else {
                                                            self.currentMenu.opened_3 = cItem2.id;
                                                            localStorage.setItem('_OPENED_MENU_3_ID', cItem2.id);
                                                            self.currentMenu.opened_2 = cItem2.id;
                                                            localStorage.setItem('_OPENED_MENU_2_ID', cItem2.id);
                                                        }
                                                    }
                                                })
                                            } else {
                                                self.currentMenu.opened_2 = cItem1.id;
                                                localStorage.setItem('_OPENED_MENU_2_ID', cItem1.id);
                                            }
                                        }
                                    })
                                }
                            }
                        });
                        self.setMenus();
                    }).catch(e => {
                        console.log(e);
                    });
                },
                openUrl(menu) {
                    localStorage.setItem('_UNFOLD_ID_1', this.currentMenu.unfold_id_1);
                    localStorage.setItem('_UNFOLD_ID_2', this.currentMenu.unfold_id_2);
                    if (menu) {
                        let args = {
                            r: menu.route
                        };
                        if (menu.params) {
                            for (let i in menu.params) {
                                args[i] = menu.params[i];
                            }
                        }
                        navigateTo(args)

                        // if (menu.route.indexOf('plugin/') != -1 && menu.route != 'mall/plugin/index') {
                        //     navigateTo(args, true)
                        // } else {
                        //     navigateTo(args)
                        // }
                    }
                },
                setMenus() {
                    let self = this;
                    if (!self.currentMenu.opened_2 && !self.currentMenu.unfold_id_1) {
                        self.currentMenu.opened_1 = 0;
                    }

                    if (self.leftMenus && self.leftMenus.length > 0) {
                        self.leftMenus.forEach(function (item) {
                            if (item.id == self.currentMenu.opened_1) {
                                self.currentMenu.list = item;
                            }
                        });
                    }
                },
                // 点击一级菜单
                menuClick1(menu) {
                    this.clearMenuStorage();
                    this.currentMenu.opened_1 = menu.id;
                    this.currentMenu.list = menu;
                    localStorage.setItem('_OPENED_MENU_1_ID', menu.id);
                    if (!menu.children) {
                        this.openUrl(menu);
                    } else {
                        // if (menu.key == 'app-manage' || menu.key == 'multiplatform') {
                        //     return
                        // }
                        if (menu.children[0].children && menu.children[0].children.length > 0) {
                            this.openUrl(menu.children[0].children[0]);
                            localStorage.setItem('_UNFOLD_ID_1', menu.children[0].id);
                            localStorage.setItem('_OPENED_MENU_3_ID', menu.children[0].children[0].id);
                        } else {
                            this.openUrl(menu.children[0]);
                            localStorage.setItem('_OPENED_MENU_2_ID', menu.children[0].id);
                        }
                    }
                },
                // 点击二级菜单
                menuClick2(menu) {
                    if (menu.children) {
                        let unfoldId1 = null;
                        if (this.currentMenu.unfold_id_1 == menu.id) {
                            unfoldId1 = 0;
                        } else {
                            unfoldId1 = menu.id;
                        }
                        this.currentMenu.unfold_id_1 = unfoldId1;
                    } else {
                        this.currentMenu.opened_2 = menu.id;
                        let temporary = this.currentMenu.temporary_opened_1;
                        if (temporary) {
                            localStorage.setItem('_OPENED_MENU_1_ID', temporary);
                        }
                        localStorage.setItem('_OPENED_MENU_2_ID', menu.id);
                        localStorage.setItem('_OPENED_MENU_3_ID', 0);
                        this.openUrl(menu);
                    }
                },
                // 点击三级菜单
                menuClick3(menu) {
                    if (menu.children) {
                        let unfoldId2 = null;
                        if (this.currentMenu.unfold_id_2 == menu.id) {
                            unfoldId2 = 0;
                        } else {
                            unfoldId2 = menu.id;
                        }
                        this.currentMenu.unfold_id_2 = unfoldId2;
                    } else {
                        this.currentMenu.opened_3 = menu.id;
                        let temporary = this.currentMenu.temporary_opened_1;
                        if (temporary) {
                            localStorage.setItem('_OPENED_MENU_1_ID', temporary);
                        }
                        localStorage.setItem('_OPENED_MENU_2_ID', 0);
                        localStorage.setItem('_OPENED_MENU_3_ID', menu.id);
                        this.openUrl(menu);
                    }
                },
                clearMenuStorage() {
                    localStorage.removeItem('_OPENED_MENU_1_ID');
                    localStorage.removeItem('_OPENED_MENU_2_ID');
                    localStorage.removeItem('_OPENED_MENU_3_ID');
                    localStorage.removeItem('_UNFOLD_ID_1');
                    localStorage.removeItem('_UNFOLD_ID_2');
                },
                mouseenterEvent(menu) {
                    this.ishoverMenu = true;
                    console.log(menu.id)
                    this.currentMenu.temporary_opened_1 = menu.id;
                    if(this.currentMenu.temporary_opened_1 == menu.id) {
                        this.currentMenu.list = menu;
                    }
                },
                mouseenterEvent2() {
                    this.ishoverMenu = true;
                    let self = this;
                    if (self.currentMenu.temporary_opened_1 > 0) {
                        self.leftMenus.forEach(function (item) {
                            if (self.currentMenu.temporary_opened_1 === item.id) {
                                self.currentMenu.list = item;
                            }
                        })
                    }
                },
                mouseleaveEvent() {
                    this.ishoverMenu = false;
                    setTimeout(()=>{
                        if(!this.ishoverMenu){
                            let self = this;
                            self.currentMenu.temporary_opened_1 = 0;
                            self.leftMenus.forEach(function (item) {
                                if (item.id === self.currentMenu.opened_1) {
                                    self.currentMenu.list = item;
                                }
                            });
                            self.currentMenu.unfold_id_1 = localStorage.getItem('_UNFOLD_ID_1');
                            self.currentMenu.unfold_id_2 = localStorage.getItem('_UNFOLD_ID_2');
                            if (this.currentMenu.opened_1 <= 0) {
                                this.currentMenu.list = {};
                            }
                        }
                    }, 200);
                }
            },
            mounted: function () {
                this.getMenus();
            }
        });
        new Vue({el: '#_header'});
    </script>

    <?php if (!empty($coze_set['bot_id'])) : ?>
        <script src="https://lf-cdn.coze.cn/obj/unpkg/flow-platform/chat-app-sdk/<?= $coze_set['version'] ?>/libs/cn/index.js"></script>
        <script>
            new CozeWebSDK.WebChatClient({
                config: {
                    bot_id: '<?= $coze_set['bot_id'] ?>',
                },
                componentProps: {
                    title: '<?= $coze_set['title'] ?>',
                    icon: '<?= $coze_set['icon'] ?>',
                    lang: '<?= $coze_set['lang'] ?>',
                    layout: '<?= $coze_set['layout'] ?>',
                    width:  <?php if(!$coze_set['width']):?> null <?php else:?><?=$coze_set['width']?><?php endif;?>,
                },
            });
        </script>
    <?php endif; ?>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>