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

class UploadPartCopyInput
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
    private $uploadId;
    /**
     * @var int
     */
    private $partNumber;

    /**
     * @var string
     */
    private $srcBucket;
    /**
     * @var string
     */
    private $srcKey;
    /**
     * @var string
     */
    private $srcVersionId;
    /**
     * @var int
     */
    private $copySourceRangeStart;
    /**
     * @var int
     */
    private $copySourceRangeEnd;
    /**
     * @var string
     */
    private $copySourceRange;
    /**
     * @var string
     */
    private $copySourceIfMatch;
    /**
     * @var int
     */
    private $copySourceIfModifiedSince;
    /**
     * @var string
     */
    private $copySourceIfNoneMatch;
    /**
     * @var int
     */
    private $copySourceIfUnmodifiedSince;
    /**
     * @var string
     */
    private $copySourceSsecAlgorithm;
    /**
     * @var string
     */
    private $copySourceSsecKey;
    /**
     * @var string
     */
    private $copySourceSsecKeyMD5;
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
     * @param string $bucket
     * @param string $key
     * @param string $uploadId
     * @param int $partNumber
     * @param string $srcBucket
     * @param string $srcKey
     * @param string $srcVersionId
     */
    public function __construct($bucket = '', $key = '', $uploadId = '', $partNumber = 1,
                                $srcBucket = '', $srcKey = '', $srcVersionId = '')
    {
        $this->bucket = $bucket;
        $this->key = $key;
        $this->uploadId = $uploadId;
        $this->partNumber = $partNumber;
        $this->srcBucket = $srcBucket;
        $this->srcKey = $srcKey;
        $this->srcVersionId = $srcVersionId;
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
    public function getUploadID()
    {
        return $this->uploadId;
    }

    /**
     * @param string $uploadId
     */
    public function setUploadID($uploadId)
    {
        $this->uploadId = $uploadId;
    }

    /**
     * @return int
     */
    public function getPartNumber()
    {
        return $this->partNumber;
    }

    /**
     * @param int $partNumber
     */
    public function setPartNumber($partNumber)
    {
        $this->partNumber = $partNumber;
    }

    /**
     * @return string
     */
    public function getSrcBucket()
    {
        return $this->srcBucket;
    }

    /**
     * @param string $srcBucket
     */
    public function setSrcBucket($srcBucket)
    {
        $this->srcBucket = $srcBucket;
    }

    /**
     * @return string
     */
    public function getSrcKey()
    {
        return $this->srcKey;
    }

    /**
     * @param string $srcKey
     */
    public function setSrcKey($srcKey)
    {
        $this->srcKey = $srcKey;
    }

    /**
     * @return string
     */
    public function getSrcVersionID()
    {
        return $this->srcVersionId;
    }

    /**
     * @param string $srcVersionId
     */
    public function setSrcVersionID($srcVersionId)
    {
        $this->srcVersionId = $srcVersionId;
    }

    /**
     * @return int
     */
    public function getCopySourceRangeStart()
    {
        return $this->copySourceRangeStart;
    }

    /**
     * @param int $copySourceRangeStart
     */
    public function setCopySourceRangeStart($copySourceRangeStart)
    {
        $this->copySourceRangeStart = $copySourceRangeStart;
    }

    /**
     * @return int
     */
    public function getCopySourceRangeEnd()
    {
        return $this->copySourceRangeEnd;
    }

    /**
     * @param int $copySourceRangeEnd
     */
    public function setCopySourceRangeEnd($copySourceRangeEnd)
    {
        $this->copySourceRangeEnd = $copySourceRangeEnd;
    }

    /**
     * @return string
     */
    public function getCopySourceRange()
    {
        return $this->copySourceRange;
    }

    /**
     * @param string $copySourceRange
     */
    public function setCopySourceRange($copySourceRange)
    {
        $this->copySourceRange = $copySourceRange;
    }

    /**
     * @return string
     */
    public function getCopySourceIfMatch()
    {
        return $this->copySourceIfMatch;
    }

    /**
     * @param string $copySourceIfMatch
     */
    public function setCopySourceIfMatch($copySourceIfMatch)
    {
        $this->copySourceIfMatch = $copySourceIfMatch;
    }

    /**
     * @return int
     */
    public function getCopySourceIfModifiedSince()
    {
        return $this->copySourceIfModifiedSince;
    }

    /**
     * @param int $copySourceIfModifiedSince
     */
    public function setCopySourceIfModifiedSince($copySourceIfModifiedSince)
    {
        $this->copySourceIfModifiedSince = $copySourceIfModifiedSince;
    }

    /**
     * @return string
     */
    public function getCopySourceIfNoneMatch()
    {
        return $this->copySourceIfNoneMatch;
    }

    /**
     * @param string $copySourceIfNoneMatch
     */
    public function setCopySourceIfNoneMatch($copySourceIfNoneMatch)
    {
        $this->copySourceIfNoneMatch = $copySourceIfNoneMatch;
    }

    /**
     * @return int
     */
    public function getCopySourceIfUnmodifiedSince()
    {
        return $this->copySourceIfUnmodifiedSince;
    }

    /**
     * @param int $copySourceIfUnmodifiedSince
     */
    public function setCopySourceIfUnmodifiedSince($copySourceIfUnmodifiedSince)
    {
        $this->copySourceIfUnmodifiedSince = $copySourceIfUnmodifiedSince;
    }

    /**
     * @return string
     */
    public function getCopySourceSSECAlgorithm()
    {
        return $this->copySourceSsecAlgorithm;
    }

    /**
     * @param string $copySourceSsecAlgorithm
     */
    public function setCopySourceSSECAlgorithm($copySourceSsecAlgorithm)
    {
        $this->copySourceSsecAlgorithm = $copySourceSsecAlgorithm;
    }

    /**
     * @return string
     */
    public function getCopySourceSSECKey()
    {
        return $this->copySourceSsecKey;
    }

    /**
     * @param string $copySourceSsecKey
     */
    public function setCopySourceSSECKey($copySourceSsecKey)
    {
        $this->copySourceSsecKey = $copySourceSsecKey;
    }

    /**
     * @return string
     */
    public function getCopySourceSSECKeyMD5()
    {
        return $this->copySourceSsecKeyMD5;
    }

    /**
     * @param string $copySourceSsecKeyMD5
     */
    public function setCopySourceSSECKeyMD5($copySourceSsecKeyMD5)
    {
        $this->copySourceSsecKeyMD5 = $copySourceSsecKeyMD5;
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
}