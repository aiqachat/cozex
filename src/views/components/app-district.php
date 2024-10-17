<?php
/**
 * @link:https://www.netbcloud.com/
 * @copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 *
 * Created by PhpStorm.
 * User: chenzs
 * Date: 2018/12/4
 * Time: 9:47
 */
?>
<!--注：本组件仅做省市区选择，返回值为选中的省市区数组，其他数据处理请自行编写-->
<template id="app-district">
    <div class="app-district">
        <el-card v-loading="districtLoading" shadow="never" body-style="padding:0" style="border:0;min-height: 100px;">
            <div flex="dir:left box:mean">
                <template v-if="list.length > 0">
                    <el-card style="max-height: 400px;overflow-y: auto;margin-right: 20px;" shadow="never">
                        <slot name="other"></slot>
                        <template v-for="(item, index) in list">
                            <div :id="'#' + item.id" class="el-checkbox" style="margin: 0 0 20px;display: block"
                                 @click="selectProvince(index)">
                                <el-checkbox v-if="!radio" @change="pickerChange(item)"
                                             v-model="item.checked" :indeterminate="item.isIndeterminate"
                                             :disabled="item.unchecked || all">
                                    <span style="display: none" class="el-checkbox__label">{{item.name}}</span>
                                </el-checkbox>
                                <el-radio style="margin-right: 0" v-else @change="pickerChange(item)" v-model="item.isIndeterminate" :label="true">
                                    <span style="display: none">{{item.name}}</span>
                                </el-radio>
                                <span class="el-checkbox__label">{{item.name}}</span>
                            </div>
                        </template>
                    </el-card>
                    <el-card style="max-height: 400px;overflow-y: auto;margin-right: 20px;" shadow="never"
                         v-if="list[p_index].list && list[p_index].list.length > 0">
                        <template v-for="(item, index) in list[p_index].list">
                            <div :id="'#' + item.id" class="el-checkbox" @click="c_index = index" style="margin: 0 0 20px;display: block">
                                <el-checkbox v-if="!radio" @change="pickerChange(item)"
                                             v-model="item.checked" :indeterminate="item.isIndeterminate"
                                             :disabled="item.unchecked || all">
                                    <span style="display: none" class="el-checkbox__label">{{item.name}}</span>
                                </el-checkbox>
                                <el-radio style="margin-right: 0" v-else @change="pickerChange(item)" v-model="item.isIndeterminate" :label="true">
                                    <span style="display: none">{{item.name}}</span>
                                </el-radio>
                                <span class="el-checkbox__label">{{item.name}}</span>
                            </div>
                        </template>
                    </el-card>
                    <el-card style="max-height: 400px;overflow-y: auto;margin-right: 20px;" shadow="never"
                         v-if="list[p_index].list[c_index].list && list[p_index].list[c_index].list.length > 0">
                        <template v-for="(item, index) in list[p_index].list[c_index].list">
                            <div :id="'#' + item.id" class="el-checkbox" @click="d_index = index" style="margin: 0 0 20px;display: block">
                                <el-checkbox v-if="!radio" @change="pickerChange(item)"
                                             v-model="item.checked"
                                             :disabled="item.unchecked || all">
                                    <span style="display: none" class="el-checkbox__label">{{item.name}}</span>
                                </el-checkbox>
                                <el-radio style="margin-right: 0" v-else @change="pickerChange(item)" v-model="item.checked" :label="true">
                                    <span style="display: none">{{item.name}}</span>
                                </el-radio>
                                <span class="el-checkbox__label">{{item.name}}</span>
                            </div>
                        </template>
                    </el-card>
                </template>
            </div>
        </el-card>
    </div>
