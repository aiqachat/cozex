<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\bootstrap;

use app\models\CoreExceptionLog;

class ExceptionLog
{
    /**
     * 异常等级
     */
    const LEVEL_ERROR = 1;// 错误
    const LEVEL_WARNING = 2;// 警告
    const LEVEL_INFO = 3;// 记录信息

    public static function index($page)
    {
        $query = CoreExceptionLog::find();

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => 20, 'page' => $page - 1]);

        $list = $query->asArray()->all();

        return [
            'list' => $list,
            'pagination' => $pagination
        ];
    }

    public function detail($id)
    {
        return CoreExceptionLog::findOne($id);
    }

    /**
     * 创建异常日志
     * @param $title
     * @param array $content
     * @param $level
     * @return bool
     */
    public function create($title, array $content, $level)
    {
        try {
            $log = new CoreExceptionLog();
            $log->level = $level;
            $log->title = $title;
            $log->content = \Yii::$app->serializer->encode($content);
            $res = $log->save();
            \Yii::warning('异常日志记录是否存储成功:' . $res);
            return $res;

        } catch (\Exception $e) {
            \Yii::error($e->getMessage());
            return false;
        }
    }

    public function error($title, array $content)
    {
        return $this->create($title, $content, self::LEVEL_ERROR);
    }

    public function warning($title, array $content)
    {
        return $this->create($title, $content, self::LEVEL_WARNING);
    }

    public function info($title, array $content)
    {
        return $this->create($title, $content, self::LEVEL_INFO);
    }


    /**
     * 删除日志
     * @param $id
     * @return mixed
     */
    public static function delete($id)
    {
        $log = CoreExceptionLog::findOne($id);

        if ($log) {
            $log->is_delete = 1;

            return $log->save();
        }

        return false;
    }
}
