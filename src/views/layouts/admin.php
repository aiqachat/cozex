<?php
/**
 * @var \yii\web\View $this
 * @var string $content
 */

use app\forms\admin\ConfigForm;
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
$indSetting = (new ConfigForm())->config();
$siteName = $indSetting['name'];
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="keywords" content="<?= $indSetting['keywords'] ?? '' ?>"/>
    <meta name="description" content="<?= $indSetting['description'] ?? '' ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
    <title><?= $this->title ? ($this->title . ' - ') : '' ?><?= $siteName ?></title>
    <link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ?>/statics/unpkg/element-ui@2.12.0/lib/theme-chalk/index.css">
    <link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ?>/statics/css/flex.css">
    <link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ?>/statics/css/common.css">
    <link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ?>/statics/svg/bootstrap-icons.min.css">
    <link href="<?= Yii::$app->request->baseUrl ?>/../favicon.ico"
          mce_href="<?= Yii::$app->request->baseUrl ?>/../favicon.ico" rel="shortcut icon"/>
    <script src="<?= Yii::$app->request->baseUrl ?>/statics/unpkg/jquery@3.3.1/dist/jquery.min.js"></script>
    <script src="<?= Yii::$app->request->baseUrl ?>/statics/unpkg/vue@2.6.10/dist/vue.js"></script>
    <script src="<?= Yii::$app->request->baseUrl ?>/statics/unpkg/element-ui@2.12.0/lib/index.js"></script>
    <script src="<?= Yii::$app->request->baseUrl ?>/statics/unpkg/qs@6.5.2/dist/qs.js"></script>
    <script src="<?= Yii::$app->request->baseUrl ?>/statics/unpkg/axios@0.18.0/dist/axios.min.js"></script>
    <script src="<?= Yii::$app->request->baseUrl ?>/statics/unpkg/vue-line-clamp@1.2.4/dist/vue-line-clamp.umd.js"></script>
    <script>
        let _layout = null;
        const _csrf = '<?=Yii::$app->request->csrfToken?>';
        const _scriptUrl = '<?=Yii::$app->request->scriptUrl?>';
        const _baseUrl = '<?= \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl ?>';
        let _isAdmin = <?=$isAdmin ? 'true' : 'false'?>;
        let _isSuperAdmin = <?=$isSuperAdmin ? 'true' : 'false'?>;
    </script>
    <?php if (!empty($indSetting['logo'])) : ?>
        <script>let _siteLogo = '<?=$indSetting['logo']?>';</script>
    <?php else : ?>
        <script>let _siteLogo = _baseUrl + '/statics/img/admin/login-logo.png';</script>
    <?php endif; ?>
    <?php if (!empty($indSetting['manage_bg'])) : ?>
        <script>let _siteNav = '<?=$indSetting['manage_bg']?>';</script>
    <?php else : ?>
        <script>let _siteNav = _baseUrl + '/statics/img/admin/d.png';</script>
    <?php endif; ?>
    <script src="<?= Yii::$app->request->baseUrl ?>/statics/js/common.js"></script>
    <style>
        html, body {
            height: 100%;
            padding: 0;
            margin: 0;
            background: #f3f3f3 !important;
        }

        .main-container {
            max-width: 1060px;
            margin: 0 auto;
        }

        .main-container.menu {
            position: absolute;
            bottom: 1px;
            left: 50%;
            margin-left: -470px;
        }

        #app {
            height: 100%;
        }

        #_header .el-header {
            margin-bottom: 20px;
            padding-top: 15px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        #_header .el-tabs__header {
            margin-bottom: 0;
        }

        #_header .el-tabs__content {
            display: none !important;
        }

        #_header .el-tabs__nav-wrap::after {
            background: transparent;
        }

        #_header,
        #_header .el-dropdown,
        #_header .el-tabs__item {
            color: #fff;
        }

        #_header .el-tabs__item:hover,
        #_header .el-tabs__item.is-active {
            color: #409EFF;
        }

        #_header .sub-menu {
            border-width: 0;
        }

        #_header .sub-menu.is-active {
            background-color: #ecf5ff;
        }

        #_header .sub-menu + .sub-menu {
            margin-left: 20px;
        }

        .el-footer {
            color: #ACACAC;
            text-align: center;
            line-height: 60px;
        }

        .el-footer a,
        .el-footer a:visited {
            color: #909399;
            text-decoration: none;
        }

        .left-menu {
            height: 100%;
        }

        #_aside .el-submenu .el-menu-item {
            min-width: 0;
        }

        .logo {
            height: 30px;
        }

        [v-cloak] {
            display: none !important;
        }

        .expired-box .el-dialog__body {
            border-top: 1px solid #DCDFE6;
            padding: 30px 0;
            margin: 0 20px;
        }

        .expired-box .message-title {
            margin: 30px 20px;
        }
        .expired-box .message-hint {
            margin: 30px 20px;
            border-radius: 5px;
            padding: 10px 20px;
            color: #ff4544;
            background-color: #ffefef;
            font-size: 14px;
        }
    </style>
