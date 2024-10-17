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

class GetObjectInput
{
    /**
     * @var string
     */
    private $bucket;
    /**
     * @var string
     */
    private $key;
    /**
     * @var string
     */
    private $versionId;
    /**
     * @var string
     */
    private $ifMatch;
    /**
     * @var int
     */
    private $ifModifiedSince;
    /**
     * @var string
     */
    private $ifNoneMatch;
    /**
     * @var int
     */
    private $ifUnmodifiedSince;
    /**
     * @var string
     */
    private $ssecAlgorithm;
    /**
     * @var string
     */
    private $ssecKey;
    /**
     * @var string
     */
    private $ssecKeyMD5;
    /**
     * @var string
     */
    private $responseCacheControl;
    /**
     * @var string
     */
    private $responseContentDisposition;
    /**
     * @var string
     */
    private $responseContentEncoding;
    /**
     * @var string
     */
    private $responseContentLanguage;
    /**
     * @var string
     */
    private $responseContentType;
    /**
     * @var int
     */
    private $responseExpires;
    /**
     * @var int
     */
    private $rangeStart;
    /**
     * @var int
     */
    private $rangeEnd;
    /**
     * @var string
     */
    private $range;
    /**
     * @var string
     */
    private $process;
    /**
     * @var string
     */
    private $saveBucket;
    /**
     * @var string
     */
    private $saveObject;
    /**
     * @var bool
     */
    private $streamMode = true;

    /**
     * @param $bucket
     * @param $key
     * @param $range
     * @param $versionId
     */
    public function __construct($bucket = '', $key = '', $range = '', $versionId = '')
    {
        $this->bucket = $bucket;
        $this->key = $key;
        $this->range = $range;
        $this->versionId = $versionId;
    }

    /**
     * @return string
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @param string $bucket
     */
    public function setBucket($bucket)
    {
        $this->bucket = $bucket;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getVersionID()
    {
        return $this->versionId;
    }

    /**
     * @param string $versionId
     */
    public function setVersionID($versionId)
    {
        $this->versionId = $versionId;
    }

    /**
     * @return string
     */
    public function getIfMatch()
    {
        return $this->ifMatch;
    }

    /**
     * @param string $ifMatch
     */
    public function setIfMatch($ifMatch)
    {
        $this->ifMatch = $ifMatch;
    }

    /**
     * @return int
     */
    public function getIfModifiedSince()
    {
        return $this->ifModifiedSince;
    }

    /**
     * @param int $ifModifiedSince
     */
    public function setIfModifiedSince($ifModifiedSince)
    {
        $this->ifModifiedSince = $ifModifiedSince;
    }

    /**
     * @return string
     */
    public function getIfNoneMatch()
    {
        return $this->ifNoneMatch;
    }

    /**
     * @param string $ifNoneMatch
     */
    public function setIfNoneMatch($ifNoneMatch)
    {
        $this->ifNoneMatch = $ifNoneMatch;
    }

    /**
     * @return int
     */
    public function getIfUnmodifiedSince()
    {
        return $this->ifUnmodifiedSince;
    }

    /**
     * @param int $ifUnmodifiedSince
     */
    public function setIfUnmodifiedSince($ifUnmodifiedSince)
    {
        $this->ifUnmodifiedSince = $ifUnmodifiedSince;
    }

    /**
     * @return string
     */
    public function getSSECAlgorithm()
    {
        return $this->ssecAlgorithm;
    }

    /**
     * @param string $ssecAlgorithm
     */
    public function setSSECAlgorithm($ssecAlgorithm)
    {
        $this->ssecAlgorithm = $ssecAlgorithm;
    }

    /**
     * @return string
     */
    public function getSSECKey()
    {
        return $this->ssecKey;
    }

    /**
     * @param string $ssecKey
     */
    public function setSSECKey($ssecKey)
    {
        $this->ssecKey = $ssecKey;
    }

    /**
     * @return string
     */
    public function getSSECKeyMD5()
    {
        return $this->ssecKeyMD5;
    }

    /**
     * @param string $ssecKeyMD5
     */
    public function setSSECKeyMD5($ssecKeyMD5)
    {
        $this->ssecKeyMD5 = $ssecKeyMD5;
    }

    /**
     * @return string
     */
    public function getResponseCacheControl()
    {
        return $this->responseCacheControl;
    }

    /**
     * @param string $responseCacheControl
     */
    public function setResponseCacheControl($responseCacheControl)
    {
        $this->responseCacheControl = $responseCacheControl;
    }

    /**
     * @return string
     */
    public function getResponseContentDisposition()
    {
        return $this->responseContentDisposition;
    }

    /**
     * @param string $responseContentDisposition
     */
    public function setResponseContentDisposition($responseContentDisposition)
    {
        $this->responseContentDisposition = $responseContentDisposition;
    }

    /**
     * @return string
     */
    public function getResponseContentEncoding()
    {
        return $this->responseContentEncoding;
    }

    /**
     * @param string $responseContentEncoding
     */
    public function setResponseContentEncoding($responseContentEncoding)
    {
        $this->responseContentEncoding = $responseContentEncoding;
    }

    /**
     * @return string
     */
    public function getResponseContentLanguage()
    {
        return $this->responseContentLanguage;
    }

    /**
     * @param string $responseContentLanguage
     */
    public function setResponseContentLanguage($responseContentLanguage)
    {
        $this->responseContentLanguage = $responseContentLanguage;
    }

    /**
     * @return string
     */
    public function getResponseContentType()
    {
        return $this->responseContentType;
    }

    /**
     * @param string $responseContentType
     */
    public function setResponseContentType($responseContentType)
    {
        $this->responseContentType = $responseContentType;
    }

    /**
     * @return int
     */
    public function getResponseExpires()
    {
        return $this->responseExpires;
    }

    /**
     * @param int $responseExpires
     */
    public function setResponseExpires($responseExpires)
    {
        $this->responseExpires = $responseExpires;
    }

    /**
     * @return int
     */
    public function getRangeStart()
    {
        return $this->rangeStart;
    }

    /**
     * @param int $rangeStart
     */
    public function setRangeStart($rangeStart)
    {
        $this->rangeStart = $rangeStart;
    }

    /**
     * @return int
     */
    public function getRangeEnd()
    {
        return $this->rangeEnd;
    }

    /**
     * @param int $rangeEnd
     */
    public function setRangeEnd($rangeEnd)
    {
        $this->rangeEnd = $rangeEnd;
    }

    /**
     * @return string
     */
    public function getRange()
    {
        return $this->range;
    }

    /**
     * @param string $range
     */
    public function setRange($range)
    {
        $this->range = $range;
    }

    /**
     * @return string
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @param string $process
     */
    public function setProcess($process)
    {
        $this->process = $process;
    }

    /**
     * @return string
     */
    public function getSaveBucket()
    {
        return $this->saveBucket;
    }

    /**
     * @param string $saveBucket
     */
    public function setSaveBucket($saveBucket)
    {
        $this->saveBucket = $saveBucket;
    }

    /**
     * @return string
     */
    public function getSaveObject()
    {
        return $this->saveObject;
    }

    /**
     * @param string $saveObject
     */
    public function setSaveObject($saveObject)
    {
        $this->saveObject = $saveObject;
    }

    /**
     * @return bool
     */
    public function isStreamMode()
    {
        return $this->streamMode;
    }

    /**
     * @param bool $streamMode
     */
    public function setStreamMode($streamMode)
    {
        $this->streamMode = $streamMode;
    }

}