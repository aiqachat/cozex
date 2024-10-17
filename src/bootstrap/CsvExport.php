<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\bootstrap;

use yii\base\Component;

class CsvExport extends Component
{
    /**
     * 导出excel(csv)
     * @data 导出数据
     * @headlist 第一行,列名
     * @fileName 输出Excel文件名
     */
    public function export($data = array(), $headlist = array(), $fileName)
    {
        try {
            $fileName = urlencode($fileName);

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename*=utf-8' . "'zh_cn'" . iconv('utf-8', 'gbk', $fileName) . '.csv');
            header('Cache-Control: max-age=0');

            //打开PHP文件句柄,php://output 表示直接输出到浏览器
            $fp = fopen('php://output', 'a');

            //输出Excel列名信息
            foreach ($headlist as $key => $value) {
                //CSV的Excel支持GBK编码，一定要转换，否则乱码
                $headlist[$key] = iconv('utf-8', 'gbk', $value);
            }

            //将数据通过fputcsv写到文件句柄
            fputcsv($fp, $headlist);

            //计数器
            $num = 0;

            //每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
            $limit = 100000;

            //逐行取出数据，不浪费内存
            $count = count($data);
            for ($i = 0; $i < $count; $i++) {
                $num++;

                //刷新一下输出buffer，防止由于数据过多造成问题
                if ($limit == $num) {
                    ob_flush();
                    flush();
                    $num = 0;
                }

                $row = $this->handleRowData($data[$i]);
                fputcsv($fp, $row);
            }

            exit();
        } catch (\Exception $exception) {
            \Yii::error($exception);
        }
    }

    /**
        * ！！！！！！！！！！！！！！！！！！！！！！！
     * 已废弃 不要再使用
     * ！！！！！！！！！！！！！！！！！！！！！！！
     * 同时导出多个csv文件时，需要弄成压缩包一起下载
     * @data 导出数据
     * @headlist 第一行,列名
     */
    public function exportMultiple($data = array(), $headlist = array())
    {
        try {
            // create a temporary file
            $fp = fopen('php://temp/maxmemory:1048576', 'w');
            if (false === $fp) {
                die('Failed to create temporary file');
            }

            //输出Excel列名信息
            foreach ($headlist as $key => $value) {
                //CSV的Excel支持GBK编码，一定要转换，否则乱码
                $headlist[$key] = iconv('utf-8', 'gbk', $value);
            }

            //将数据通过fputcsv写到文件句柄
            fputcsv($fp, $headlist);

            //计数器
            $num = 0;

            //每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
            $limit = 100000;

            //逐行取出数据，不浪费内存
            $count = count($data);
            for ($i = 0; $i < $count; $i++) {
                $num++;

                //刷新一下输出buffer，防止由于数据过多造成问题
                if ($limit == $num) {
                    ob_flush();
                    flush();
                    $num = 0;
                }

                $row = $this->handleRowData($data[$i]);
                fputcsv($fp, $row);
            }

            rewind($fp);
            return $fp;
        } catch (\Exception $exception) {
            \Yii::error($exception);
        }
    }

    /**
     * ！！！！！！！！！！！！！！！！！！！！！！！
     * 已废弃 不要再使用
     * ！！！！！！！！！！！！！！！！！！！！！！！
     *
     * 异步处理超大csv数据
     * @data 导出数据
     * @headlist 第一行,列名
     * @fileName 输出Excel文件名
     */
    public function ajaxExport($data = array(), $headlist = array(), $fileName, $filePath = '/web/csv/')
    {
        try {
            $fileName = $fileName . '.csv';
            $newFilePath = \Yii::$app->basePath . $filePath;

            if (!file_exists($newFilePath . $fileName)) {
                if (!is_dir($newFilePath)) {
                    mkdir($newFilePath, 0777, true);
                }
                $fp = fopen($newFilePath . $fileName, 'a+');
                //输出Excel列名信息
                foreach ($headlist as $key => $value) {
                    //CSV的Excel支持GBK编码，一定要转换，否则乱码
                    $headlist[$key] = iconv('utf-8', 'gbk', $value);
                }

                //将数据通过fputcsv写到文件句柄
                fputcsv($fp, $headlist);
            }

            $fp = fopen($newFilePath . $fileName, 'a+');

            //计数器
            $num = 0;

            //每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
            $limit = 100000;

            //逐行取出数据，不浪费内存
            $count = count($data);
            for ($i = 0; $i < $count; $i++) {
                $num++;

                //刷新一下输出buffer，防止由于数据过多造成问题
                if ($limit == $num) {
                    ob_flush();
                    flush();
                    $num = 0;
                }

                $row = $this->handleRowData($data[$i]);
                fputcsv($fp, $row);
            }

            return $fileName;
        } catch (\Exception $exception) {
            \Yii::error($exception);
        }
    }

