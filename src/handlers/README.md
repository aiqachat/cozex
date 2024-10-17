# 公共消息通道

公共消息通道用于服务器端向小程序推送消息，基于事件系统和小程序定时请求服务端实现。

- 消息通道在小程序onHide时自动停止，在onShow时自动开始

- 消息通道每5秒请求一次

## 小程序端使用

创建消息事件处理监听器，接受服务器端的数据

```javascript
let removeTrigger = false; // 是否在出发后删除该处理器，这里设置成false，因为消息通道会有多次数据推送。
getApp().event.on('app_message_response', removeTrigger).then(response => {
    console.log(response); // data=>服务器端推送的数据
});
```

## 服务器端使用

### \Yii::$app->appMessage->push说明

在事件处理器中使用`\Yii::$app->appMessage->push(key, data);`实现推送数据

- key: 尽量确保key唯一，避免覆盖其他地方使用

- data: string或array类型

### 非插件下使用

创建事件处理器app\handlers\AppMessageTestHandler

```php
namespace app\handlers;
class AppMessageTestHandler extends HandlerBase
{
    public function register()
    {
        // 此处必须是\Yii::$app->appMessage::EVENT_APP_MESSAGE_REQUEST事件
        \Yii::$app->on(\Yii::$app->appMessage::EVENT_APP_MESSAGE_REQUEST, function ($event) {
            // 推送消息
            \Yii::$app->appMessage->push('test_msg', '测试消息');
        });
    }
}

```

在app\handlers\HandlerRegister注册事件处理器

```php
class HandlerRegister extends BaseObject
{
    public function getHandlers()
    {
        return [
            AppMessageTestHandler::class, // 注册事件处理器
        ];
    }
}

```

### 插件下使用

在插件的app\plugins\xxx\Plugin中实现handler方法，编写\Yii::$app->appMessage::EVENT_APP_MESSAGE_REQUEST事件处理器

```php
    public function handler()
    {
        // 举例，app message 请求事件
        \Yii::$app->on(\Yii::$app->appMessage::EVENT_APP_MESSAGE_REQUEST, function ($event) {
            \Yii::$app->appMessage->push('plugin_wxapp_test', '测试插件消息');
        });
    }
```
