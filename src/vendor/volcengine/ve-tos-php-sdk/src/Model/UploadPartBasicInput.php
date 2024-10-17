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

class UploadPartBasicInput
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
    private $contentMD5;
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
     */
    public function __construct($bucket = '', $key = '', $uploadId = '', $partNumber = 1)
    {
        $this->bucket = $bucket;
        $this->key = $key;
        $this->uploadId = $uploadId;
        $this->partNumber = $partNumber;
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
    public function getContentMD5()
    {
        return $this->contentMD5;
    }

    /**
     * @param string $contentMD5
     */
    public function setContentMD5($contentMD5)
    {
        $this->contentMD5 = $contentMD5;
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