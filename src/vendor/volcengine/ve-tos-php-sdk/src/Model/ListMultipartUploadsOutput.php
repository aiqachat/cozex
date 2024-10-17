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

class ListMultipartUploadsOutput
{
    use RequestInfoHolder;

    /**
     * @var string
     */
    private $bucket;
    /**
     * @var string
     */
    private $prefix;
    /**
     * @var string
     */
    private $delimiter;
    /**
     * @var string
     */
    private $keyMarker;
    /**
     * @var string
     */
    private $uploadIdMarker;
    /**
     * @var int
     */
    private $maxUploads;
    /**
     * @var string
     */
    private $encodingType;
    /**
     * @var bool
     */
    private $isTruncated;
    /**
     * @var string
     */
    private $nextKeyMarker;
    /**
     * @var string
     */
    private $nextUploadIdMarker;
    /**
     * @var ListedCommonPrefix[]
     */
    private $commonPrefixes;
    /**
     * @var ListedUpload[]
     */
    private $uploads;

    /**
     *
     * @param RequestInfo $requestInfo
     * @param string $bucket
     * @param string $prefix
     * @param string $delimiter
     * @param string $keyMarker
     * @param string $uploadIdMarker
     * @param int $maxUploads
     * @param string $encodingType
     * @param bool $isTruncated
     * @param string $nextKeyMarker
     * @param string $nextUploadIdMarker
     * @param ListedCommonPrefix[] $commonPrefixes
     * @param ListedUpload[] $uploads
     */
    public function __construct(RequestInfo &$requestInfo, $bucket = '', $prefix = '', $delimiter = '',
                                            $keyMarker = '', $uploadIdMarker = '', $maxUploads = 0, $encodingType = '',
                                            $isTruncated = false, $nextKeyMarker = '', $nextUploadIdMarker = '',
                                array       &$commonPrefixes = [], array &$uploads = [])
    {
        $this->requestInfo = $requestInfo;
        $this->bucket = $bucket;
        $this->prefix = $prefix;
        $this->delimiter = $delimiter;
        $this->keyMarker = $keyMarker;
        $this->uploadIdMarker = $uploadIdMarker;
        $this->maxUploads = $maxUploads;
        $this->encodingType = $encodingType;
        $this->isTruncated = $isTruncated;
        $this->nextKeyMarker = $nextKeyMarker;
        $this->nextUploadIdMarker = $nextUploadIdMarker;
        $this->commonPrefixes = $commonPrefixes;
        $this->uploads = $uploads;
    }

    /**
     * @return string
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * @return string
     */
    public function getKeyMarker()
    {
        return $this->keyMarker;
    }

    /**
     * @return string
     */
    public function getUploadIDMarker()
    {
        return $this->uploadIdMarker;
    }

    /**
     * @return int
     */
    public function getMaxUploads()
    {
        return $this->maxUploads;
    }

    /**
     * @return string
     */
    public function getEncodingType()
    {
        return $this->encodingType;
    }

    /**
     * @return bool
     */
    public function isTruncated()
    {
        return $this->isTruncated;
    }

    /**
     * @return string
     */
    public function getNextKeyMarker()
    {
        return $this->nextKeyMarker;
    }

    /**
     * @return string
     */
    public function getNextUploadIdMarker()
    {
        return $this->nextUploadIdMarker;
    }

    /**
     * @return ListedCommonPrefix[]
     */
    public function getCommonPrefixes()
    {
        return $this->commonPrefixes;
    }

    /**
     * @return ListedUpload[]
     */
    public function getUploads()
    {
        return $this->uploads;
    }
}