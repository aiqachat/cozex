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

namespace Tos\Model;

use Tos\Config\ConfigParser;
use Tos\Exception\TosClientException;
use Tos\Helper\Helper;

trait InputTranslator
{
    use EnumChecker;

    protected static function &transCreateBucketInput(CreateBucketInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        $headers = [];
        self::dealAcl($input, $headers);

        if (($storageClass = $input->getStorageClass()) && self::checkStorageClass($storageClass)) {
            $headers[Constant::HeaderStorageClass] = $storageClass;
        }

        if (($azRedundancy = $input->getAzRedundancy()) && self::checkAzRedundancy($azRedundancy)) {
            $headers[Constant::HeaderAzRedundancy] = $azRedundancy;
        }

        $request = new HttpRequest();
        $request->operation = 'createBucket';
        $request->method = Enum::HttpMethodPut;
        $request->bucket = $bucket;
        $request->headers = $headers;
        return $request;
    }

    protected static function &transHeadBucketInput(HeadBucketInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        $request = new HttpRequest();
        $request->operation = 'headBucket';
        $request->method = Enum::HttpMethodHead;
        $request->bucket = $bucket;
        return $request;
    }

    protected static function &transDeleteBucketInput(DeleteBucketInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        $request = new HttpRequest();
        $request->operation = 'deleteBucket';
        $request->method = Enum::HttpMethodDelete;
        $request->bucket = $bucket;
        return $request;
    }

    protected static function &transListBucketsInput(ListBucketsInput &$input, ConfigParser &$cp)
    {
        $request = new HttpRequest();
        $request->operation = 'listBuckets';
        $request->method = Enum::HttpMethodGet;
        return $request;
    }

    protected static function &transCopyObjectInput(CopyObjectInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        $key = self::checkKey($input->getKey());

        $headers = [];
        $srcBucket = self::checkBucket($input->getSrcBucket());
        $srcKey = self::checkKey($input->getSrcKey());
        $copySource = '/' . $srcBucket . '/' . Helper::urlencodeWithSafe($srcKey);
        if ($srcVersionId = $input->getSrcVersionID()) {
            $copySource .= '?versionId=' . $srcVersionId;
        }
        $headers[Constant:: HeaderCopySource] = $copySource;

        if (($metadataDirective = $input->getMetadataDirective()) && self::checkMetadataDirective($metadataDirective)) {
            $headers[Constant::HeaderMetadataDirective] = $metadataDirective;
        }

        self::dealIfCondition($input, $headers, true);
        self::dealHttpBasicHeader($input, $headers);
        self::dealSse($input, $headers, true, true);
        self::dealAcl($input, $headers, false);
        self::dealMeta($input, $headers);
        self::dealScAndWrl($input, $headers);
        self::dealTagging($input, $headers, true);

        $request = new HttpRequest();
        $request->operation = 'copyObject';
        $request->method = Enum::HttpMethodPut;
        $request->bucket = $bucket;
        $request->key = $key;
        $request->headers = $headers;
        return $request;
    }

    protected static function &transPutObjectInput(PutObjectInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        $key = self::checkKey($input->getKey());
        $headers = [];

        self::dealContentLengthAndContentMD5($input, $headers);

        if ($contentSHA256 = $input->getContentSHA256()) {
            $headers[Constant::HeaderContentSHA256] = $contentSHA256;
        }

        self::dealHttpBasicHeader($input, $headers);
        self::dealSse($input, $headers);
        self::dealAcl($input, $headers, false);
        self::dealMeta($input, $headers);
        self::dealScAndWrl($input, $headers);
        self::dealTagging($input, $headers);

        $request = new HttpRequest();
        $request->operation = 'putObject';
        $request->method = Enum::HttpMethodPut;
        $request->bucket = $bucket;
        $request->key = $key;
        $request->headers = $headers;
        $request->body = $input->getContent();
        return $request;
    }

    protected static function &transPutObjectFromFileInput(PutObjectFromFileInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        $key = self::checkKey($input->getKey());

        $filePath = trim($input->getFilePath());

        if (!$filePath) {
            throw new TosClientException('empty file path');
        }

        if (!file_exists($filePath)) {
            throw new TosClientException('the specified file path does not exist');
        }

        if (is_dir($filePath)) {
            throw new TosClientException('the specified file path is a dir');
        }

        $headers = [];

        self::dealContentLengthAndContentMD5($input, $headers);
        if (!isset($headers[Constant::HeaderContentLength])) {
            $headers[Constant::HeaderContentLength] = filesize($filePath);
        }

        if ($contentSHA256 = $input->getContentSHA256()) {
            $headers[Constant::HeaderContentSHA256] = $contentSHA256;
        }
        self::dealHttpBasicHeader($input, $headers);
        self::dealSse($input, $headers);
        self::dealAcl($input, $headers, false);
        self::dealMeta($input, $headers);
        self::dealScAndWrl($input, $headers);
        self::dealTagging($input, $headers);

        $request = new HttpRequest();
        $request->operation = 'putObjectFromFile';
        $request->method = Enum::HttpMethodPut;
        $request->bucket = $bucket;
        $request->key = $key;
        $request->headers = $headers;
        $request->body = fopen($filePath, 'r');
        return $request;
    }

