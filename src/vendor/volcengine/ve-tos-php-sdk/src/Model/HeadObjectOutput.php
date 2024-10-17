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

class HeadObjectOutput
{
    use RequestInfoHolder;

    /**
     * @var string
     */
    private $etag;
    /**
     * @var int
     */
    private $lastModified;
    /**
     * @var bool
     */
    private $deleteMarker;
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
    private $versionId;
    /**
     * @var string
     */
    private $websiteRedirectLocation;
    /**
     * @var string
     */
    private $objectType;
    /**
     * @var string
     */
    private $hashCrc64ecma;
    /**
     * @var string
     */
    private $storageClass;
    /**
     * @var string[]
     */
    private $meta;
    /**
     * @var int
     */
    private $contentLength;

    /**
     * @var string
     */
    private $cacheControl;
    /**
     * @var string
     */
    private $contentDisposition;
    /**
     * @var string
     */
    private $contentEncoding;
    /**
     * @var string
     */
    private $contentLanguage;
    /**
     * @var string
     */
    private $contentType;
    /**
     * @var int
     */
    private $expires;

    /**
     * @param string $etag
     * @param int $lastModified
     * @param bool $deleteMarker
     * @param string $ssecAlgorithm
     * @param string $ssecKeyMD5
     * @param string $versionId
     * @param string $websiteRedirectLocation
     * @param string $objectType
     * @param string $hashCrc64ecma
     * @param string $storageClass
     * @param string[] $meta
     * @param int $contentLength
     * @param string $cacheControl
     * @param string $contentDisposition
     * @param string $contentEncoding
     * @param string $contentLanguage
     * @param string $contentType
     * @param int $expires
     */
    public function __construct(RequestInfo &$requestInfo, $etag = '', $lastModified = 0,
                                            $deleteMarker = false, $ssecAlgorithm = '', $ssecKeyMD5 = '', $versionId = '',
                                            $websiteRedirectLocation = '', $objectType = '', $hashCrc64ecma = '', $storageClass = '',
                                array       &$meta = [], $contentLength = 0, $cacheControl = '', $contentDisposition = '',
                                            $contentEncoding = '', $contentLanguage = '', $contentType = '', $expires = 0)
    {
        $this->requestInfo = $requestInfo;
        $this->etag = $etag;
        $this->lastModified = $lastModified;
        $this->deleteMarker = $deleteMarker;
        $this->ssecAlgorithm = $ssecAlgorithm;
        $this->ssecKeyMD5 = $ssecKeyMD5;
        $this->versionId = $versionId;
        $this->websiteRedirectLocation = $websiteRedirectLocation;
        $this->objectType = $objectType;
        $this->hashCrc64ecma = $hashCrc64ecma;
        $this->storageClass = $storageClass;
        $this->meta = $meta;
        $this->contentLength = $contentLength;
        $this->cacheControl = $cacheControl;
        $this->contentDisposition = $contentDisposition;
        $this->contentEncoding = $contentEncoding;
        $this->contentLanguage = $contentLanguage;
        $this->contentType = $contentType;
        $this->expires = $expires;
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
     * @return bool
     */
    public function isDeleteMarker()
    {
        return $this->deleteMarker;
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
    public function getVersionID()
    {
        return $this->versionId;
    }

    /**
     * @return string
     */
    public function getWebsiteRedirectLocation()
    {
        return $this->websiteRedirectLocation;
    }

    /**
     * @return string
     */
    public function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * @return string
     */
    public function getHashCrc64ecma()
    {
        return $this->hashCrc64ecma;
    }

    /**
     * @return string
     */
    public function getStorageClass()
    {
        return $this->storageClass;
    }

    /**
     * @return string[]
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @return int
     */
    public function getContentLength()
    {
        return $this->contentLength;
    }

    /**
     * @return string
     */
    public function getCacheControl()
    {
        return $this->cacheControl;
    }

    /**
     * @return string
     */
    public function getContentDisposition()
    {
        return $this->contentDisposition;
    }

    /**
     * @return string
     */
    public function getContentEncoding()
    {
        return $this->contentEncoding;
    }

    /**
     * @return string
     */
    public function getContentLanguage()
    {
        return $this->contentLanguage;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @return int
     */
    public function getExpires()
    {
        return $this->expires;
    }
}