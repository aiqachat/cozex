<?php
/**
 * @link:https://www.netbcloud.com/
 * @copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 *
 * Created by PhpStorm.
 * User: wstianxia
 * Date: 2018/11/30
 * Time: 11:50
 */

namespace app\models\BaseQuery;


use app\bootstrap\Pagination;
use yii\db\ActiveQuery;

class BaseActiveQuery extends ActiveQuery
{
    /**
     * @param Pagination|null $pagination
     * @param Integer $limit
     * @param Integer $page
     * @return BaseActiveQuery
     */
    public function page(&$pagination = null, $limit = 20, $page = null)
    {
        $count = $this->count();
        if ($page) {
            $currentPage = $page - 1;
        } else {
            $currentPage = \Yii::$app->request->get('page', 1) - 1;
        }
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $limit, 'page' => $currentPage]);
        $this->limit($pagination->limit)->offset($pagination->offset);
        return $this;
    }

    /**
     * @param int $limit
     * @param int $page
     * @return BaseActiveQuery
     * 无需计算总数的分页
     */
    public function apiPage($limit = 20, $page = 1)
    {
        $offset = ($page - 1) * $limit;
        $this->limit($limit)->offset($offset);
        return $this;
    }

    /**
     * @param string|boolean $keyword
     * @param array|string $condition
     * @return BaseActiveQuery
     * 当keyword为true时，将条件添加到andWhere中
     */
    public function keyword($keyword, $condition)
    {
        if ($keyword) {
            $this->andWhere($condition);
        }
        return $this;
    }

    public function findInSet($str, $params)
    {
        $this->andWhere('find_in_set(\'' . $str . '\',`' . $params . '`)');
        return $this;
    }

    public function findInSetOr($arr, $params)
    {
        $condition = ['or'];
        foreach ($arr as $item) {
            $condition[] = 'find_in_set(\'' . $item . '\', `' . $params . '`)';
        }
        $this->keyword(count($condition) > 1, $condition);
        return $this;
    }
}