    protected static function &transGetObjectInput(GetObjectInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        $key = self::checkKey($input->getKey());
        $headers = [];
        $queries = [];

        self::dealIfCondition($input, $headers);
        self::dealSse($input, $headers, false);

        if ($range = trim(strval($input->getRange()))) {
            if (strpos($range, 'bytes=') !== 0) {
                throw new TosClientException('invalid range format');
            }

            $headers[Constant::HeaderRange] = $range;
        } else {
            $rangeStart = $input->getRangeStart();
            $rangeEnd = $input->getRangeEnd();
            if (isset($rangeStart) && isset($rangeEnd) && (($rangeStart = intval($rangeStart)) >= 0) && ($rangeStart <= ($rangeEnd = (intval($rangeEnd))))) {
                $headers[Constant::HeaderRange] = 'bytes=' . $rangeStart . '-' . $rangeEnd;
            }
        }

        if ($versionId = $input->getVersionID()) {
            $queries[Constant::QueryVersionId] = $versionId;
        }

        if ($responseCacheControl = $input->getResponseCacheControl()) {
            $queries[Constant::QueryResponseCacheControl] = $responseCacheControl;
        }

        if ($responseContentDisposition = $input->getResponseContentDisposition()) {
            $queries[Constant::QueryResponseContentDisposition] = Helper::urlEncodeChinese($responseContentDisposition);
        }

        if ($responseContentEncoding = $input->getResponseContentEncoding()) {
            $queries[Constant::QueryResponseContentEncoding] = Helper::urlEncodeChinese($responseContentEncoding);
        }

        if ($responseContentLanguage = $input->getResponseContentLanguage()) {
            $queries[Constant::QueryResponseContentLanguage] = $responseContentLanguage;
        }

        if ($responseContentType = $input->getResponseContentType()) {
            $queries[Constant::QueryResponseContentType] = $responseContentType;
        }

        if (($responseExpires = intval($input->getResponseExpires())) && $responseExpires > 0) {
            $queries[Constant::QueryResponseExpires] = gmdate('D, d M Y H:i:s \G\M\T', $responseExpires);
        }

        if ($process = $input->getProcess()) {
            $queries[Constant::QueryProcess] = $process;
        }

        if ($saveBucket = $input->getSaveBucket()) {
            $queries[Constant::QuerySaveBucket] = $saveBucket;
        }

        if ($saveObject = $input->getSaveObject()) {
            $queries[Constant::QuerySaveObject] = $saveObject;
        }

        $request = new HttpRequest();
        $request->operation = 'getObject';
        $request->method = Enum::HttpMethodGet;
        $request->bucket = $bucket;
        $request->key = $key;
        $request->headers = $headers;
        $request->queries = $queries;
        return $request;
    }

    protected static function &transGetObjectToFileInput(GetObjectToFileInput &$input, ConfigParser &$cp)
    {
        $filePath = trim($input->getFilePath());
        if (!$filePath) {
            throw new TosClientException('empty file path');
        }

        $dir = dirname($filePath);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $doMkdir = false;
        $key = self::checkKey($input->getKey());
        if (file_exists($filePath)) {
            if (is_dir($filePath)) {
                if ($key[strlen($key) - 1] === '/') {
                    // mkdir
                    $doMkdir = true;
                }
                $filePath .= DIRECTORY_SEPARATOR . $key;
            }
        } else if ($filePath[strlen($filePath) - 1] === '/' || $filePath[strlen($filePath) - 1] === DIRECTORY_SEPARATOR) {
            mkdir($filePath, 0755, true);
            if ($key[strlen($key) - 1] === '/') {
                // mkdir
                $doMkdir = true;
            }
            $filePath .= $key;
        }

        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        if ($doMkdir) {
            $result = [self::transHeadObjectInput($input, $cp), $filePath, $doMkdir, $bucket, $key];
        } else {
            $result = [self::transGetObjectInput($input, $cp), $filePath, $doMkdir, $bucket, $key];
        }
        return $result;
    }

    protected static function &transDeleteObjectInput(DeleteObjectInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        $key = self::checkKey($input->getKey());
        $queries = [];
        if ($versionId = $input->getVersionID()) {
            $queries[Constant::QueryVersionId] = $versionId;
        }

        $request = new HttpRequest();
        $request->operation = 'deleteObject';
        $request->method = Enum::HttpMethodDelete;
        $request->bucket = $bucket;
        $request->key = $key;
        $request->queries = $queries;
        return $request;
    }

