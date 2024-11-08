<?php
/**
 * Created by PhpStorm
 * User: chenzs
 * Date: 2024/9/29
 * Time: 4:08 下午
 * @copyright: ©2024 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */
?>
<style>
    .code-block {
        background: #e8efee;
        border-left: 2px solid #d2d2d2;
        margin: 10px 0;
        padding: 10px 10px;
        white-space: pre-line;
    }

    .not-exists-f {
        margin-right: 20px;
        display: inline-block;
    }

    .not-exists-f:last-child {
        margin-right: 0;
    }
</style>
<div id="app" v-cloak>
    <el-card shadow="never">
        <div slot="header">
            <span>队列服务</span>
        </div>
        <div style="padding-left: 40px;">
            <el-alert
                    v-if="not_exists_fs && not_exists_fs.length > 0"
                    title="检测到您服务器的PHP有以下函数被禁用了，请从PHP禁用函数列表中移除掉它们，否则队列服务将无法运行。"
                    type="error">
                <code v-for="(f, i) in not_exists_fs" :key="i" class="not-exists-f">{{f}}</code>
            </el-alert>
        </div>
        <ol>
            <li>
                <?php
                $queueFile = Yii::$app->basePath . '/queue.sh';
                $command = 'chmod a+x ' . $queueFile . ' && ' . $queueFile;
                ?>
                <h4>启动服务</h4>
                <div>Linux使用SSH远程登录服务器，运行命令：</div>
                <pre class="code-block"><?= $command ?></pre>
                <?php
                $queueFile = Yii::$app->basePath . '\yii';
                $command = 'php ' . $queueFile . ' queue/listen';
                ?>
                <div>Window使用cmd窗口，运行命令：</div>
                <pre class="code-block"><?= $command ?></pre>
            </li>
            <li>
                <h4>测试服务</h4>
                <el-button style="margin-bottom: 10px" @click="createQueue" :loading="testLoading">开始测试</el-button>
                <span :style="'margin-left: 10px; color: ' + (status ? 'green' : 'red') + ';'">{{msg}}</span>
                <div style="color: #909399">测试过程最多可能需要两分钟的时间。</div>
            </li>
        </ol>
    </el-card>
</div>
<script>
    new Vue({
        el: '#app',
        data() {
            return {
                testLoading: false,
                testCount: 0,
                maxTestCount: 10,
                not_exists_fs: [],
                msg: '',
                status: null,
            };
        },
        created() {
            this.checkEnv();
        },
        methods: {
            createQueue() {
                this.testLoading = true;
                this.$request({
                    params: {
                        r: 'mall/index/queue',
                        action: 'create',
                    },
                }).then(e => {
                    if (e.data.code === 0) {
                        this.testQueue(e.data.data, 'test');
                    } else {
                        this.$alert(e.data.msg);
                    }
                });
            },
            testQueue(data, action) {
                if (this.testCount >= this.maxTestCount) {
                    this.cMsg(data)
                    return;
                }
                this.testCount++;
                this.$request({
                    params: {
                        r: 'mall/index/queue',
                        action: action,
                        id: data.id,
                        time: data.time,
                        maxC: this.maxTestCount,
                        c: this.testCount,
                    },
                }).then(e => {
                    if (e.data.code === 0) {
                        if (e.data.data.done) {
                            this.cMsg(e.data.data)
                        } else {
                            data = Object.assign({}, data, e.data.data)
                            setTimeout(() => {
                                this.testQueue(data, action);
                            }, 1000);
                        }
                    } else {
                        this.cMsg(e.data.data, e.data.msg)
                    }
                });
            },
            checkEnv() {
                this.$request({
                    params: {
                        r: 'mall/index/queue',
                        action: 'env',
                    },
                }).then(e => {
                    if (e.data.code === 0) {
                        this.not_exists_fs = e.data.data.not_exists_fs;
                        this.cMsg(e.data.data)
                    }
                });
            },
            cMsg(data, msg){
                this.testLoading = false;
                this.testCount = 0;
                this.status = data.done;
                if(this.status){
                    this.msg = '队列服务测试通过，服务已正常运行。最后测试时间：' + data.date;
                }else{
                    this.msg = (msg || '队列服务测试失败，请检查服务是否正常运行') + '。最后测试时间：' + data.date;
                }
            },
        },
    });
</script>
