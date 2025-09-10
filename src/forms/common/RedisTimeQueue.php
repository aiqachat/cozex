<?php
namespace app\forms\common;

use Yii;
use yii\base\Component;

/**
 * Redis时间队列组件
 * 实现基于时间排序的数据存储和按时间顺序读取功能
 */
class RedisTimeQueue extends Component
{
    /**
     * @var \yii\redis\Connection Redis连接
     */
    public $redis;
    
    /**
     * @var string Redis中存储数据的key前缀
     */
    public $keyPrefix = 'time_queue:';
    
    /**
     * 初始化Redis连接
     */
    public function init()
    {
        parent::init();
        if ($this->redis === null) {
            $this->redis = Yii::$app->redis;
        }
    }
    
    /**
     * 向队列中添加数据
     * 
     * @param string $queueName 队列名称
     * @param mixed $data 要存储的数据
     * @param int $timestamp 时间戳
     * @return bool 是否添加成功
     */
    public function push($queueName, $data, $timestamp = null)
    {
        if ($timestamp === null) {
            $timestamp = time();
        }
        
        $key = $this->keyPrefix . $queueName;
        
        // 将数据序列化后存储
        $serializedData = serialize($data);
        
        // 使用有序集合存储，score为时间戳
        return $this->redis->zadd($key, $timestamp, $serializedData) > 0;
    }
    
    /**
     * 获取并移除最小时间的数据
     * 
     * @param string $queueName 队列名称
     * @return mixed|null 返回最早的数据，如果没有数据则返回null
     */
    public function pop($queueName)
    {
        $key = $this->keyPrefix . $queueName;
        
        // 获取并移除score最小的元素（最早的时间）
        $result = $this->redis->zpopmin($key);
        
        if (empty($result)) {
            return null;
        }
        
        // 反序列化数据
        return unserialize(array_keys($result)[0]);
    }
    
    /**
     * 获取最小时间的数据（不移除）
     * 
     * @param string $queueName 队列名称
     * @return mixed|null 返回最早的数据，如果没有数据则返回null
     */
    public function peek($queueName)
    {
        $key = $this->keyPrefix . $queueName;
        
        // 获取score最小的元素（最早的时间），但不移除
        $result = $this->redis->zrange($key, 0, 0, 'WITHSCORES');
        
        if (empty($result)) {
            return null;
        }
        
        // 反序列化数据
        return unserialize(array_keys($result)[0]);
    }
    
    /**
     * 获取队列中元素数量
     * 
     * @param string $queueName 队列名称
     * @return int 元素数量
     */
    public function count($queueName)
    {
        $key = $this->keyPrefix . $queueName;
        return $this->redis->zcard($key);
    }
    
    /**
     * 移除指定的数据
     * 
     * @param string $queueName 队列名称
     * @param mixed $data 要移除的数据
     * @return int 被移除的元素数量
     */
    public function remove($queueName, $data)
    {
        $key = $this->keyPrefix . $queueName;
        $serializedData = serialize($data);
        return $this->redis->zrem($key, $serializedData);
    }
    
    /**
     * 获取指定时间范围内的数据
     * 
     * @param string $queueName 队列名称
     * @param int $startTime 开始时间戳
     * @param int $endTime 结束时间戳
     * @param bool $withTimestamp 是否包含时间戳
     * @return array 数据列表
     */
    public function rangeByTime($queueName, $startTime = 0, $endTime = null, $withTimestamp = false)
    {
        if ($endTime === null) {
            $endTime = time();
        }
        
        $key = $this->keyPrefix . $queueName;
        
        if ($withTimestamp) {
            $result = $this->redis->zrangebyscore($key, $startTime, $endTime, 'WITHSCORES');
            $data = [];
            foreach ($result as $i => $item) {
                if($i % 2 == 0) {
                    $data[] = [
                        'data' => unserialize($item),
                        'timestamp' => ''
                    ];
                }else{
                    $data[count($data) - 1]['timestamp'] = $item;
                }
            }
            return $data;
        } else {
            $result = $this->redis->zrangebyscore($key, $startTime, $endTime);
            return array_map('unserialize', $result);
        }
    }
    
    /**
     * 清空队列
     * 
     * @param string $queueName 队列名称
     * @return bool 是否清空成功
     */
    public function clear($queueName)
    {
        $key = $this->keyPrefix . $queueName;
        return $this->redis->del($key) > 0;
    }
}