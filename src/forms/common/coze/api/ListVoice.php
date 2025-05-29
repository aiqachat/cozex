<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\coze\api;

use app\forms\common\coze\Base;

// 查看音色列表  https://www.coze.cn/open/docs/developer_guides/list_voices
class ListVoice extends Base
{
    /** @var boolean 查看音色列表时是否过滤掉系统音色。
        true：过滤系统音色
        false：（默认）不过滤系统音色
     */
    public $filter_system_voice;

    /** @var string 音色模型的类型，如果不填，默认都返回。可选值包括：
            big：大模型
            small：小模型
     */
    public $model_type;

    /** @var integer 查询结果分页展示时，此参数用于设置查看的页码。最小值为 1，默认为 1。 */
    public $page_num;

    /** @var integer 查询结果分页展示时，此参数用于设置每页返回的数据量。取值范围为 1~100，默认为 100。 */
    public $page_size;

    public function getMethodName()
    {
        return "/v1/audio/voices";
    }

    function getMethod()
    {
        return self::METHOD_GET;
    }
}
