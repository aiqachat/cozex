<style>
    .app-search {
        padding: 15px;
    }

    .app-search>div {
        margin-right: 5px;
    }

    .app-search .clean {
        color: #92959B;
        margin-left: 35px;
        cursor: pointer;
        font-size: 15px;
    }

    .app-search .input-item {
        display: inline-block;
        width: 250px;
    }

    .app-search .input-item .el-input__inner {
        border-right: 0;
    }

    .app-search .input-item .el-input__inner:hover {
        border: 1px solid #dcdfe6;
        border-right: 0;
        outline: 0;
    }

    .app-search .input-item .el-input__inner:focus {
        border: 1px solid #dcdfe6;
        border-right: 0;
        outline: 0;
    }

    .app-search .input-item .el-input-group__append {
        background-color: #fff;
        border-left: 0;
        width: 10%;
        padding: 0;
    }

    .app-search .input-item .el-input-group__append .el-button {
        padding: 0;
    }

    .app-search .input-item .el-input-group__append .el-button {
        margin: 0;
    }
</style>


<template id="app-search">
    <div class="app-search" flex="cross:center">
        <slot name="select"></slot>
        <!-- 时间选择框 -->
        <template v-if="isShowPicker">
            <div>
                <el-date-picker
                        size="small"
                        @change="changeTime"
                        style="width: 380px;margin-right: 30px;"
                        v-model="search.time"
                        type="daterange"
                        value-format="yyyy-MM-dd"
                        range-separator="至"
                        start-placeholder="开始日期"
                        end-placeholder="结束日期">
                </el-date-picker>
            </div>
            <div>
                <el-tabs v-model="activeName" @tab-click="tabTotal">
                    <el-tab-pane label="7日" name="week"></el-tab-pane>
                    <el-tab-pane label="30日" name="month"></el-tab-pane>
                </el-tabs>
            </div>
        </template>
        <div v-if="isShowKeyword" class="input-item">
            <el-input
                    @keyup.enter.native="getList"
                    @clear="getList"
                    clearable
                    size="small"
                    :placeholder="placeholder"
                    v-model="search.name">
                <el-button slot="append" icon="el-icon-search" @click="getList"></el-button>
            </el-input>
        </div>
        <slot name="other"></slot>
        <div>
            <div class="clean" @click="clean">清空筛选</div>
        </div>
    </div>
</template>

<script>
    Vue.component('app-search', {
        template: '#app-search',
        props: {
            newSearch: {
                type: Object,
                default: function () {
                    return {}
                }
            },
            dayData: {
                type: Object,
                default: function () {
                    return {}
                }
            },
            isShowKeyword: {
                type: Boolean,
                default: true
            },
            isShowPicker: {
                type: Boolean,
                default: true
            },
            placeholder: {
                type: String,
                default: "请输入搜索内容"
            }
        },
        data() {
            return {
                search: {},
                // 日期选中状态
                activeName: '',
                // 今天
                today: '',
                // 七天前
                weekDay: '',
                // 30天前
                monthDay: '',
            }
        },
        methods: {
            // 清空筛选
            clean() {
                this.search = {
                    time: null,
                    date_start: null,
                    date_end: null,
                    name: '',
                };
                this.activeName = null;
                this.getList('clean');
            },
            // 选择时间区间
            tabTotal() {
                console.log(this.activeName);
                if (this.activeName == 'week') {
                    this.search.time = [this.weekDay, this.today];
                }
                if (this.activeName == 'month') {
                    this.search.time = [this.monthDay, this.today];
                }
                this.search.date_start = this.search.time[0];
                this.search.date_end = this.search.time[1];
                this.getList()
            },
            // 自定义时间
            changeTime() {
                this.activeName = null;
                if (this.search.time) {
                    this.search.date_start = this.search.time[0];
                    this.search.date_end = this.search.time[1];
                } else {
                    this.search.date_start = null;
                    this.search.date_end = null;
                }
                this.getList();
            },
            getList(type) {
                this.$emit('search', this.search,type)
            },
            // 废弃
            toSearch() {
                this.$emit('to-search', this.search)
            }
        },
        created() {
            this.search = this.newSearch;
            this.today = this.dayData.today;
            this.weekDay = this.dayData.weekDay;
            this.monthDay = this.dayData.monthDay;
        }
    })
</script>