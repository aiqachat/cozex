<?php
/**
 * author: chenzs
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/11/14 13:49
 */
?>
<style>
.app-gallery .app-gallery-list {
    -webkit-flex-wrap: wrap;
    flex-wrap: wrap;
}

.app-gallery .app-gallery-item {
    width: 100px;
    height: 100px;
    border: 1px solid #e3e3e3;
    border-radius: 2px;
    margin-right: 10px;
    margin-bottom: 10px;
    position: relative;
}

.app-gallery .app-gallery-delete {
    position: absolute;
    right: -8px;
    top: -8px;
    padding: 4px 4px;
}

.app-gallery .app-gallery-img {
    max-width: 100%;
    max-height: 100%;
}

</style>
<template id="app-gallery">
    <div class="app-gallery">
        <!--<el-button @click="test">test</el-button>-->
        <div class="app-gallery-list" flex>
            <template v-for="(item, index) in defaultList">
                <div class="app-gallery-item" flex="main:center cross:center" :style="reversedStyle">
                    <el-button v-if="showDelete && item[urlKey ? urlKey : 'url']" class="app-gallery-delete"
                               size="mini" type="danger" icon="el-icon-close" circle
                               @click="deleted(item, index)"></el-button>
                    <img class="app-gallery-img" :src="item[urlKey ? urlKey : 'url']">
                </div>
            </template>
        </div>
    </div>
</template>
<script>
Vue.component('app-gallery', {
    template: '#app-gallery',
    props: {
        list: Array,
        urlKey: String,
        width: String,
        height: String,
        showDelete: Boolean,
        url: String
    },
    data() {
        return {};
    },
    created() {
    },
    computed: {
        reversedStyle() {
            return (this.height ? `height: ${this.height}; ` : '') + (this.width ? `width: ${this.width}; ` : '');
        },
        defaultList() {
            if (typeof this.url != 'undefined') {
                return [{
                    url: this.url
                }];
            } else {
                return this.list;
            }
        }
    },
    methods: {
        test() {
            console.log(this);
        },
        deleted(item, index) {
            this.$emit('deleted', item, index);
        },
    },
});
</script>
