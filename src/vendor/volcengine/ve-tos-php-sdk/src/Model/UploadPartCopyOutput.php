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

class UploadPartCopyOutput
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
     * @var int
     */
    private $lastModified;
    /**
     * @var string
     */
    private $copySourceVersionId;
    /**
     * @var string
     */
    private $ssecAlgorithm;
    /**
     * @var string
     */
    private $ssecKeyMD5;

    /**
     * @param RequestInfo $requestInfo
     * @param int $partNumber
     * @param string $etag
     * @param int $lastModified
     * @param string $copySourceVersionId
     * @param string $ssecAlgorithm
     * @param string $ssecKeyMD5
     */
    public function __construct(RequestInfo &$requestInfo, $partNumber = 1, $etag = '',
                                            $lastModified = 0, $copySourceVersionId = '', $ssecAlgorithm = '', $ssecKeyMD5 = '')
    {
        $this->requestInfo = $requestInfo;
        $this->partNumber = $partNumber;
        $this->etag = $etag;
        $this->lastModified = $lastModified;
        $this->copySourceVersionId = $copySourceVersionId;
        $this->ssecAlgorithm = $ssecAlgorithm;
        $this->ssecKeyMD5 = $ssecKeyMD5;
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
     * @return int
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * @return string
     */
    public function getCopySourceVersionID()
    {
        return $this->copySourceVersionId;
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

}