<style>

</style>

<template id="app-header">
    <div class="app-header" flex="dir:left box:first cross:center">
        <span><slot></slot></span>
<!--        <div flex="dir:right">-->
<!--            <app-new-export-dialog-2-->
<!--                text="导出全部"-->
<!--                :params="search"-->
<!--                :directly=true-->
<!--                :action_url="url">-->
<!--            </app-new-export-dialog-2>-->
<!--        </div>-->
    </div>
</template>

<script>
    Vue.component('app-header', {
        template: '#app-header',
        props: {
            url: {
                type: String,
                default: ''
            },
            newSearch: {
                type: String,
                default: '',
            }
        },
        watch: {
            newSearch: function (newVal) {
                let self = this;
                let newSearch = JSON.parse(newVal);
                self.search = newSearch;
            }
        },
        data() {
            return {
                search: [],
            }
        },
        created() {
            this.search = JSON.parse(this.newSearch)
        }
    })
</script>