</template>
<script>
    Vue.component('app-district', {
        template: '#app-district',
        data: function () {
            return {
                p_index: 0,
                c_index: 0,
                d_index: 0,
                districtLoading: false,
                first: true,
                defaultList: [], // 所有省市区
                tempList: [],// 已选择待返回的省市区
                list: [] // 渲染页面使用省市区列表
            }
        },
        props: {
            detail: Array, // 传入的所有省市区数组
            edit: Array, // 传入的选中省市区数组
            level: Number, // 展示省市区的级数
            params: Array, // 传出值
            all: Boolean,
            radio: Boolean
        },
        mounted: function () {
            let self = this;
            self.districtLoading = true;
            request({
                params: {
                    r: 'district/index',
                    level: this.level
                },
                method: 'get'
            }).then(function (e) {
                self.districtLoading = false;
                if (e.data.code == 0) {
                    self.defaultList = e.data.data.district;
                    self.newList();
                } else {
                    self.$message.error(e.data.msg);
                }
            }).catch(function (e) {
                self.districtLoading = false;
            });
        },
        watch: {
            // 监听传入的省市区数组
            detail() {
                this.newList();
            },
            edit() {
                this.newList();
            }
        },
        methods: {
            // 根据传入数据重新获取省市区列表
            newList() {
                let defaultList = this.checkList(this.defaultList, this.detail, this.edit);
                let listP = this.setListUnchecked(defaultList, 1);
                let list = this.setListUnchecked(listP, 0);
                this.list = JSON.parse(JSON.stringify(list));
                if(this.radio) {
                    for(let p_index in this.list) {
                        if(this.list[p_index].isIndeterminate) {
                            this.p_index = p_index
                            if(this.first) {
                                let id = '#' + this.list[p_index].id;
                                setTimeout(()=>{
                                    this.first = false;
                                    let element = document.getElementById(id);
                                    element.scrollIntoView();
                                })
                            }
                            for(let c_index in this.list[p_index].list) {
                                if(this.list[p_index].list[c_index].isIndeterminate) {
                                    this.c_index = c_index
                                }
                            }
                        }
                    }
                }
            },

            // 判断哪些省市区已经选择
            checkList(defaultList, formDetail, edit) {
                if (typeof defaultList == 'undefined') {
                    return [];
                }
                for (let i in defaultList) {
                    defaultList[i].checked = false;
                    defaultList[i].unchecked = false;
                    defaultList[i].isIndeterminate = false;
                    if (typeof defaultList[i].list != 'undefined') {
                        defaultList[i].list = this.checkList(defaultList[i].list, formDetail, edit);
                    }
                    if (typeof formDetail != 'undefined') {
                        for (let j in formDetail) {
                            if (typeof formDetail[j].list != 'undefined') {
                                for (let k in formDetail[j].list) {
                                    if (defaultList[i].id == formDetail[j].list[k].id) {
                                        defaultList[i].unchecked = true;
                                        break;
                                    }
                                }
                            } else {
                                if (defaultList[i].id == formDetail[j].id) {
                                    defaultList[i].unchecked = true;
                                    break;
                                }
                            }
                        }
                    }
                    if (typeof edit != 'undefined') {
                        for (let j in edit) {
                            if (typeof edit[j].list != 'undefined') {
                                for (let k in edit[j].list) {
                                    if (defaultList[i].id == edit[j].list[k].id) {
                                        defaultList[i].checked = true;
                                        defaultList[i].unchecked = false;
                                        break;
                                    }
                                }
                            } else {
                                if (defaultList[i].id == edit[j].id) {
                                    defaultList[i].checked = true;
                                    defaultList[i].unchecked = false;
                                    break;
                                }
                            }
                        }
                    }
                }
                return defaultList;
            },
            // 重新赋值已经选择的省市区level == 1父级影响子级||level == 0子级影响父级
            setListUnchecked(list, level = 1) {
                if (typeof list == 'undefined') {
                    return list;
                }
                for (let i in list) {
                    if (typeof list[i].list != 'undefined') {
                        if(level == 0) {
                            list[i].list = this.setListUnchecked(list[i].list, 0);
                        }
                        let unchecked = false;
                        let checkCount = 0;
                        let isIndeterminateCount = 0;
                        for (let j in list[i].list) {
                            if (list[i].unchecked && level == 1) {
                                list[i].list[j].unchecked = true;
                            }
                            if (list[i].list[j].unchecked) {
                                unchecked = true;
                            }
                            if (list[i].checked) {
                                list[i].list[j].checked = true;
                            }
                            if(list[i].list[j].checked) {
                                checkCount++;
                            }
                            if(list[i].list[j].isIndeterminate) {
                                isIndeterminateCount++;
                            }
                        }
                        if (unchecked && level == 0) {
                            list[i].unchecked = true;
                        }
                        if(list[i].list.length == checkCount) {
                            list[i].checked = true;
                        } else if (checkCount > 0) {
                            list[i].checked = false;
                            list[i].isIndeterminate = true;
                        } else {
                            list[i].checked = false;
                            if(isIndeterminateCount > 0) {
                                list[i].isIndeterminate = true;
                            } else {
                                list[i].isIndeterminate = false;
                            }
                        }
                        if(level == 1) {
                            list[i].list = this.setListUnchecked(list[i].list, 1);
                        }
                    }
                }
                return list;
            },
            // 将当前值的子列表的选中状态与当前值一致
            changeAllList(list, checked) {
                if (typeof list != 'undefined') {
                    for (let i in list) {
                        if (typeof list[i].list != 'undefined') {
                            list[i].list = this.changeAllList(list[i].list, checked);
                        }
                        list[i].checked = checked;
                    }
                }
                return list;
            },
            // 重置所有省市区的选中状态 返回已选中的省市区
            setTempList(list) {
                let newList = [];
                let checkCount = 0;
                let isIndeterminateCount = 0;
                for (let i in list) {
                    if (typeof list[i].list != 'undefined') {
                        let childList = this.setTempList(list[i].list);
                        if (childList.isIndeterminateCount > 0) {
                            list[i].checked = false;
                            list[i].isIndeterminate = true;
                            isIndeterminateCount++;
                            newList = newList.concat(childList.newList);
                        } else if (childList.checkCount < list[i].list.length) {
                            list[i].checked = false;
                            if (childList.checkCount > 0) {
                                list[i].isIndeterminate = true;
                                isIndeterminateCount++;
                                newList = newList.concat(childList.newList);
                            } else {
                                list[i].isIndeterminate = false;
                            }
                        } else {
                            list[i].checked = true;
                            list[i].isIndeterminate = false;
                        }
                    }
                    if (list[i].checked) {
                        checkCount++;
                        newList.push(list[i]);
                        // console.log(newList)
                    }
                }
                return {
                    newList: newList,
                    checkCount: checkCount,
                    isIndeterminateCount: isIndeterminateCount
                };
            },
            // 向父组件发送消息
            pickerChange(item) {
                console.log(item)
                if(this.radio) {
                    let provinceItem = {
                        id: '',
                        name: ''
                    };
                    let cityItem = {
                        id: '',
                        name: ''
                    };
                    let districtItem = {
                        id: '',
                        name: ''
                    };
                    for(let province of this.list) {
                        province.isIndeterminate = false;
                        if(item.parent_id == 1) {
                            if(item.id == province.id) {
                                province.isIndeterminate = true;
                                provinceItem.id = province.id;
                                provinceItem.name = province.name;
                            }
                        }else {
                            if(item.level == 'city' && item.parent_id == province.id) {
                                province.isIndeterminate = true;
                                provinceItem.id = province.id;
                                provinceItem.name = province.name;
                            }
                        }
                        for(let city of province.list) {
                            city.isIndeterminate = false;
                            if(item.level == 'city') {
                                if(item.id == city.id) {
                                    city.isIndeterminate = true;
                                    cityItem.id = city.id;
                                    cityItem.name = city.name;
                                }
                            }
                            if(item.level == 'district') {
                                if(item.parent_id == city.id) {
                                    city.isIndeterminate = true;
                                    cityItem.id = city.id;
                                    cityItem.name = city.name;
                                    province.isIndeterminate = true;
                                    provinceItem.id = province.id;
                                    provinceItem.name = province.name;
                                }
                            }
                            for(let district of city.list) {
                                district.checked = false;
                                if(item.level == 'district') {
                                    if(item.id == district.id) {
                                        district.checked = true;
                                        districtItem.id = district.id;
                                        districtItem.name = district.name;
                                        this.$emit('selected', [provinceItem,cityItem,districtItem]);
                                    }
                                }
                            }
                        }
                    }
                }else {
                    this.changeAllList(item.list, item.checked);
                    let tempList = this.setTempList(this.list);
                    this.tempList = tempList.newList;
                    console.log(this.tempList, this.params)
                    this.$emit('selected', this.tempList, this.params);
                }
            },
            // 选择省份
            selectProvince(index) {
                this.p_index = index;
                this.c_index = 0;
                this.d_index = 0;
            }
        }
    });
</script>
