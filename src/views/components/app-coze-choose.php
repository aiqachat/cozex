<?php
/**
 * author: chenzs
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 */
?>
<style>
    .space_img{
        width: 32px;
        height: 32px;
        border-radius: 50%;
        margin-right: 5px;
    }
</style>
<template id="app-coze-choose">
    <div class="app-coze-choose" style="margin-top: 10px;">
        <el-form size="small" :inline="true" :model="search" v-loading="loading">
            <el-form-item label="COZE账号：">
                <el-select v-model="search.account_id" placeholder="请选择coze账号">
                    <el-option v-for="item in account" :key="item.id" :label="item.name" :value="item.id"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="空间列表：">
                <el-dropdown placement="bottom-start">
                    <span style="display: flex;align-items: center;" v-if="search.space_id">
                        <img :src="search.icon_url" class="space_img"/>
                        {{ search.name || '--' }}
                        <i class="el-icon-arrow-down el-icon--right"></i>
                    </span>
                    <el-dropdown-menu slot="dropdown" v-if="space.length > 0">
                        <el-dropdown-item style="padding: 10px 20px 5px 20px;"
                                v-for="(item, index) in space"
                                :key="index"
                                @click.native="changeSpace(item)">
                            <div style="display: flex;">
                                <img :src="item.icon_url" class="space_img"/>
                                {{ item.name || '未命名' }}
                            </div>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </el-form-item>
        </el-form>
    </div>
</template>
<script>
Vue.component('app-coze-choose', {
    template: '#app-coze-choose',
    props: {
        all: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            search: {
                account_id: null,
                space_id: null,
            },
            loading: false,
            account: [],
            space: [],
            cookieName: 'cozex-account-choose',
        }
    },
    watch: {
        'search.account_id': function (val, oldValue){
            this.$emit('account', val);
            this.getSpace();
        },
        'search.space_id': function (val, oldValue){
            if(!this.search.account_id){
                this.search.space_id = null;
            }else {
                this.$emit('space', val);
            }
            this.saveCookie();
        }
    },
    created() {
        let search = this.getCookie();
        if (search) {
            this.search = Object.assign(this.search, search);
        }
        this.getAccount();
    },
    methods: {
        changeSpace(item){
            this.search = Object.assign({}, this.search, {
                space_id: item.id || '',
                icon_url: item.icon_url || '',
                name: item.name || '',
            });
        },
        getAccount(){
            this.loading = true;
            request({
                params: {
                    r: 'mall/index/coze-account',
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
        getSpace(){
            if(this.search.space_id === 'all'){
                this.search.space_id = '';
            }
            this.space = [];
            this.loading = true;
            request({
                params: {
                    r: 'mall/index/coze-space',
                    id: this.search.account_id
                },
                method: 'get',
            }).then(e => {
                this.loading = false;
                this.space = e.data.data.space;
                if(this.all){
                    this.space.unshift({
                        id: 'all',
                        name: '全部空间',
                        icon_url: 'statics/img/mall/poster-big-shop.png',
                    });
                }
                let search = false;
                this.space.forEach(item => {
                    if (item.id === this.search.space_id) {
                        search = true;
                    }
                });
                if(!search){
                    this.changeSpace(this.space[0]);
                }
            }).catch(e => {
                this.search.space_id = null;
                this.loading = false;
            });
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
