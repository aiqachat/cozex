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

class CompleteMultipartUploadOutput
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
    private $etag;
    /**
     * @var string
     */
    private $location;
    /**
     * @var string
     */
    private $versionId;
    /**
     * @var string
     */
    private $hashCrc64ecma;

    /**
     * @param RequestInfo $requestInfo
     * @param string $bucket
     * @param string $key
     * @param string $etag
     * @param string $location
     * @param string $versionId
     * @param string $hashCrc64ecma
     */
    public function __construct(RequestInfo &$requestInfo, $bucket = '', $key = '', $etag = '', $location = '', $versionId = '', $hashCrc64ecma = '')
    {
        $this->requestInfo = $requestInfo;
        $this->bucket = $bucket;
        $this->key = $key;
        $this->etag = $etag;
        $this->location = $location;
        $this->versionId = $versionId;
        $this->hashCrc64ecma = $hashCrc64ecma;
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
    public function getETag()
    {
        return $this->etag;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return string
     */
    public function getVersionID()
    {
        return $this->versionId;
    }

    /**
     * @return string
     */
    public function getHashCrc64ecma()
    {
        return $this->hashCrc64ecma;
    }
}