    protected static function &transDeleteMultiObjectsInput(DeleteMultiObjectsInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        if (!is_array($input->getObjects()) || count($input->getObjects()) === 0) {
            throw new TosClientException('empty objects');
        }

        $body = ['Objects' => [], 'Quiet' => boolval($input->isQuiet())];
        foreach ($input->getObjects() as $item) {
            if ($key = $item->getKey()) {
                $obj = ['Key' => $key];
                if ($versionId = $item->getVersionID()) {
                    $obj['VersionId'] = $versionId;
                }
                $body['Objects'][] = $obj;
            }
        }

        if (count($body['Objects']) === 0) {
            throw new TosClientException('empty objects');
        }

        $contents = json_encode($body);
        if (!$contents || json_last_error() !== 0) {
            throw new TosClientException(sprintf('unable to do serialization/deserialization, %s', json_last_error_msg()));
        }

        $headers = [Constant::HeaderContentMD5 => base64_encode(md5($contents, true))];
        $queries = ['delete' => ''];

        $request = new HttpRequest();
        $request->operation = 'deleteMultiObjects';
        $request->method = Enum::HttpMethodPost;
        $request->bucket = $bucket;
        $request->headers = $headers;
        $request->queries = $queries;
        $request->body = $contents;
        return $request;
    }

    protected static function &transGetObjectACLInput(GetObjectACLInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        $key = self::checkKey($input->getKey());

        $queries = ['acl' => ''];
        if ($versionId = $input->getVersionID()) {
            $queries[Constant::QueryVersionId] = $versionId;
        }

        $request = new HttpRequest();
        $request->operation = 'getObjectACL';
        $request->method = Enum::HttpMethodGet;
        $request->bucket = $bucket;
        $request->key = $key;
        $request->queries = $queries;
        return $request;
    }

    protected static function &transHeadObjectInput(&$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        $key = self::checkKey($input->getKey());

        $headers = [];
        self::dealIfCondition($input, $headers);
        self::dealSse($input, $headers, false);

        $queries = [];
        if ($versionId = $input->getVersionID()) {
            $queries[Constant::QueryVersionId] = $versionId;
        }

        $request = new HttpRequest();
        $request->operation = 'headObject';
        $request->method = Enum::HttpMethodHead;
        $request->bucket = $bucket;
        $request->key = $key;
        $request->headers = $headers;
        $request->queries = $queries;
        return $request;
    }

    protected static function &transAppendObjectInput(AppendObjectInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        $key = self::checkKey($input->getKey());
        if (!$input->getContent()) {
            throw new TosClientException('empty content');
        }

        $headers = [];

        $contentLength = $input->getContentLength();
        if ($input->getContent() && isset($contentLength) && ($contentLength = intval($contentLength)) >= 0) {
            $headers[Constant::HeaderContentLength] = $contentLength;
        }

        self::dealHttpBasicHeader($input, $headers);
        self::dealAcl($input, $headers, false);
        self::dealMeta($input, $headers);
        self::dealScAndWrl($input, $headers);

        $offset = intval($input->getOffset());
        if ($offset < 0) {
            throw new TosClientException('invalid offset');
        }

        $queries = ['append' => '', 'offset' => $offset];
        $request = new HttpRequest();
        $request->operation = 'appendObject';
        $request->method = Enum::HttpMethodPost;
        $request->bucket = $bucket;
        $request->key = $key;
        $request->headers = $headers;
        $request->queries = $queries;
        $request->body = $input->getContent();
        return $request;
    }

    protected static function &transListObjectsInput(ListObjectsInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());

        $queries = [];
        self::dealListParams($input, $queries);