</head>
<body>
<?php $this->beginBody() ?>
<div id="_layout"></div>
<?= $this->renderFile('@app/views/components/index.php') ?>
<el-container>
    <div id="_header">
        <header class="el-header" :style="{'background-image':'url('+nav+')','height':'200px'}">
            <div class="main-container" flex="box:last cross:center">
                <div><img class="logo" :src="logo" alt=""></div>
                <div>
                    <el-dropdown @command="handleCommand">
                        <span class="el-dropdown-link">
                            <span style="font-size: 16px;"><i class="el-icon-user-solid" style="margin-right: 6px"></i>{{user.nickname}}</span>
                            <i class="el-icon-arrow-down el-icon--right"></i></span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item
                                    v-if="user.identity.is_super_admin == 1 || user.identity.is_admin == 1"
                                    command="updatePassword"
                                    style="text-align: center">
                                修改密码
                            </el-dropdown-item>
                            <el-dropdown-item command="logout" style="text-align: center">注销</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </div>
            </div>
            <div class="main-container" style="font-size: 20px;margin-top: 35px;padding-left: 10px;">管理中心</div>
            <div class="main-container menu">
                <el-tabs v-model="activeMenuId" @tab-click="handleClick">
                    <template v-for="(menu, index) in menus">
                        <el-tab-pane :name="menu.id">
                            <template slot="label">
                                <i v-if="menu.icon" :class="'bi ' + menu.icon"></i>
                                <span>{{menu.name}}</span>
                            </template>
                        </el-tab-pane>
                    </template>
                </el-tabs>
            </div>
        </header>
        <div v-if="subMenus && subMenus.length" class="main-container"
             style="background: #fff;margin-bottom: 20px;padding: 15px 20px; border-radius: 5px">
            <template v-for="(menu, index) in subMenus">
                <el-button @click="openUrl(menu)" v-if="menu.active" class="sub-menu is-active" size="small"
                           round>{{menu.name}}
                </el-button>
                <el-button @click="openUrl(menu)" v-else class="sub-menu" size="small" round>{{menu.name}}
                </el-button>
            </template>
        </div>

        <!--    超级管理员 修复密码-->
        <el-dialog v-cloak title="修改密码" :close-on-click-modal="false" :visible.sync="dialogFormVisible" width="35%">
            <el-form :model="ruleForm" status-icon :rules="rules" ref="ruleForm">
                <el-form-item label="密码" prop="pass">
                    <el-input type="password" v-model="ruleForm.pass" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item label="确认密码" prop="checkPass">
                    <el-input type="password" v-model="ruleForm.checkPass" autocomplete="off"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer">
                <el-button @click="dialogFormVisible = false">取消</el-button>
                <el-button :loading="btnLoading" type="primary" @click="updatePasswordSubmit('ruleForm')">确定
                </el-button>
            </div>
        </el-dialog>

        <div class="expired-box" v-cloak>
            <el-dialog
                    title="即将到期提醒"
                    :visible.sync="dialogVisibleExpired"
                    width="50%"
                    :before-close="handleCloseExpired">
                <div>
                    <div class="message-title" v-html="expiredData.message_text"></div>
                    <div class="message-hint">
                        <div>您的账户将在 {{expiredData.expired_day}} 天后到期</div>
                        <div>到期时间{{expiredData.expired_at}}</div>
                    </div>
                </div>
                <span slot="footer" class="dialog-footer">
                <el-button type="primary" size="small" @click="handleCloseExpired">我知道了</el-button>
              </span>
            </el-dialog>
        </div>
    </div>

    <div class="main-container"><?= $content ?></div>
    <footer class="el-footer">
        <?php if (!empty($indSetting['copyright'])) : ?>
            <a style="color: #000;" href="<?= $indSetting['copyright_url'] ?? '' ?>"
               target="_blank"><?= $indSetting['copyright'] ?></a>
        <?php endif; ?>
    </footer>
    <div style="display: none">
        <?= $indSetting['ai_code'] ?? '' ?>
    </div>
