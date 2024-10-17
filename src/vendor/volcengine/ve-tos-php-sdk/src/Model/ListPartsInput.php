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

class ListPartsInput
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
    private $partNumberMarker;
    /**
     * @var int
     */
    private $maxParts;

    /**
     * @param string $bucket
     * @param string $key
     * @param string $uploadId
     * @param int $maxParts
     * @param int $partNumberMarker
     */
    public function __construct($bucket = '', $key = '', $uploadId = '', $maxParts = 0, $partNumberMarker = 0)
    {
        $this->bucket = $bucket;
        $this->key = $key;
        $this->uploadId = $uploadId;
        $this->maxParts = $maxParts;
        $this->partNumberMarker = $partNumberMarker;
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
    public function getPartNumberMarker()
    {
        return $this->partNumberMarker;
    }

    /**
     * @param int|string $partNumberMarker
     */
    public function setPartNumberMarker($partNumberMarker)
    {
        $this->partNumberMarker = $partNumberMarker;
    }

    /**
     * @return int
     */
    public function getMaxParts()
    {
        return $this->maxParts;
    }

    /**
     * @param int $maxParts
     */
    public function setMaxParts($maxParts)
    {
        $this->maxParts = $maxParts;
    }
}