        $request = new HttpRequest();
        $request->operation = 'listObjects';
        $request->method = Enum::HttpMethodGet;
        $request->bucket = $bucket;
        $request->queries = $queries;
        return $request;
    }

    protected static function &transListObjectVersionsInput(ListObjectVersionsInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());

        $queries = ['versions' => ''];
        self::dealListParams($input, $queries, 1);
        $request = new HttpRequest();
        $request->operation = 'listObjectVersions';
        $request->method = Enum::HttpMethodGet;
        $request->bucket = $bucket;
        $request->queries = $queries;
        return $request;
    }

    protected static function &transPutObjectACLInput(PutObjectACLInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        $key = self::checkKey($input->getKey());

        $queries = ['acl' => ''];
        if ($versionId = $input->getVersionID()) {
            $queries[Constant::QueryVersionId] = $versionId;
        }

        $request = new HttpRequest();

        if (($acl = $input->getACL()) && $input->getGrants()) {
            throw new TosClientException('both acl and grants are set');
        } else if ($acl) {
            $headers = [];
            self::dealAcl($input, $headers, false);
            $request->headers = $headers;
        } else if ($grants = $input->getGrants()) {
            if (!is_array($grants) || count($grants) === 0) {
                throw new TosClientException('empty grants');
            }
            if (!($owner = $input->getOwner()) || !($id = $owner->getID())) {
                throw new TosClientException('empty owner id');
            }

            $body = [];
            $body['Owner'] = ['ID' => strval($id)];

            $body['Grants'] = [];
            foreach ($input->getGrants() as $grant) {
                self::checkPermission($grant->getPermission());
                $grantee = $grant->getGrantee();
                $body['Grants'] [] = ['Permission' => $grant->getPermission(), 'Grantee' => self::transGranteeToArray($grantee)];
            }

            $contents = json_encode($body);
            if (!$contents || json_last_error() !== 0) {
                throw new TosClientException(sprintf('unable to do serialization/deserialization, %s', json_last_error_msg()));
            }

            $request->body = $contents;
        } else {
            throw new TosClientException('neither acl nor grants is set');
        }

        $request->operation = 'putObjectACL';
        $request->method = Enum::HttpMethodPut;
        $request->bucket = $bucket;
        $request->key = $key;
        $request->queries = $queries;
        return $request;
    }

    protected static function &transSetObjectMetaInput(SetObjectMetaInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        $key = self::checkKey($input->getKey());

        $queries = ['metadata' => ''];
        if ($versionId = $input->getVersionID()) {
            $queries[Constant::QueryVersionId] = $versionId;
        }

        $headers = [];
        self::dealHttpBasicHeader($input, $headers);
        self::dealMeta($input, $headers);

        $request = new HttpRequest();
        $request->operation = 'setObjectMeta';
        $request->method = Enum::HttpMethodPost;
        $request->bucket = $bucket;
        $request->key = $key;
        $request->headers = $headers;
        $request->queries = $queries;
        return $request;
    }

    protected static function &transCreateMultipartUploadInput(CreateMultipartUploadInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        $key = self::checkKey($input->getKey());

        $headers = [];
        self::dealHttpBasicHeader($input, $headers);
        self::dealAcl($input, $headers, false);
        self::dealSse($input, $headers);
        self::dealMeta($input, $headers);
        self::dealScAndWrl($input, $headers);
        self::dealTagging($input, $headers);

        $queries = ['uploads' => ''];
        self::dealEncodingType($input, $queries);

        $request = new HttpRequest();
        $request->operation = 'createMultipartUpload';
        $request->method = Enum::HttpMethodPost;
        $request->bucket = $bucket;
        $request->key = $key;
        $request->headers = $headers;
        $request->queries = $queries;
        return $request;
    }

    protected static function &transUploadFileInput(UploadFileInput &$input, ConfigParser &$cp)
    {
        $filePath = self::checkFilePath($input->getFilePath());

        $partSize = intval($input->getPartSize());
        if ($partSize < 5 * 1024 * 1024 || $partSize > 5 * 1024 * 1024 * 1024) {
            throw new TosClientException('invalid part size, the size must be [5242880, 5368709120]');
        }

        $taskNum = intval($input->getTaskNum());
        if ($taskNum <= 0) {
            $taskNum = 1;
        }

        $checkpointFile = null;
        if ($input->isEnableCheckpoint()) {
            $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
            $key = self::checkKey($input->getKey());

            $checkpointFile = trim(strval($input->getCheckpointFile()));
            if (is_dir($checkpointFile)) {
                $checkpointFile .= DIRECTORY_SEPARATOR . basename($filePath) . '.' . base64_encode(md5($bucket . '.' . $key)) . '.upload';
            } else if ($checkpointFile === '') {
                $checkpointFile = dirname($filePath) . DIRECTORY_SEPARATOR . basename($filePath) . '.' . base64_encode(md5($bucket . '.' . $key)) . '.upload';
            }
        }

        $result = [$filePath, $partSize, $taskNum, $checkpointFile];
        return $result;
    }

    protected static function &transUploadPartInput(UploadPartInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        $key = self::checkKey($input->getKey());
        if (!$input->getContent()) {
            throw new TosClientException('empty content');
        }
        $headers = [];
        self::dealContentLengthAndContentMD5($input, $headers);
        self::dealSse($input, $headers, false);

        $queries = [];
        self::dealUploadIdAndPartNumber($input, $queries);
        $request = new HttpRequest();
        $request->operation = 'uploadPart';
        $request->method = Enum::HttpMethodPut;
        $request->bucket = $bucket;
        $request->key = $key;
        $request->headers = $headers;
        $request->queries = $queries;
        $request->body = $input->getContent();
        return $request;
    }

    protected static function &transUploadPartFromFileInput(UploadPartFromFileInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        $key = self::checkKey($input->getKey());

        $filePath = self::checkFilePath($input->getFilePath());

        $file = fopen($filePath, 'r');
        $offset = intval($input->getOffset());
        $fileSize = filesize($filePath);
        if ($offset < 0 || $offset > $fileSize) {
            throw new TosClientException('invalid offset');
        } else if ($offset > 0) {
            fseek($file, $offset, 0);
        }

        $headers = [];
        $partSize = $input->getPartSize();
        if (isset($partSize) && ($partSize = intval($partSize)) >= 0) {
            $headers[Constant::HeaderContentLength] = $partSize;
        } else {
            $headers[Constant::HeaderContentLength] = $fileSize - $offset;
        }

        if ($contentMD5 = $input->getContentMD5()) {
            $headers[Constant::HeaderContentMD5] = $contentMD5;
        }
        self::dealSse($input, $headers, false);

        $queries = [];
        self::dealUploadIdAndPartNumber($input, $queries);
        $request = new HttpRequest();
        $request->operation = 'uploadPartFromFile';
        $request->method = Enum::HttpMethodPut;
        $request->bucket = $bucket;
        $request->key = $key;
        $request->headers = $headers;
        $request->queries = $queries;
//        $request->body = new StreamReader(new Stream($file), $headers[Constant::HeaderContentLength]);
        $request->body = $file;
        return $request;
    }

    protected static function &transCompleteMultipartUploadInput(CompleteMultipartUploadInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        $key = self::checkKey($input->getKey());
        if (!is_array($parts = $input->getParts()) || count($parts) === 0) {
            throw new TosClientException('empty parts');
        }

        $body = ['Parts' => []];
        foreach ($parts as $part) {
            $body['Parts'][] = ['PartNumber' => intval($part->getPartNumber()), 'ETag' => strval($part->getETag())];
        }
        usort($body['Parts'], function ($a, $b) {
            if ($a['PartNumber'] === $b['PartNumber']) {
                return 0;
            }
            return $a['PartNumber'] > $b['PartNumber'] ? 1 : -1;
        });

        $contents = json_encode($body);
        if (!$contents || json_last_error() !== 0) {
            throw new TosClientException(sprintf('unable to do serialization/deserialization, %s', json_last_error_msg()));
        }

        $queries = [];
        self::dealUploadIdAndPartNumber($input, $queries, true);

        $request = new HttpRequest();
        $request->operation = 'completeMultipartUpload';
        $request->method = Enum::HttpMethodPost;
        $request->bucket = $bucket;
        $request->key = $key;
        $request->queries = $queries;
        $request->body = $contents;
        return $request;
    }

    protected static function &transAbortMultipartUploadInput(AbortMultipartUploadInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        $key = self::checkKey($input->getKey());

        $queries = [];
        self::dealUploadIdAndPartNumber($input, $queries, true);

        $request = new HttpRequest();
        $request->operation = 'abortMultipartUpload';
        $request->method = Enum::HttpMethodDelete;
        $request->bucket = $bucket;
        $request->key = $key;
        $request->queries = $queries;
        return $request;
    }

    protected static function &transUploadPartCopyInput(UploadPartCopyInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        $key = self::checkKey($input->getKey());

        $headers = [];
        $srcBucket = self::checkBucket($input->getSrcBucket());
        $srcKey = self::checkKey($input->getSrcKey());
        $copySource = '/' . $srcBucket . '/' . Helper::urlencodeWithSafe($srcKey);
        if ($srcVersionId = $input->getSrcVersionID()) {
            $copySource .= '?versionId=' . $srcVersionId;
        }
        $headers[Constant:: HeaderCopySource] = $copySource;

        self::dealIfCondition($input, $headers, true);
        self::dealSse($input, $headers, false, true);

        if ($range = trim(strval($input->getCopySourceRange()))) {
            if (strpos($range, 'bytes=') !== 0) {
                throw new TosClientException('invalid range format');
            }

            $headers[Constant::HeaderCopySourceRange] = $range;
        } else {
            $rangeStart = $input->getCopySourceRangeStart();
            $rangeEnd = $input->getCopySourceRangeEnd();
            if (isset($rangeStart) && isset($rangeEnd) && (($rangeStart = intval($rangeStart)) >= 0) && ($rangeStart <= ($rangeEnd = (intval($rangeEnd))))) {
                $headers[Constant::HeaderCopySourceRange] = 'bytes=' . $rangeStart . '-' . $rangeEnd;
            }
        }

        $queries = [];
        self::dealUploadIdAndPartNumber($input, $queries);

        $request = new HttpRequest();
        $request->operation = 'uploadPartCopy';
        $request->method = Enum::HttpMethodPut;
        $request->bucket = $bucket;
        $request->key = $key;
        $request->headers = $headers;
        $request->queries = $queries;
        return $request;
    }

    protected static function &transListMultipartUploadsInput(ListMultipartUploadsInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        $queries = ['uploads' => ''];
        self::dealListParams($input, $queries, 2);

        $request = new HttpRequest();
        $request->operation = 'listMultipartUploads';
        $request->method = Enum::HttpMethodGet;
        $request->bucket = $bucket;
        $request->queries = $queries;
        return $request;
    }

    protected static function &transListPartsInput(ListPartsInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        $key = self::checkKey($input->getKey());

        $queries = [];
        self::dealUploadIdAndPartNumber($input, $queries, true);
        if ($maxParts = intval($input->getMaxParts())) {
            $queries['max-parts'] = $maxParts;
        }

        if ($partNumberMarker = intval($input->getPartNumberMarker())) {
            $queries['part-number-marker'] = $partNumberMarker;
        }

        $request = new HttpRequest();
        $request->operation = 'listParts';
        $request->method = Enum::HttpMethodGet;
        $request->bucket = $bucket;
        $request->key = $key;
        $request->queries = $queries;
        return $request;
    }

    protected static function &transPutObjectTaggingInput(PutObjectTaggingInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        $key = self::checkKey($input->getKey());

        $queries = ['tagging' => ''];
        if ($versionId = $input->getVersionID()) {
            $queries[Constant::QueryVersionId] = $versionId;
        }

        $tagSet = $input->getTagSet();

        if (!$tagSet || !($tags = $tagSet->getTags()) || !is_array($tags) || count($tags) === 0) {
            throw new TosClientException('empty tags');
        }

        $tagSet = [];
        foreach ($tags as $tag) {
            $tagSet['Tags'] [] = ['Key' => $tag->getKey(), 'Value' => $tag->getValue()];
        }
        $body = [];
        $body['TagSet'] = $tagSet;

        $contents = json_encode($body);
        if (!$contents || json_last_error() !== 0) {
            throw new TosClientException(sprintf('unable to do serialization/deserialization, %s', json_last_error_msg()));
        }

        $request = new HttpRequest();
        $request->body = $contents;
        $request->operation = 'putObjectTagging';
        $request->method = Enum::HttpMethodPut;
        $request->bucket = $bucket;
        $request->key = $key;
        $request->queries = $queries;
        return $request;
    }

    protected static function &transGetObjectTaggingInput(GetObjectTaggingInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        $key = self::checkKey($input->getKey());
        $queries = ['tagging' => ''];
        if ($versionId = $input->getVersionID()) {
            $queries[Constant::QueryVersionId] = $versionId;
        }
        $request = new HttpRequest();
        $request->operation = 'getObjectTagging';
        $request->method = Enum::HttpMethodGet;
        $request->bucket = $bucket;
        $request->key = $key;
        $request->queries = $queries;
        return $request;
    }

    protected static function &transDeleteObjectTaggingInput(DeleteObjectTaggingInput &$input, ConfigParser &$cp)
    {
        $bucket = self::checkBucket($input->getBucket(), $cp->isCustomDomain());
        $key = self::checkKey($input->getKey());
        $queries = ['tagging' => ''];
        if ($versionId = $input->getVersionID()) {
            $queries[Constant::QueryVersionId] = $versionId;
        }
        $request = new HttpRequest();
        $request->operation = 'deleteObjectTagging';
        $request->method = Enum::HttpMethodDelete;
        $request->bucket = $bucket;
        $request->key = $key;
        $request->queries = $queries;
        return $request;
    }

    protected static function dealHttpBasicHeader(&$input, &$headers)
    {
        if ($cacheControl = $input->getCacheControl()) {
            $headers[Constant::HeaderCacheControl] = $cacheControl;
        }

        if ($contentType = $input->getContentType()) {
            $headers[Constant::HeaderContentType] = $contentType;
        }

        if ($contentLanguage = $input->getContentLanguage()) {
            $headers[Constant::HeaderContentLanguage] = $contentLanguage;
        }

        if ($contentEncoding = $input->getContentEncoding()) {
            $headers[Constant::HeaderContentEncoding] = $contentEncoding;
        }

        if ($contentDisposition = $input->getContentDisposition()) {
            $headers[Constant::HeaderContentDisposition] = Helper::urlEncodeChinese($contentDisposition);
        }

        if (($expires = intval($input->getExpires())) && $expires > 0) {
            $headers[Constant::HeaderExpires] = gmdate('D, d M Y H:i:s \G\M\T', $expires);
        }
    }

    protected static function dealIfCondition(&$input, &$headers, $copySource = false)
    {
        if ($copySource) {
            if ($copySourceIfMatch = $input->getCopySourceIfMatch()) {
                $headers[Constant::HeaderCopySourceIfMatch] = $copySourceIfMatch;
            }

            if ($copySourceIfNoneMatch = $input->getCopySourceIfNoneMatch()) {
                $headers[Constant::HeaderCopySourceIfNoneMatch] = $copySourceIfNoneMatch;
            }

            if (($copySourceIfModifiedSince = intval($input->getCopySourceIfModifiedSince())) && $copySourceIfModifiedSince > 0) {
                $headers[Constant::HeaderCopySourceIfModifiedSince] = gmdate('D, d M Y H:i:s \G\M\T', $copySourceIfModifiedSince);
            }

            if (($copySourceIfUnModifiedSince = intval($input->getCopySourceIfUnModifiedSince())) && $copySourceIfUnModifiedSince > 0) {
                $headers[Constant::HeaderCopySourceIfUnmodifiedSince] = gmdate('D, d M Y H:i:s \G\M\T', $copySourceIfUnModifiedSince);
            }
        } else {
            if ($ifMatch = $input->getIfMatch()) {
                $headers[Constant::HeaderIfMatch] = $ifMatch;
            }

            if ($ifNoneMatch = $input->getIfNoneMatch()) {
                $headers[Constant::HeaderIfNoneMatch] = $ifNoneMatch;
            }

            if (($ifModifiedSince = intval($input->getIfModifiedSince())) && $ifModifiedSince > 0) {
                $headers[Constant::HeaderIfModifiedSince] = gmdate('D, d M Y H:i:s \G\M\T', $ifModifiedSince);
            }

            if (($ifUnModifiedSince = intval($input->getIfUnModifiedSince())) && $ifUnModifiedSince > 0) {
                $headers[Constant::HeaderIfUnmodifiedSince] = gmdate('D, d M Y H:i:s \G\M\T', $ifUnModifiedSince);
            }
        }
    }

    protected static function dealMeta(&$input, &$headers)
    {
        if (($meta = $input->getMeta()) && is_array($meta)) {
            foreach ($meta as $key => $val) {
                $key = Helper::urlEncodeChinese($key);
                if (strpos($key, Constant::HeaderPrefixMeta) !== 0) {
                    $key = Constant::HeaderPrefixMeta . $key;
                }
                $headers[$key] = Helper::urlEncodeChinese($val);
            }
        }
    }

    protected static function dealSse(&$input, &$headers, $dealServerSideEncryption = true, $copySource = false)
    {
        if ($copySource) {
            if (($copySourceSsecAlgorithm = $input->getCopySourceSSECAlgorithm()) && self::checkSSECAlgorithm($copySourceSsecAlgorithm)) {
                if (!$input->getCopySourceSSECKey() || !$input->getCopySourceSSECKeyMD5()) {
                    throw new TosClientException('empty copy source ssec key or ssec key md5');
                }
                $headers[Constant::HeaderCopySourceSSECAlgorithm] = $copySourceSsecAlgorithm;
                $headers[Constant::HeaderCopySourceSSECKey] = $input->getCopySourceSSECKey();
                $headers[Constant::HeaderCopySourceSSECKeyMD5] = $input->getCopySourceSSECKeyMD5();
            }
        }

        if (($ssecAlgorithm = $input->getSSECAlgorithm()) && self::checkSSECAlgorithm($ssecAlgorithm)) {
            if ($dealServerSideEncryption && $input->getServerSideEncryption()) {
                throw new TosClientException('both ssec and server side encryption are set');
            }

            if (!$input->getSSECKey() || !$input->getSSECKeyMD5()) {
                throw new TosClientException('empty ssec key or ssec key md5');
            }

            $headers[Constant::HeaderSSECAlgorithm] = $ssecAlgorithm;
            $headers[Constant::HeaderSSECKey] = $input->getSSECKey();
            $headers[Constant::HeaderSSECKeyMD5] = $input->getSSECKeyMD5();
        } else if ($dealServerSideEncryption && ($serverSideEncryption = $input->getServerSideEncryption()) && self::checkServerSideEncryption($serverSideEncryption)) {
            $headers[Constant::HeaderServerSideEncryption] = $serverSideEncryption;
        }
    }

    protected static function dealAcl(&$input, &$headers, $dealGrantWrite = true)
    {
        if (($acl = $input->getACL()) && self::checkAcl($acl)) {
            $headers[Constant::HeaderAcl] = $acl;
        }

        if ($grantFullControl = $input->getGrantFullControl()) {
            $headers[Constant::HeaderGrantFullControl] = $grantFullControl;
        }

        if ($grantRead = $input->getGrantRead()) {
            $headers[Constant::HeaderGrantRead] = $grantRead;
        }

        if ($grantReadAcp = $input->getGrantReadAcp()) {
            $headers[Constant::HeaderGrantReadAcp] = $grantReadAcp;
        }

        if ($dealGrantWrite) {
            if ($grantWrite = $input->getGrantWrite()) {
                $headers[Constant::HeaderGrantWrite] = $grantWrite;
            }
        }

        if ($grantWriteAcp = $input->getGrantWriteAcp()) {
            $headers[Constant::HeaderGrantWriteAcp] = $grantWriteAcp;
        }
    }

    protected static function dealScAndWrl(&$input, &$headers)
    {
        if (($storageClass = $input->getStorageClass()) && self::checkStorageClass($storageClass)) {
            $headers[Constant::HeaderStorageClass] = $storageClass;
        }

        if ($websiteRedirectLocation = $input->getWebsiteRedirectLocation()) {
            $headers[Constant::HeaderWebsiteRedirectLocation] = $websiteRedirectLocation;
        }
    }

    protected static function dealTagging(&$input, &$headers, $withTaggingDirective = false)
    {
        if ($tagging = $input->getTagging()) {
            $headers[Constant::HeaderTagging] = $tagging;
        }

        if ($withTaggingDirective && ($taggingDirective = $input->getTaggingDirective()) && self::checkTaggingDirectiveType($taggingDirective)) {
            $headers[Constant::HeaderTaggingDirective] = $taggingDirective;
        }
    }

    protected static function dealContentLengthAndContentMD5(&$input, &$headers)
    {
        $contentLength = $input->getContentLength();
        if (isset($contentLength) && ($contentLength = intval($contentLength)) >= 0) {
            $headers[Constant::HeaderContentLength] = $contentLength;
        }

        if ($contentMD5 = $input->getContentMD5()) {
            $headers[Constant::HeaderContentMD5] = $contentMD5;
        }
    }

    protected static function dealListParams(&$input, &$queries, $listParamsType = 0)
    {
        if ($prefix = $input->getPrefix()) {
            $queries['prefix'] = $prefix;
        }
        if ($delimiter = $input->getDelimiter()) {
            $queries['delimiter'] = $delimiter;
        }

        if ($listParamsType <= 1) {
            if ($listParamsType === 0) {
                if ($marker = $input->getMarker()) {
                    $queries['marker'] = $marker;
                }
                if ($reverse = boolval($input->isReverse())) {
                    $queries['reverse'] = $reverse;
                }
            } else {
                if ($keyMarker = $input->getKeyMarker()) {
                    $queries['key-marker'] = $keyMarker;
                }
                if ($versionIdMarker = $input->getVersionIDMarker()) {
                    $queries['version-id-marker'] = $versionIdMarker;
                }
            }
            if ($maxKeys = intval($input->getMaxKeys())) {
                $queries['max-keys'] = $maxKeys;
            }
        } else if ($listParamsType === 2) {
            if ($keyMarker = $input->getKeyMarker()) {
                $queries['key-marker'] = $keyMarker;
            }
            if ($uploadIdMarker = $input->getUploadIDMarker()) {
                $queries['upload-id-marker'] = $uploadIdMarker;
            }
            if ($maxUploads = intval($input->getMaxUploads())) {
                $queries['max-uploads'] = $maxUploads;
            }
        }

        self::dealEncodingType($input, $queries);
    }

    protected static function dealUploadIdAndPartNumber(&$input, &$queries, $uploadIdOnly = false)
    {
        if ($uploadId = $input->getUploadID()) {
            $queries['uploadId'] = $uploadId;
        } else {
            throw new TosClientException('empty upload id');
        }

        if (!$uploadIdOnly) {
            if ($partNumber = intval($input->getPartNumber())) {
                $queries['partNumber'] = $partNumber;
            } else {
                throw new TosClientException('invalid part number');
            }
        }
    }

    protected static function dealEncodingType(&$input, &$queries)
    {
        if (($encodingType = $input->getEncodingType()) && self::checkEncodingType($encodingType)) {
            $queries['encoding-type'] = $encodingType;
        }
    }

    protected static function checkBucket($bucket, $isCustomDomain=false)
    {
        if($isCustomDomain){
            return $bucket;
        }

        $bucket = trim($bucket);
        $length = strlen($bucket);
        if ($length < 3 || $length > 63) {
            throw new TosClientException('invalid bucket name, the length must be [3, 63]');
        }

        if ($bucket[0] === '-' || $bucket[strlen($bucket) - 1] === '-') {
            throw new TosClientException('invalid bucket name, the bucket name can be neither starting with \'-\' nor ending with \'-\'');
        }

        if (!preg_match('/^[a-z0-9-]+$/', $bucket)) {
            throw new TosClientException('invalid bucket name, the character set is illegal');
        }

        return $bucket;
    }

    protected static function checkKey($key)
    {
        $length = strlen($key);
        if ($length < 1 || $length > 696) {
            throw new TosClientException('invalid object name, the length must be [1, 696]');
        }

        if ($length === 1 && (ord($key) < 32 || ord($key) > 127)) {
            throw new TosClientException('invalid object name, the character set is illegal');
        }

        if ($key[0] === '\\') {
            throw new TosClientException('invalid object name, the object name can not start with \\');
        }

        return $key;
    }

    protected static function &transGranteeToArray(Grantee &$grantee)
    {
        switch ($type = $grantee->getType()) {
            case Enum::GranteeGroup:
                self::checkCanned($canned = $grantee->getCanned());
                $result = ['Type' => $type, 'Canned' => $canned];
                return $result;
            case Enum::GranteeUser:
                if (!$id = $grantee->getID()) {
                    throw new TosClientException('empty grantee id');
                }
                $result = ['Type' => $type, 'ID' => strval($id)];
                return $result;
            default:
                throw new TosClientException('invalid grantee type');
        }
    }

    protected static function checkFilePath($filePath)
    {
        $filePath = trim($filePath);

        if (!$filePath) {
            throw new TosClientException('empty file path');
        }

        if (!file_exists($filePath)) {
            throw new TosClientException('the specified file path does not exist');
        }

        if (is_dir($filePath)) {
            throw new TosClientException('the specified file path is a dir');
        }

        return $filePath;
    }
}