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

class ListPartsOutput
{
    use RequestInfoHolder;

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
    private $partNumberMarker;
    /**
     * @var int
     */
    private $maxParts;
    /**
     * @var bool
     */
    private $isTruncated;
    /**
     * @var int
     */
    private $nextPartNumberMarker;
    /**
     * @var string
     */
    private $storageClass;
    /**
     * @var Owner
     */
    private $owner;
    /**
     * @var UploadedPart[]
     */
    private $parts;

    /**
     * @param RequestInfo $requestInfo
     * @param string $bucket
     * @param string $key
     * @param string $uploadId
     * @param int $partNumberMarker
     * @param int $maxParts
     * @param bool $isTruncated
     * @param int $nextPartNumberMarker
     * @param string $storageClass
     * @param Owner $owner
     * @param UploadedPart[] $parts
     */
    public function __construct(RequestInfo &$requestInfo, $bucket = '', $key = '', $uploadId = '',
                                            $partNumberMarker = 1, $maxParts = 0, $isTruncated = false,
                                            $nextPartNumberMarker = 1, $storageClass = '', Owner &$owner = null, array &$parts = [])
    {
        $this->requestInfo = $requestInfo;
        $this->bucket = $bucket;
        $this->key = $key;
        $this->uploadId = $uploadId;
        $this->partNumberMarker = $partNumberMarker;
        $this->maxParts = $maxParts;
        $this->isTruncated = $isTruncated;
        $this->nextPartNumberMarker = $nextPartNumberMarker;
        $this->storageClass = $storageClass;
        $this->owner = $owner;
        $this->parts = $parts;
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
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getUploadID()
    {
        return $this->uploadId;
    }

    /**
     * @return int
     */
    public function getPartNumberMarker()
    {
        return $this->partNumberMarker;
    }

    /**
     * @return int
     */
    public function getMaxParts()
    {
        return $this->maxParts;
    }

    /**
     * @return bool
     */
    public function isTruncated()
    {
        return $this->isTruncated;
    }

    /**
     * @return int
     */
    public function getNextPartNumberMarker()
    {
        return $this->nextPartNumberMarker;
    }

    /**
     * @return string
     */
    public function getStorageClass()
    {
        return $this->storageClass;
    }

    /**
     * @return Owner|null
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return UploadedPart[]
     */
    public function getParts()
    {
        return $this->parts;
    }
}