<?php
/**
 * 项目本地配置
 */

return [
    'redis' => [
        'class' => 'yii\redis\Connection',
        'hostname' => '127.0.0.1',
        'port' => 6379,
    ],
    'cache' => [
        'class' => 'yii\caching\FileCache',
    ],
    'enableSchemaCache' => false,
    'schemaCacheDuration' => 3600,
    'schemaCache' => 'cache',
    'session' => [
        'name' => 'WS_SESSION_ID',
        'class' => 'yii\web\DbSession',
        'sessionTable' => '{{%core_session}}',
    ],
    'debugAllowedIPs' => ['127.0.0.1', '::1',],
    'giiAllowedIPs' => ['127.0.0.1', '::1',],
    // redis 消息队列
    'queue' => [
        'class' => \yii\queue\db\Queue::class,
        'tableName' => '{{%core_queue}}',
    ],
    // kafka消息队列 @czs
    'kafka' => [
        'class' => \app\bootstrap\kafka\Queue::class,
        'channel' => 'test', // topic的名称
        'global' => [
            'group.id' => 'consumer',
            'metadata.broker.list' => '10.150.2.4:9092',
            'enable.auto.commit' => 'false',

            // ---------- 启用 SASL 验证时需要设置 （外网访问）----------
            'sasl.mechanism' => 'PLAIN',
            // 设置用户名：实例 ID + # + **用户管理**中配置的用户名
            'sasl.username' => 'ckafka-aj4q3e5b#test',
            //  设置密码：**用户管理**中配置的密码
            'sasl.password' => '123456abc',
            // 在本地配置 ACL 策略。
            'security.protocol' => 'SASL_PLAINTEXT',
            // Kafka producer 的 ack 有 3 种机制，分别说明如下：
            // -1 或 all：Broker 在 leader 收到数据并同步给所有 ISR 中的 follower 后，才应答给 Producer 继续发送下一条（批）消息。
            // 这种配置提供了最高的数据可靠性，只要有一个已同步的副本存活就不会有消息丢失。注意：这种配置不能确保所有的副本读写入该数据才返回，
            // 可以配合 Topic 级别参数 min.insync.replicas 使用。
            // 0：生产者不等待来自 broker 同步完成的确认，继续发送下一条（批）消息。这种配置生产性能最高，但数据可靠性最低
            //（当服务器故障时可能会有数据丢失，如果 leader 已死但是 producer 不知情，则 broker 收不到消息）
            // 1： 生产者在 leader 已成功收到的数据并得到确认后再发送下一条（批）消息。这种配置是在生产吞吐和数据可靠性之间的权衡
            //（如果leader已死但是尚未复制，则消息可能丢失）
            // 用户不显示配置时，默认值为1。用户根据自己的业务情况进行设置
            'acks' => '1',
            // 请求发生错误时重试次数，建议将该值设置为大于0，失败重试最大程度保证消息不丢失
            'retries' => '0',
            // 发送请求失败时到下一次重试请求之间的时间
            'retry.backoff.ms' => '100',
            // ---------- 启用 SASL 验证时需要设置 （外网访问）----------
        ],
        'topic' => [
            // producer 网络请求的超时时间。
            'socket.timeout.ms' => '6000',
            'reconnect.backoff.max.ms' => '3000',
        ],
    ],
];
