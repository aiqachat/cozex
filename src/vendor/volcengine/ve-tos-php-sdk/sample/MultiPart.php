<?php
/**
 * Copyright (2022) Volcengine
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use GuzzleHttp\Psr7\Utils;
use Tos\Exception\TosClientException;
use Tos\Exception\TosServerException;
use Tos\Model\AbortMultipartUploadInput;
use Tos\Model\CompleteMultipartUploadInput;
use Tos\Model\CreateMultipartUploadInput;
use Tos\Model\GetObjectInput;
use Tos\Model\ListMultipartUploadsInput;
use Tos\Model\ListPartsInput;
use Tos\Model\UploadedPart;
use Tos\Model\UploadPartInput;
use Tos\TosClient;

if (is_file(__DIR__ . '/../autoload.php')) {
    require_once __DIR__ . '/../autoload.php';
}

if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);


class MultiPartSample
{

    protected static $region = 'your region';
    protected static $endpoint = 'your endpoint';
    protected static $ak = 'your access key';
    protected static $sk = 'your secret key';
    protected static $bucket = 'bucket-test';
    protected static $key = 'key-test';
    protected static $client;

    public static function getClient()
    {
        if (!self::$client) {
            self::$client = new TosClient([
                'region' => self::$region,
                'ak' => self::$ak,
                'sk' => self::$sk,
                'endpoint' => self::$endpoint,
            ]);
        }
        return self::$client;
    }

    public static function createAndAbort()
    {
        $output = null;
        try {
            $input = new CreateMultipartUploadInput(self::$bucket, self::$key);
            $output = self::getClient()->createMultipartUpload($input);
            echo 'create multipart succeed, request id: ' . $output->getRequestId() . PHP_EOL;
            echo $output->getUploadID() . PHP_EOL;
        } catch (TosClientException $ex) {
            echo 'create multipart failed, message: ' . $ex->getMessage() . PHP_EOL;
        } catch (TosServerException $ex) {
            echo 'create multipart failed, code: ' . $ex->getErrorCode() . PHP_EOL;
            echo $ex->getRequestId() . PHP_EOL;
            echo $ex->getStatusCode() . PHP_EOL;
            echo $ex->getMessage() . PHP_EOL;
        }
        echo PHP_EOL;

        if ($output) {
            try {
                $input = new AbortMultipartUploadInput(self::$bucket, self::$key, $output->getUploadID());
                $output = self::getClient()->abortMultipartUpload($input);
                echo 'abort multipart succeed, request id: ' . $output->getRequestId() . PHP_EOL;
            } catch (TosClientException $ex) {
                echo 'abort multipart failed, message: ' . $ex->getMessage() . PHP_EOL;
            } catch (TosServerException $ex) {
                echo 'abort multipart failed, code: ' . $ex->getErrorCode() . PHP_EOL;
                echo $ex->getRequestId() . PHP_EOL;
                echo $ex->getStatusCode() . PHP_EOL;
                echo $ex->getMessage() . PHP_EOL;
            }
            echo PHP_EOL;
        }
    }

    public static function multiPartUpload()
    {
        $uploadId = '';
        try {
            $input = new CreateMultipartUploadInput(self::$bucket, self::$key);
            $output = self::getClient()->createMultipartUpload($input);
            echo 'create multipart succeed, request id: ' . $output->getRequestId() . PHP_EOL;
            echo ($uploadId = $output->getUploadID()) . PHP_EOL;
        } catch (TosClientException $ex) {
            echo 'create multipart failed, message: ' . $ex->getMessage() . PHP_EOL;
        } catch (TosServerException $ex) {
            echo 'create multipart failed, code: ' . $ex->getErrorCode() . PHP_EOL;
            echo $ex->getRequestId() . PHP_EOL;
            echo $ex->getStatusCode() . PHP_EOL;
            echo $ex->getMessage() . PHP_EOL;
        }
        echo PHP_EOL;

        if (!$uploadId) {
            return;
        }

        $sampleFilePath = './temp/test.txt'; //sample large file path
        //  you can prepare a large file in you filesystem first
        self::createSampleFile($sampleFilePath);


        $firstPartSize = 5 * 1024 * 1024;
        $partNumber1 = 1;
        $etag1 = '';
        $file = null;
        try {
            $file = fopen($sampleFilePath, 'r');
            $input = new UploadPartInput(self::$bucket, self::$key, $uploadId, $partNumber1);
            $input->setContentLength($firstPartSize);
            $input->setContent($file);
            $output = self::getClient()->uploadPart($input);
            echo 'upload part 1 succeed, request id: ' . $output->getRequestId() . PHP_EOL;
            echo ($etag1 = $output->getETag()) . PHP_EOL;
        } catch (TosClientException $ex) {
            echo 'upload part 1 failed, message: ' . $ex->getMessage() . PHP_EOL;
        } catch (TosServerException $ex) {
            echo 'upload part 1 failed, code: ' . $ex->getErrorCode() . PHP_EOL;
            echo $ex->getRequestId() . PHP_EOL;
            echo $ex->getStatusCode() . PHP_EOL;
            echo $ex->getMessage() . PHP_EOL;
        } finally {
            if (is_resource($file)) {
                fclose($file);
            }
        }
        echo PHP_EOL;

        if (!$etag1) {
            return;
        }

        $parts = [];
        $parts[] = new UploadedPart($partNumber1, $etag1);

        $partNumber2 = 2;
        $etag2 = '';
        $file = null;
        try {
            $file = fopen($sampleFilePath, 'r');
            fseek($file, $firstPartSize, 0);
            $input = new UploadPartInput(self::$bucket, self::$key, $uploadId, $partNumber2);
            $input->setContentLength(filesize($sampleFilePath) - $firstPartSize);
            $input->setContent($file);
            $output = self::getClient()->uploadPart($input);
            echo 'upload part 2 succeed, request id: ' . $output->getRequestId() . PHP_EOL;
            echo ($etag2 = $output->getETag()) . PHP_EOL;
        } catch (TosClientException $ex) {
            echo 'upload part 2 failed, message: ' . $ex->getMessage() . PHP_EOL;
        } catch (TosServerException $ex) {
            echo 'upload part 2 failed, code: ' . $ex->getErrorCode() . PHP_EOL;
            echo $ex->getRequestId() . PHP_EOL;
            echo $ex->getStatusCode() . PHP_EOL;
            echo $ex->getMessage() . PHP_EOL;
        } finally {
            if (is_resource($file)) {
                fclose($file);
            }
        }
        echo PHP_EOL;
        if (!$etag2) {
            return;
        }

        $parts[] = new UploadedPart($partNumber2, $etag2);

        try {
            $input = new ListMultipartUploadsInput(self::$bucket);
            $output = self::getClient()->listMultipartUploads($input);
            echo 'list multipart uploads succeed, request id: ' . $output->getRequestId() . PHP_EOL;
            foreach ($output->getUploads() as $upload) {
                echo $upload->getKey() . ':' . $upload->getUploadID() . PHP_EOL;
            }
        } catch (TosClientException $ex) {
            echo 'list multipart uploads failed, message: ' . $ex->getMessage() . PHP_EOL;
        } catch (TosServerException $ex) {
            echo 'list multipart uploads failed, code: ' . $ex->getErrorCode() . PHP_EOL;
            echo $ex->getRequestId() . PHP_EOL;
            echo $ex->getStatusCode() . PHP_EOL;
            echo $ex->getMessage() . PHP_EOL;
        }
        echo PHP_EOL;

        try {
            $input = new ListPartsInput(self::$bucket, self::$key, $uploadId);
            $output = self::getClient()->listParts($input);
            echo 'list parts succeed, request id: ' . $output->getRequestId() . PHP_EOL;
            foreach ($output->getParts() as $part) {
                echo $part->getPartNumber() . ':' . $part->getETag() . PHP_EOL;
            }
        } catch (TosClientException $ex) {
            echo 'list parts failed, message: ' . $ex->getMessage() . PHP_EOL;
        } catch (TosServerException $ex) {
            echo 'list parts failed, code: ' . $ex->getErrorCode() . PHP_EOL;
            echo $ex->getRequestId() . PHP_EOL;
            echo $ex->getStatusCode() . PHP_EOL;
            echo $ex->getMessage() . PHP_EOL;
        }
        echo PHP_EOL;

        try {
            $input = new CompleteMultipartUploadInput(self::$bucket, self::$key, $uploadId, $parts);
            $output = self::getClient()->completeMultipartUpload($input);
            echo 'complete multipart upload succeed, request id: ' . $output->getRequestId() . PHP_EOL;
        } catch (TosClientException $ex) {
            echo 'complete multipart upload failed, message: ' . $ex->getMessage() . PHP_EOL;
        } catch (TosServerException $ex) {
            echo 'complete multipart upload failed, code: ' . $ex->getErrorCode() . PHP_EOL;
            echo $ex->getRequestId() . PHP_EOL;
            echo $ex->getStatusCode() . PHP_EOL;
            echo $ex->getMessage() . PHP_EOL;
        }
        echo PHP_EOL;

        try {
            $input = new GetObjectInput(self::$bucket, self::$key);
            $output = self::getClient()->getObject($input);
            echo 'get object succeed, request id: ' . $output->getRequestId() . PHP_EOL;

            $dstFilePath = $sampleFilePath . '_bak';
            $file = fopen($dstFilePath, 'w');
            Utils::copyToStream(Utils::streamFor($output->getContent()), Utils::streamFor($file));
            $source = md5_file($sampleFilePath);
            $dst = md5_file($dstFilePath);
            if ($source == $dst) {
                echo 'compare md5 succeed' . PHP_EOL;
            } else {
                echo 'compare md5 failed, expect: ' . $source . ', actual: ' . $dst;
            }
            if (file_exists($sampleFilePath)) {
                unlink($sampleFilePath);
            }
            if (file_exists($dstFilePath)) {
                unlink($dstFilePath);
            }
        } catch (TosClientException $ex) {
            echo 'get object failed, message: ' . $ex->getMessage() . PHP_EOL;
        } catch (TosServerException $ex) {
            echo 'get object failed, code: ' . $ex->getErrorCode() . PHP_EOL;
            echo $ex->getRequestId() . PHP_EOL;
            echo $ex->getStatusCode() . PHP_EOL;
            echo $ex->getMessage() . PHP_EOL;
        } finally {
            if (is_resource($file)) {
                fclose($file);
            }
        }
        echo PHP_EOL;
    }

    public static function createSampleFile($filePath)
    {
        if (file_exists($filePath)) {
            return;
        }
        $filePath = iconv('UTF-8', 'GBK', $filePath);
        if (is_string($filePath) && $filePath !== '') {
            $fp = null;
            $dir = dirname($filePath);
            try {
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                if (($fp = fopen($filePath, 'w'))) {
                    for ($i = 0; $i < 500000; $i++) {
                        fwrite($fp, uniqid() . "\n");
                        fwrite($fp, uniqid() . "\n");
                        if ($i % 100 === 0) {
                            fflush($fp);
                        }
                    }
                }
            } finally {
                if ($fp) {
                    fclose($fp);
                }
            }
        }
    }
}

MultiPartSample::createAndAbort();
MultiPartSample::multiPartUpload();

