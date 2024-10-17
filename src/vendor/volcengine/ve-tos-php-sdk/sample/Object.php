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
use Tos\Model\CopyObjectInput;
use Tos\Model\DeleteMultiObjectsInput;
use Tos\Model\DeleteObjectInput;
use Tos\Model\Enum;
use Tos\Model\GetObjectACLInput;
use Tos\Model\GetObjectInput;
use Tos\Model\HeadObjectInput;
use Tos\Model\ListObjectsInput;
use Tos\Model\ListObjectVersionsInput;
use Tos\Model\ObjectTobeDeleted;
use Tos\Model\PutObjectACLInput;
use Tos\Model\PutObjectInput;
use Tos\Model\SetObjectMetaInput;
use Tos\TosClient;

if (is_file(__DIR__ . '/../autoload.php')) {
    require_once __DIR__ . '/../autoload.php';
}

if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);


class ObjectSample
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

    public static function putObject()
    {
        try {
            $input = new PutObjectInput(self::$bucket, self::$key, 'hello world');
            $output = self::getClient()->putObject($input);
            echo 'put object succeed, request id: ' . $output->getRequestId() . PHP_EOL;
            echo $output->getETag() . PHP_EOL;
            echo $output->getHashCrc64ecma() . PHP_EOL;
        } catch (TosClientException $ex) {
            echo 'put object failed, message: ' . $ex->getMessage() . PHP_EOL;
        } catch (TosServerException $ex) {
            echo 'put object failed, code: ' . $ex->getErrorCode() . PHP_EOL;
            echo $ex->getRequestId() . PHP_EOL;
            echo $ex->getStatusCode() . PHP_EOL;
            echo $ex->getMessage() . PHP_EOL;
        }
        echo PHP_EOL;
    }

    public static function setObjectMeta()
    {
        try {
            $input = new SetObjectMetaInput(self::$bucket, self::$key, ['aaa' => 'bbb', '中文键' => '中文值']);
            $output = self::getClient()->setObjectMeta($input);
            echo 'set object meta succeed, request id: ' . $output->getRequestId() . PHP_EOL;
        } catch (TosClientException $ex) {
            echo 'set object meta failed, message: ' . $ex->getMessage() . PHP_EOL;
        } catch (TosServerException $ex) {
            echo 'set object meta failed, code: ' . $ex->getErrorCode() . PHP_EOL;
            echo $ex->getRequestId() . PHP_EOL;
            echo $ex->getStatusCode() . PHP_EOL;
            echo $ex->getMessage() . PHP_EOL;
        }
        echo PHP_EOL;
    }

    public static function headObject()
    {
        try {
            $input = new HeadObjectInput(self::$bucket, self::$key);
            $output = self::getClient()->headObject($input);
            echo 'head object succeed, request id: ' . $output->getRequestId() . PHP_EOL;
            foreach ($output->getMeta() as $k => $v) {
                echo $k . ':' . $v . PHP_EOL;
            }
        } catch (TosClientException $ex) {
            echo 'head object failed, message: ' . $ex->getMessage() . PHP_EOL;
        } catch (TosServerException $ex) {
            echo $ex->getRequestId() . PHP_EOL;
            echo $ex->getStatusCode() . PHP_EOL;
        }
        echo PHP_EOL;
    }

    public static function getObject()
    {
        $output = null;
        try {
            $input = new GetObjectInput(self::$bucket, self::$key);
            $output = self::getClient()->getObject($input);
            echo 'get object succeed, request id: ' . $output->getRequestId() . PHP_EOL;
            echo $output->getContent()->getContents() . PHP_EOL;
        } catch (TosClientException $ex) {
            echo 'get object failed, message: ' . $ex->getMessage() . PHP_EOL;
        } catch (TosServerException $ex) {
            echo 'get object failed, code: ' . $ex->getErrorCode() . PHP_EOL;
            echo $ex->getRequestId() . PHP_EOL;
            echo $ex->getStatusCode() . PHP_EOL;
            echo $ex->getMessage() . PHP_EOL;
        } finally {
            if ($output) {
                $output->getContent()->close();
            }
        }
        echo PHP_EOL;
    }

    public static function copyObject()
    {
        try {
            $input = new CopyObjectInput(self::$bucket, self::$key . '-copied', self::$bucket, self::$key);
            $output = self::getClient()->copyObject($input);
            echo 'copy object succeed, request id: ' . $output->getRequestId() . PHP_EOL;
            echo $output->getETag() . PHP_EOL;
        } catch (TosClientException $ex) {
            echo 'copy object failed, message: ' . $ex->getMessage() . PHP_EOL;
        } catch (TosServerException $ex) {
            echo 'copy object failed, code: ' . $ex->getErrorCode() . PHP_EOL;
            echo $ex->getRequestId() . PHP_EOL;
            echo $ex->getStatusCode() . PHP_EOL;
            echo $ex->getMessage() . PHP_EOL;
        }
        echo PHP_EOL;
    }

    public static function listObjects()
    {
        try {
            $input = new ListObjectsInput(self::$bucket);
            $output = self::getClient()->listObjects($input);
            echo 'list objects succeed, request id: ' . $output->getRequestId() . PHP_EOL;
            foreach ($output->getContents() as $content) {
                echo $content->getKey() . PHP_EOL;
                echo $content->getETag() . PHP_EOL;
                echo $content->getHashCrc64ecma() . PHP_EOL;
            }
        } catch (TosClientException $ex) {
            echo 'list objects failed, message: ' . $ex->getMessage() . PHP_EOL;
        } catch (TosServerException $ex) {
            echo 'list objects failed, code: ' . $ex->getErrorCode() . PHP_EOL;
            echo $ex->getRequestId() . PHP_EOL;
            echo $ex->getStatusCode() . PHP_EOL;
            echo $ex->getMessage() . PHP_EOL;
        }
        echo PHP_EOL;
    }

    public static function listObjectVersions()
    {
        try {
            $input = new ListObjectVersionsInput(self::$bucket);
            $output = self::getClient()->listObjectVersions($input);
            echo 'list object versions succeed, request id: ' . $output->getRequestId() . PHP_EOL;
            foreach ($output->getVersions() as $version) {
                echo $version->getKey() . PHP_EOL;
                echo $version->getVersionID() . PHP_EOL;
                echo $version->getSize() . PHP_EOL;
                echo $version->getETag() . PHP_EOL;
            }

            foreach ($output->getDeleteMarkers() as $deleteMarker) {
                echo $deleteMarker->getKey() . PHP_EOL;
                echo $deleteMarker->getVersionID() . PHP_EOL;
            }
        } catch (TosClientException $ex) {
            echo 'list object versions failed, message: ' . $ex->getMessage() . PHP_EOL;
        } catch (TosServerException $ex) {
            echo 'list object versions failed, code: ' . $ex->getErrorCode() . PHP_EOL;
            echo $ex->getRequestId() . PHP_EOL;
            echo $ex->getStatusCode() . PHP_EOL;
            echo $ex->getMessage() . PHP_EOL;
        }
        echo PHP_EOL;
    }

    public static function putObjectACL()
    {
        try {
            $input = new PutObjectACLInput(self::$bucket, self::$key, Enum::ACLPublicRead);
            $output = self::getClient()->putObjectACL($input);
            echo 'put object acl succeed, request id: ' . $output->getRequestId() . PHP_EOL;
        } catch (TosClientException $ex) {
            echo 'put object acl failed, message: ' . $ex->getMessage() . PHP_EOL;
        } catch (TosServerException $ex) {
            echo 'put object acl failed, code: ' . $ex->getErrorCode() . PHP_EOL;
            echo $ex->getRequestId() . PHP_EOL;
            echo $ex->getStatusCode() . PHP_EOL;
            echo $ex->getMessage() . PHP_EOL;
        }
        echo PHP_EOL;
    }

    public static function getObjectACL()
    {
        try {
            $input = new GetObjectACLInput(self::$bucket, self::$key);
            $output = self::getClient()->getObjectACL($input);
            echo 'get object acl succeed, request id: ' . $output->getRequestId() . PHP_EOL;
            foreach ($output->getGrants() as $grant) {
                echo $grant->getGrantee()->getType() . PHP_EOL;
                echo $grant->getGrantee()->getCanned() . PHP_EOL;
                echo $grant->getGrantee()->getID() . PHP_EOL;
                echo $grant->getPermission() . PHP_EOL;
            }
        } catch (TosClientException $ex) {
            echo 'get object acl failed, message: ' . $ex->getMessage() . PHP_EOL;
        } catch (TosServerException $ex) {
            echo 'get object acl failed, code: ' . $ex->getErrorCode() . PHP_EOL;
            echo $ex->getRequestId() . PHP_EOL;
            echo $ex->getStatusCode() . PHP_EOL;
            echo $ex->getMessage() . PHP_EOL;
        }
        echo PHP_EOL;
    }

    public static function deleteObject()
    {
        try {
            $input = new DeleteObjectInput(self::$bucket, self::$key);
            $output = self::getClient()->deleteObject($input);
            echo 'delete object succeed, request id: ' . $output->getRequestId() . PHP_EOL;
        } catch (TosClientException $ex) {
            echo 'delete object failed, message: ' . $ex->getMessage() . PHP_EOL;
        } catch (TosServerException $ex) {
            echo 'delete object failed, code: ' . $ex->getErrorCode() . PHP_EOL;
            echo $ex->getRequestId() . PHP_EOL;
            echo $ex->getStatusCode() . PHP_EOL;
            echo $ex->getMessage() . PHP_EOL;
        }
        echo PHP_EOL;
    }

    public static function deleteMultiObjects()
    {
        try {
            $objects = [];
            $objects[] = new ObjectTobeDeleted(self::$key);
            $objects[] = new ObjectTobeDeleted(self::$key . '-copied');
            $input = new DeleteMultiObjectsInput(self::$bucket, $objects);
            $output = self::getClient()->deleteMultiObjects($input);
            echo 'delete multi objects succeed, request id: ' . $output->getRequestId() . PHP_EOL;
            echo 'delete succeed:' . PHP_EOL;
            foreach ($output->getDeleted() as $deleted) {
                echo $deleted->getKey() . PHP_EOL;
            }
            echo 'delete error:' . PHP_EOL;
            foreach ($output->getError() as $error) {
                echo $error->getKey() . PHP_EOL;
                echo $error->getCode() . PHP_EOL;
            }
        } catch (TosClientException $ex) {
            echo 'delete multi objects failed, message: ' . $ex->getMessage() . PHP_EOL;
        } catch (TosServerException $ex) {
            echo 'delete multi objects failed, code: ' . $ex->getErrorCode() . PHP_EOL;
            echo $ex->getRequestId() . PHP_EOL;
            echo $ex->getStatusCode() . PHP_EOL;
            echo $ex->getMessage() . PHP_EOL;
        }
        echo PHP_EOL;
    }
}


ObjectSample::putObject();
ObjectSample::setObjectMeta();
ObjectSample::headObject();
ObjectSample::getObject();
ObjectSample::copyObject();
ObjectSample::listObjects();
ObjectSample::listObjectVersions();
ObjectSample::putObjectACL();
ObjectSample::getObjectACL();
ObjectSample::deleteObject();
ObjectSample::deleteMultiObjects();
