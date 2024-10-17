<?php
/**
 * author: chenzs
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/11/14 13:49
 */
?>
<template id="app-image">
    <div class="app-image" :style="style"></div>
</template>
<script>
    Vue.component('app-image', {
        template: '#app-image',
        props: {
            src: String,
            mode: String,
            width: String,
            height: String,
            radius: String
        },
        data() {
            return {};
        },
        created() {
        },
        computed: {
            style() {
                let width = '50px';
                let height = '50px';
                let radius = '0%';
                let bgSize = 'cover';
                let bgPosition = 'center';
                switch (this.mode) {
                    case 'scaleToFill':
                        bgSize = '100% 100%';
                        break;
                    default:
                        bgSize = 'cover';
                        break;
                }
                if (this.width) {
                    width = this.width + (isNaN(this.width) ? '' : 'px');
                }

                if (this.height) {
                    height = this.height + (isNaN(this.height) ? '' : 'px');
                }
                if (this.radius) {
                    radius = this.radius + (isNaN(this.radius) ? '' : '%');
                }

                return `background-image:url(${this.src ? this.src : 'statics/img/mall/default_img.png'});`
                    + `background-size:${bgSize};`
                    + `background-position:${bgPosition};`
                    + `width:${width};`
                    + `height:${height};`
                    + `border-radius: ${radius};`;
            },
        },
        methods: {},
    });
</script>