    /**
     * 异步处理超大csv数据
     * @data 导出数据
     * @headlist 第一行,列名
     * @fileName 输出文件名
     */
    public function newAjaxExport($data = array(), $headlist = array(), $fileName)
    {
        try {
            // 检查目录是否存在
            $filePath = sprintf('%s%s', \Yii::$app->basePath, '/web/csv/');
            if (!is_dir($filePath)) {
                mkdir($filePath, 0777, true);
            }

            $fileName = sprintf('%s%s', $filePath, $fileName);
            if (!file_exists($fileName)) {
                $fp = fopen($fileName, 'a+');
                //输出Excel列名信息
                foreach ($headlist as $key => $value) {
                    //CSV的Excel支持GBK编码，一定要转换，否则乱码
                    $headlist[$key] = iconv('utf-8', 'gbk', $value);
                }
                //将数据通过fputcsv写到文件句柄
                fputcsv($fp, $headlist);
                fclose($fp);
            }
            $fp = fopen($fileName, 'a+');
            flock($fp,LOCK_EX);

            //计数器
            $num = 0;

            //每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
            $limit = 100000;

            //逐行取出数据，不浪费内存
            $count = count($data);
            for ($i = 0; $i < $count; $i++) {
                $num++;

                //刷新一下输出buffer，防止由于数据过多造成问题
                if ($limit == $num) {
                    ob_flush();
                    flush();
                    $num = 0;
                }

                $row = $this->handleRowData($data[$i]);
                fputcsv($fp, $row);
            }
            fclose($fp);

            return $fileName;
        } catch (\Exception $exception) {
            \Yii::error($exception);
        }
    }

    //判断是否含有英文逗号，英文引号
    public function check($value)
    {
        if (!$this->checkJson($value)) {
            $isInt = is_int($value) ? true : false;
            $isFloat = is_float($value) ? true : false;

            $value = rtrim($value, "\r\n");
            $value = str_replace(",", "，", $value);

            $value = is_string($value) ? $value . "\t" : $value;
            $value = $isInt ? intval($value) : $value;
            $value = $isFloat ? floatval($value) : $value;
        }
        return $value;
    }

    public function handleRowData($data)
    {
        $row = $data;
        foreach ($row as $key => $value) {
            $value = $this->check($value);
            $row[$key] = mb_convert_encoding($value, 'GBK', 'UTF-8');
        }
        return $row;
    }

    // 判断是否为json数据
    private function checkJson($value)
    {
        $data = json_decode($value);
        if ($data && (is_object($data)) || is_array($data)) {
            return true;
        }

        return false;
    }

    //下载批量发货模板
    public function downloadBatchSend($out_filename)
    {
        $file_url = \Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'statics' . DIRECTORY_SEPARATOR . 'text' . DIRECTORY_SEPARATOR . 'batch_send_default.xlsx';

        header('Accept-Ranges: bytes');
        header('Accept-Length: ' . filesize($file_url));
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: max-age=0');
        header('Content-Disposition: attachment; filename=' . $out_filename);
        header('Content-Type: application/vnd.ms-excel; name=' . $out_filename);
        if (is_file($file_url) && is_readable($file_url)) {
            $file = fopen($file_url, "r");
            echo fread($file, filesize($file_url));
            fclose($file);
        }
    }
}