</el-container>
<script>
    _layout = new Vue({
        el: '#_layout',
    });
    new Vue({
        el: '#_header',
        data() {
            var validatePass = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error("请输入密码"));
                } else {
                    if (this.ruleForm.checkPass !== '') {
                        this.$refs.ruleForm.validateField('checkPass');
                    }
                    callback();
                }
            };
            var validatePass2 = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error("请再次输入密码"));
                } else if (value !== this.ruleForm.pass) {
                    callback(new Error("两次输入密码不一致"));
                } else {
                    callback();
                }
            };
            return {
                nav: _siteNav,
                logo: _siteLogo,
                user: {
                    identity: {}
                },
                menus: [],
                subMenus: [],
                activeMenuId: 0,
                activeSubMenuId: 0,
                dialogFormVisible: false,
                btnLoading: false,
                ruleForm: {
                    pass: '',
                    checkPass: '',
                },
                rules: {
                    pass: [
                        {validator: validatePass, trigger: 'change'}
                    ],
                    checkPass: [
                        {validator: validatePass2, trigger: 'change'}
                    ],
                },

                dialogVisibleExpired: false,
                expiredData: {}
            }
        },
        created() {
            this.getUser();
            this.getMenus();
            setInterval(() => {
                this.$request({
                    params: {
                        r: 'keep-alive/index',
                    },
                }).then(e => {
                }).catch(e => {
                });
            }, 1000 * 60 * 5);
        },
        methods: {
            getUser() {
                let self = this;
                self.btnLoading = true;
                request({
                    params: {
                        r: 'admin/user/user'
                    },
                    method: 'get',
                    data: {}
                }).then(e => {
                    // 账号在已登录情况下被删除 要跳转到登录页面
                    if (e.data.code == -1) {
                        self.$navigate({
                            r: 'admin/passport/login',
                        });
                    }
                    self.btnLoading = false;
                    self.user = e.data.data.user;

                    self.expiredData = e.data.data.expired_data;
                    let isShowMessage = localStorage.getItem('IS_SHOW_MESSAGE');
                    if (e.data.data.expired_data.is_show_message && !isShowMessage) {
                        self.dialogVisibleExpired = true;
                    }
                }).catch(e => {
                    console.log(e);
                });
            },
            handleCommand(command) {
                let self = this;
                if (command === 'logout') {
                    localStorage.removeItem('IS_SHOW_MESSAGE');
                    request({
                        params: {
                            r: 'admin/passport/logout'
                        },
                        method: 'get',
                        data: {}
                    }).then(e => {
                        // 在当前页面打开
                        self.$navigate({
                            r: e.data.data.url,
                        });
                    }).catch(e => {
                        console.log(e);
                    });
                }
                if (command === 'updatePassword') {
                    this.dialogFormVisible = true;
                }
            },
            getMenus() {
                const cacheKey = '_ADMIN_MENUS';
                let data = localStorage.getItem(cacheKey);
                if (data) {
                    try {
                        data = JSON.parse(data);
                    } catch (e) {
                        data = false;
                    }
                }
                if (data && data.menus) {
                    this.menus = data.menus;
                    this.currentRouteInfo = data.currentRouteInfo;
                    this.setActiveMenu();
                }
                request({
                    params: {
                        r: 'admin/menus/index'
                    },
                    method: 'post',
                    data: {
                        route: getQuery('r')
                    }
                }).then(e => {
                    localStorage.setItem(cacheKey, JSON.stringify(e.data.data));
                    this.menus = e.data.data.menus;
                    this.activeMenuId = function (e) {
                        let currentRouteInfo = e.data.data.currentRouteInfo;
                        if (currentRouteInfo) {
                            if (currentRouteInfo.pid > 0) {
                                return currentRouteInfo.pid;
                            }
                            if (currentRouteInfo.id > 0) {
                                return currentRouteInfo.id
                            }
                        }
                        return 0;
                    }(e);
                    this.activeSubMenuId = e.data.data.currentRouteInfo ? e.data.data.currentRouteInfo.id : 0;
                    this.setActiveMenu();
                }).catch(e => {
                    console.log(e);
                });
            },
            setActiveMenu() {
                if (!this.activeMenuId) this.activeMenuId = localStorage.getItem('_ADMIN_LAST_ACTIVE_MENU_ID');
                if (!this.activeSubMenuId) this.activeSubMenuId = localStorage.getItem('_ADMIN_LAST_ACTIVE_SUB_MENU_ID');
                if (!this.activeMenuId || !this.activeSubMenuId) return;
                for (let i in this.menus) {
                    if (this.menus[i].id == this.activeMenuId) {
                        this.subMenus = this.menus[i].children;
                        for (let j in this.subMenus) {
                            if (this.subMenus[j].id == this.activeSubMenuId) this.subMenus[j].active = true;
                        }
                    }
                }
            },
            handleClick(tab, e) {
                for (let i in this.menus) {
                    if (this.menus[i].id == this.activeMenuId) {
                        localStorage.setItem('_ADMIN_LAST_ACTIVE_MENU_ID', this.menus[i].id);
                        this.subMenus = this.menus[i].children;
                        if (this.menus[i].route) {
                            this.openUrl(this.menus[i]);
                        } else {
                            this.openUrl(this.subMenus[0])
                        }
                    }
                }
            },
            openUrl(menu) {
                localStorage.setItem('_ADMIN_LAST_ACTIVE_SUB_MENU_ID', menu.id);
                let args = {
                    r: menu.route
                };
                if (menu.params) {
                    for (let i in menu.params) {
                        args[i] = menu.params[i];
                    }
                }
                navigateTo(args);
            },
            updatePasswordSubmit(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        let self = this;
                        self.btnLoading = true;
                        request({
                            params: {
                                r: 'admin/user/admin-edit-password'
                            },
                            method: 'post',
                            data: {
                                password: self.ruleForm.checkPass,
                            }
                        }).then(e => {
                            self.$message.success(e.data.msg);
                            self.btnLoading = false;
                            self.dialogFormVisible = false;
                        }).catch(e => {
                            console.log(e);
                        });
                    } else {
                        console.log('error submit!!');
                        return false;
                    }
                });
            },
            handleCloseExpired() {
                this.dialogVisibleExpired = false;
                localStorage.setItem('IS_SHOW_MESSAGE', 'ok');
            }
        },
    });
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
