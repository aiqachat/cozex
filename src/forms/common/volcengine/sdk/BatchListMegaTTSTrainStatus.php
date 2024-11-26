<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\sdk;

// 分页查询SpeakerID状态  https://www.volcengine.com/docs/6561/1305191
class BatchListMegaTTSTrainStatus extends Basics
{
    /** @var string AppID */
    public $AppID;

    /** @var array SpeakerID的列表，传空为返回指定APPID下的全部SpeakerID */
    public $SpeakerIDs;

    /** @var string 音色状态，支持取值：Unknown、Training、Success、Active、Expired、Reclaimed 详见附录：State状态枚举值 */
    public $State;

    /** @var int 页数, 需大于0, 默认为1 */
    public $PageNumber;

    /** @var int 每页条数, 必须在范围[1, 100]内, 默认为10 */
    public $PageSize;

    /** @var string 上次请求返回的字符串; 如果不为空的话, 将覆盖PageNumber及PageSize的值 */
    public $NextToken;

    /** @var int 与NextToken相配合控制返回结果的最大数量; 如果不为空则必须在范围[1, 100]内, 默认为10 */
    public $MaxResults;

    function setIdent()
    {
        // TODO: Implement getSign() method.
        $this->speech();
        $this->action = 'BatchListMegaTTSTrainStatus';
    }
}
