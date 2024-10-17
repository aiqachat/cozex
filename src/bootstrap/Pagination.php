<?php
/**
 * @copyright ©2022 wstianxia
 * author: wstianxia
 * Created by IntelliJ IDEA
 * Date Time: 2018/10/18 12:02
 */


namespace app\bootstrap;


class Pagination extends \yii\data\Pagination
{
    public $defaultPageSize = 10;
    public $pageSize;
    public $total_count;
    public $page_count;
    public $current_page;

    public function init()
    {
        parent::init();
        if (!$this->pageSize) {
            $this->pageSize = $this->defaultPageSize;
        }
        $this->totalCount = $this->totalCount ? intval($this->totalCount) : 0;
        $this->total_count = $this->totalCount;
        $this->page_count = $this->pageCount;
        $this->current_page = $this->page + 1;
    }

    public function getPageSize()
    {
        return $this->pageSize;
    }
}
