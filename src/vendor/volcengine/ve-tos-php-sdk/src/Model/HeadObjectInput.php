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

class HeadObjectInput
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
     * @param string $bucket
     * @param string $key
     * @param string $versionId
     */
    public function __construct($bucket = '', $key = '', $versionId = '')
    {
        $this->bucket = $bucket;
        $this->key = $key;
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

}