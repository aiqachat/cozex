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

use Psr\Http\Message\StreamInterface;

class GetObjectOutput extends GetObjectBasicOutput
{
    /**
     * @var StreamInterface
     */
    private $content;

    /**
     * @param RequestInfo $requestInfo
     * @param $contentRange
     * @param $etag
     * @param $lastModified
     * @param $deleteMarker
     * @param $ssecAlgorithm
     * @param $ssecKeyMD5
     * @param $versionId
     * @param $websiteRedirectLocation
     * @param $objectType
     * @param $hashCrc64ecma
     * @param $storageClass
     * @param array $meta
     * @param $contentLength
     * @param $cacheControl
     * @param $contentDisposition
     * @param $contentEncoding
     * @param $contentLanguage
     * @param $contentType
     * @param $expires
     * @param StreamInterface|null $content
     */
    public function __construct(RequestInfo &$requestInfo, $contentRange = '', $etag = '', $lastModified = 0,
                                            $deleteMarker = false, $ssecAlgorithm = '', $ssecKeyMD5 = '', $versionId = '',
                                            $websiteRedirectLocation = '', $objectType = '', $hashCrc64ecma = '', $storageClass = '',
                                array       &$meta = [], $contentLength = 0, $cacheControl = '', $contentDisposition = '',
                                            $contentEncoding = '', $contentLanguage = '', $contentType = '', $expires = 0, StreamInterface $content = null)
    {
        parent::__construct($requestInfo, $contentRange, $etag, $lastModified, $deleteMarker, $ssecAlgorithm, $ssecKeyMD5,
            $versionId, $websiteRedirectLocation, $objectType, $hashCrc64ecma, $storageClass, $meta, $contentLength,
            $cacheControl, $contentDisposition, $contentEncoding, $contentLanguage, $contentType, $expires);
        $this->content = $content;
    }

    /**
     * @return StreamInterface
     */
    public function getContent()
    {
        return $this->content;
    }
}