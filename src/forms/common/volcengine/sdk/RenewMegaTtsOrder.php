<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\sdk;

// 音色续费  https://www.volcengine.com/docs/6561/1305191
class RenewMegaTtsOrder extends Basics
{
    /** @var integer 续费音色的时长，单位为月 */
    public $Times;

    /** @var array 要续费的SpeakerID的列表 */
    public $SpeakerIDs;

    /** @var boolean 是否自动使用代金券 */
    public $AutoUseCoupon;

    /** @var boolean 代金券ID，通过代金券管理获取 */
    public $CouponID;

    function setIdent()
    {
        // TODO: Implement getSign() method.
        $this->speech();
        $this->action = 'RenewAccessResourcePacks';
    }
}
