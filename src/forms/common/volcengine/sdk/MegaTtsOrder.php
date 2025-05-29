<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\sdk;

// 音色下单  https://www.volcengine.com/docs/6561/1305191
class MegaTtsOrder extends Basics
{
    /** @var string AppID */
    public $AppID;

    /** @var string 平台的服务类型资源标识 */
    public $ResourceID = 'volc.megatts.voiceclone';

    /** @var string 平台的计费项标识 */
    public $Code = 'Model_storage';

    /** @var integer 下单单个音色的时长，单位为月 */
    public $Times;

    /** @var integer 下单音色的个数，如100，即为购买100个音色 */
    public $Quantity;

    /** @var boolean 是否自动使用代金券 */
    public $AutoUseCoupon;

    /** @var boolean 代金券ID，通过代金券管理获取 */
    public $CouponID;

    function setIdent()
    {
        // TODO: Implement getSign() method.
        $this->speech();
        $this->action = 'OrderAccessResourcePacks';
    }
}
