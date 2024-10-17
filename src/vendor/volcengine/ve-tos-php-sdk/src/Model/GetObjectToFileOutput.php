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

class GetObjectToFileOutput extends GetObjectBasicOutput
{

    private $realFilePath;

    /**
     * @param RequestInfo $requestInfo
     * @param string $contentRange
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
    public function __construct(RequestInfo &$requestInfo, $contentRange = '', $etag = '', $lastModified = 0,
                                            $deleteMarker = false, $ssecAlgorithm = '', $ssecKeyMD5 = '', $versionId = '',
                                            $websiteRedirectLocation = '', $objectType = '', $hashCrc64ecma = '', $storageClass = '',
                                array       &$meta = [], $contentLength = 0, $cacheControl = '', $contentDisposition = '',
                                            $contentEncoding = '', $contentLanguage = '', $contentType = '', $expires = 0, $realFilePath = '')
    {
        parent::__construct($requestInfo, $contentRange, $etag, $lastModified, $deleteMarker, $ssecAlgorithm, $ssecKeyMD5,
            $versionId, $websiteRedirectLocation, $objectType, $hashCrc64ecma, $storageClass, $meta, $contentLength,
            $cacheControl, $contentDisposition, $contentEncoding, $contentLanguage, $contentType, $expires);
        $this->realFilePath = $realFilePath;
    }

    /**
     * @return mixed|string
     */
    public function getRealFilePath()
    {
        return $this->realFilePath;
    }
    
}