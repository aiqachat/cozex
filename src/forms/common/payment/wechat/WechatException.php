<?php
/**
 * @author: chenzs
 * Created by IntelliJ IDEA
 * Date Time: 2018/11/3 15:03
 */

namespace app\forms\common\payment\wechat;

use Throwable;

class WechatException extends \Exception
{
    protected $raw;

    public function __construct($message = "", $code = 0, Throwable $previous = null, $raw = null)
    {
        $this->raw = $raw;
        parent::__construct($message, $code, $previous);
    }

    /**
     * 获取原始信息
     * @return mixed
     */
    public function getRaw()
    {
        return $this->raw;
    }
}
