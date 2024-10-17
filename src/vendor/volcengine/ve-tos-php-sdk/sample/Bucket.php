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

use Tos\Exception\TosClientException;
use Tos\Exception\TosServerException;
use Tos\Model\CreateBucketInput;
use Tos\Model\DeleteBucketInput;
use Tos\Model\HeadBucketInput;
use Tos\TosClient;

if (is_file(__DIR__ . '/../autoload.php')) {
    require_once __DIR__ . '/../autoload.php';
}

if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);


class BucketSample
{

    protected static $region = 'your region';
    protected static $endpoint = 'your endpoint';
    protected static $ak = 'your access key';
    protected static $sk = 'your secret key';
    protected static $bucket = 'bucket-test';
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

    public static function createBucket()
    {
        try {
            $input = new CreateBucketInput(self::$bucket);
            $output = self::getClient()->createBucket($input);
            echo 'create bucket succeed, request id: ' . $output->getRequestId() . PHP_EOL;
            echo $output->getLocation() . PHP_EOL;
        } catch (TosClientException $ex) {
            echo 'create bucket failed, message: ' . $ex->getMessage() . PHP_EOL;
        } catch (TosServerException $ex) {
            echo 'create bucket failed, code: ' . $ex->getErrorCode() . PHP_EOL;
            echo $ex->getRequestId() . PHP_EOL;
            echo $ex->getStatusCode() . PHP_EOL;
            echo $ex->getMessage() . PHP_EOL;
        }
        echo PHP_EOL;
    }

    public static function headBucket()
    {
        try {
            $input = new HeadBucketInput(self::$bucket);
            $output = self::getClient()->headBucket($input);
            echo 'head bucket succeed, request id: ' . $output->getRequestId() . PHP_EOL;
            echo $output->getRegion() . PHP_EOL;
            echo $output->getStorageClass() . PHP_EOL;
        } catch (TosClientException $ex) {
            echo 'head bucket failed, message: ' . $ex->getMessage() . PHP_EOL;
        } catch (TosServerException $ex) {
            echo $ex->getRequestId() . PHP_EOL;
            echo $ex->getStatusCode() . PHP_EOL;
        }
        echo PHP_EOL;
    }

    public static function listBuckets()
    {
        try {
            $output = self::getClient()->listBuckets();
            echo 'list buckets succeed, request id: ' . $output->getRequestId() . PHP_EOL;
            foreach ($output->getBuckets() as $bucket) {
                echo $bucket->getName() . PHP_EOL;
            }
        } catch (TosClientException $ex) {
            echo 'list bucket failed, message: ' . $ex->getMessage() . PHP_EOL;
        } catch (TosServerException $ex) {
            echo 'list bucket failed, code: ' . $ex->getErrorCode() . PHP_EOL;
            echo $ex->getRequestId() . PHP_EOL;
            echo $ex->getStatusCode() . PHP_EOL;
            echo $ex->getMessage() . PHP_EOL;
        }
        echo PHP_EOL;
    }

    public static function deleteBucket()
    {
        try {
            $input = new DeleteBucketInput(self::$bucket);
            $output = self::getClient()->deleteBucket($input);
            echo 'delete bucket succeed, request id: ' . $output->getRequestId() . PHP_EOL;
        } catch (TosClientException $ex) {
            echo 'delete bucket failed, message: ' . $ex->getMessage() . PHP_EOL;
        } catch (TosServerException $ex) {
            echo 'delete bucket failed, code: ' . $ex->getErrorCode() . PHP_EOL;
            echo $ex->getRequestId() . PHP_EOL;
            echo $ex->getStatusCode() . PHP_EOL;
            echo $ex->getMessage() . PHP_EOL;
        }
        echo PHP_EOL;
    }
}

BucketSample::createBucket();
BucketSample::headBucket();
BucketSample::listBuckets();
BucketSample::deleteBucket();