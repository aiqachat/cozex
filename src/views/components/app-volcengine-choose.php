<?php
/**
 * author: chenzs
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 */
?>
<template id="app-volcengine-choose">
    <div class="app-volcengine-choose" style="margin-top: 10px;">
        <el-form size="small" :inline="true" :model="search" v-loading="loading">
<<<<<<< HEAD
            <el-tag v-if="title" style="background-color: #ffff;border-radius: 10px;margin-right: 10px;font-weight: bold;font-size: 14px;">{{title}}</el-tag>
=======
>>>>>>> aa46331817a85d4745f22daa8a771a67c28a9ec7
            <el-form-item label="应用名称：">
                <el-select v-model="search.account_id" placeholder="请选择应用">
                    <el-option v-for="item in account" :key="item.id" :label="item.name" :value="item.id"></el-option>
                </el-select>
                <el-button type="text" @click="$navigate({r:'mall/setting/volcengine'}, true)" style="padding-left: 5px;">添加应用</el-button>
                <el-tooltip effect="dark" content="刷新" placement="right">
                    <el-button class="el-icon-refresh" type="text" @click="getAccount"></el-button>
                </el-tooltip>
            </el-form-item>
        </el-form>
        <el-dialog title="添加语音技术授权" :visible.sync="dialog" width="30%" :before-close="close">
            <div flex="main:center" style="font-size: 18px;">
                请先在语音技术配置绑定密钥
            </div>
            <div flex="main:center" style="margin-top: 10px;">
                <el-button @click="$navigate({r:'mall/setting/volcengine'}, true)" size="small" type="primary">去绑定</el-button>
            </div>
            <div slot="footer">
                <el-button @click="close" type="primary">我知道了</el-button>
            </div>
        </el-dialog>
    </div>
</template>
<script>
Vue.component('app-volcengine-choose', {
    template: '#app-volcengine-choose',
    props: {
        dialog: Boolean,
<<<<<<< HEAD
        title: String,
=======
>>>>>>> aa46331817a85d4745f22daa8a771a67c28a9ec7
    },
    data() {
        return {
            search: {
                account_id: null,
            },
            loading: false,
            account: [],
            cookieName: 'volcengine-account-choose',
        }
    },
    watch: {
        'search.account_id': function (val, oldValue){
            this.$emit('account', val);
            this.saveCookie();
        },
    },
    created() {
        let search = this.getCookie();
        if (search) {
            this.search = Object.assign(this.search, search);
        }
        this.getAccount();
    },
    methods: {
        getAccount(){
            this.loading = true;
            request({
                params: {
                    r: 'mall/index/volcengine-account',
                },
                method: 'get',
            }).then(e => {
                this.loading = false;
                this.account = e.data.data.account;
                let search = false;
                this.account.forEach(item => {
                    if (item.id === this.search.account_id) {
                        search = true;
                    }
                });
                if(!search){
                    this.search.account_id = this.account.length > 0 ? this.account[0].id : null;
                }
            }).catch(e => {
                this.loading = false;
            });
        },
        close() {
            this.$emit('close');
        },
        getCookie() {
            let params = localStorage.getItem(this.cookieName);
            params = JSON.parse(params);
            return params || {};
        },
        saveCookie() {
            localStorage.setItem(this.cookieName, JSON.stringify(this.search));
        },
    },
});
</script>
