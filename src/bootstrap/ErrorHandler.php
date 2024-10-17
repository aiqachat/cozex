<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/10/30 12:00
 */

namespace app\bootstrap;

use yii\web\Response;

class ErrorHandler extends \yii\web\ErrorHandler
{
    public function init()
    {
        parent::init();
        if (!YII_DEBUG) {
            $this->exceptionView = '@app/views/error/exception.php';
            $this->errorView = '@app/views/error/error.php';
        }
    }

    /**
     * @param \Exception $exception
     * @return array
     */
    private function formatException($exception)
    {
        $title = $exception->getMessage();
        $title = str_replace('\\\\', '\\', $title);
        $title = str_replace(\Yii::$app->basePath, '', $title); // 把路径去掉 @czs
        $file = $exception->getFile();
        $file = str_replace(\Yii::$app->basePath, '', $file); // 把路径去掉 @czs
        $file = str_replace('\\', '/', $file);
        $line = $exception->getLine();
        $list = $exception->getTrace();
        $newList = [
            "#{$line}: {$file}",
        ];
        foreach ($list as $i => $item) {
            if ($i === 0) {
                continue;
            }
            if (isset($item['file'])) {
                $file = $item['file'];
                $file = str_replace(\Yii::$app->basePath, '', $file); // 把路径去掉 @czs
                $file = str_replace('\\', '/', $file);
                $newList[] = "#{$item['line']}: {$file}";
            } elseif (isset($item['class'])) {
                $class = $item['class'];
                $class = str_replace(\Yii::$app->basePath, '', $class); // 把路径去掉 @czs
                $class = str_replace('\\', '\\', $class);
                $newList[] = "#0: {$class}->{$item['function']}()";
            }
        }
        return [
            'title' => $title,
            'list' => $newList,
        ];
    }

    public function handleException($exception)
    {
        $this->exception = $exception;

        // 记录日志
        $errors = $this->getResult();
        $log = new ExceptionLog();
        $log->error($errors['title'], $errors['list']);

        if (YII_DEBUG) {
            return parent::handleException($exception);
        } else {
            if (\Yii::$app->has('response')) {
                $response = \Yii::$app->getResponse();
            } else {
                $response = new Response();
            }
            if (\Yii::$app->request->isAjax) {
                $result = $this->getResult();
                $response->data = [
                    'code' => 500,
                    'msg' => $result['title'],
                    'data' => null,
                    'error' => $result['list'],
                ];
            } else {
                $response->data = $this->renderFile($this->exceptionView, [
                    'handler' => $this,
                ]);
            }
            $response->send();
        }
    }

    public function handleError($code, $message, $file, $line)
    {
        return parent::handleError($code, $message, $file, $line);
    }

    public function getResult()
    {
        return $this->formatException($this->exception);
    }

    /**
     * Renders call stack.
     * @param \Exception|\ParseError $exception exception to get call stack from
     * @return string HTML content of the rendered call stack.
     * @since 2.0.12
     */
    public function renderCallStack($exception)
    {
        $out = '<ul>';
        $out .= $this->renderCallStackItem($exception->getFile(), $exception->getLine(), null, null, [], 1);
        for ($i = 0, $trace = $exception->getTrace(), $length = count($trace); $i < $length; ++$i) {
            $file = !empty($trace[$i]['file']) ? $trace[$i]['file'] : null;
            $line = !empty($trace[$i]['line']) ? $trace[$i]['line'] : null;
            $class = !empty($trace[$i]['class']) ? $trace[$i]['class'] : null;
            $function = null;
            if (!empty($trace[$i]['function']) && $trace[$i]['function'] !== 'unknown') {
                $function = $trace[$i]['function'];
            }
            // 不输出函数方法参数内容
            // $args = !empty($trace[$i]['args']) ? $trace[$i]['args'] : [];
            $args = [];
            $out .= $this->renderCallStackItem($file, $line, $class, $function, $args, $i + 2);
        }
        $out .= '</ul>';
        return $out;
    }
}
