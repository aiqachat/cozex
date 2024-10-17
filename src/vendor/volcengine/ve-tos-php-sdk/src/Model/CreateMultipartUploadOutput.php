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

class CreateMultipartUploadOutput
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
     * @var string
     */
    private $ssecAlgorithm;
    /**
     * @var string
     */
    private $ssecKeyMD5;
    /**
     * @var string
     */
    private $encodingType;

    /**
     * @param RequestInfo $requestInfo
     * @param string $bucket
     * @param string $key
     * @param string $uploadId
     * @param string $ssecAlgorithm
     * @param string $ssecKeyMD5
     * @param string $encodingType
     */
    public function __construct(RequestInfo &$requestInfo, $bucket = '', $key = '', $uploadId = '', $ssecAlgorithm = '', $ssecKeyMD5 = '', $encodingType = '')
    {
        $this->requestInfo = $requestInfo;
        $this->bucket = $bucket;
        $this->key = $key;
        $this->uploadId = $uploadId;
        $this->ssecAlgorithm = $ssecAlgorithm;
        $this->ssecKeyMD5 = $ssecKeyMD5;
        $this->encodingType = $encodingType;
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
     * @return string
     */
    public function getSSECAlgorithm()
    {
        return $this->ssecAlgorithm;
    }

    /**
     * @return string
     */
    public function getSSECKeyMD5()
    {
        return $this->ssecKeyMD5;
    }

    /**
     * @return string
     */
    public function getEncodingType()
    {
        return $this->encodingType;
    }
}