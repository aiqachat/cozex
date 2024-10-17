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

class UploadPartOutput
{
    use RequestInfoHolder;

    /**
     * @var int
     */
    private $partNumber;
    /**
     * @var string
     */
    private $etag;
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
    private $hashCrc64ecma;

    /**
     * @param RequestInfo $requestInfo
     * @param int $partNumber
     * @param string $etag
     * @param string $ssecAlgorithm
     * @param string $ssecKeyMD5
     * @param string $hashCrc64ecma
     */
    public function __construct(RequestInfo &$requestInfo, $partNumber = 1, $etag = '', $ssecAlgorithm = '', $ssecKeyMD5 = '', $hashCrc64ecma = '')
    {
        $this->requestInfo = $requestInfo;
        $this->partNumber = $partNumber;
        $this->etag = $etag;
        $this->ssecAlgorithm = $ssecAlgorithm;
        $this->ssecKeyMD5 = $ssecKeyMD5;
        $this->hashCrc64ecma = $hashCrc64ecma;
    }

    /**
     * @return int
     */
    public function getPartNumber()
    {
        return $this->partNumber;
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
    public function getHashCrc64ecma()
    {
        return $this->hashCrc64ecma;
    }
}