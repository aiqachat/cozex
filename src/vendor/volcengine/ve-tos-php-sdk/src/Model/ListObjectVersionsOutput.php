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

class ListObjectVersionsOutput
{
    use RequestInfoHolder;

    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $prefix;
    /**
     * @var string
     */
    private $delimiter;
    /**
     * @var string
     */
    private $keyMarker;
    /**
     * @var string
     */
    private $versionIdMarker;
    /**
     * @var int
     */
    private $maxKeys;
    /**
     * @var string
     */
    private $encodingType;
    /**
     * @var bool
     */
    private $isTruncated;
    /**
     * @var string
     */
    private $nextKeyMarker;
    /**
     * @var string
     */
    private $nextVersionIdMarker;
    /**
     * @var ListedCommonPrefix[]
     */
    private $commonPrefixes;
    /**
     * @var ListedObjectVersion[]
     */
    private $versions;
    /**
     * @var ListedDeleteMarker[]
     */
    private $deleteMarkers;

    /**
     * @param RequestInfo $requestInfo
     * @param string $name
     * @param string $prefix
     * @param string $delimiter
     * @param string $keyMarker
     * @param string $versionIdMarker
     * @param int $maxKeys
     * @param string $encodingType
     * @param bool $isTruncated
     * @param string $nextKeyMarker
     * @param string $nextVersionIdMarker
     * @param ListedCommonPrefix[] $commonPrefixes
     * @param ListedObjectVersion[] $versions
     * @param ListedDeleteMarker[] $deleteMarkers
     */
    public function __construct(RequestInfo &$requestInfo, $name = '', $prefix = '', $delimiter = '', $keyMarker = '', $versionIdMarker = '',
                                            $maxKeys = 0, $encodingType = '', $isTruncated = false, $nextKeyMarker = '', $nextVersionIdMarker = '',
                                array       &$commonPrefixes = [], array &$versions = [], array &$deleteMarkers = [])
    {
        $this->requestInfo = $requestInfo;
        $this->name = $name;
        $this->prefix = $prefix;
        $this->delimiter = $delimiter;
        $this->keyMarker = $keyMarker;
        $this->versionIdMarker = $versionIdMarker;
        $this->maxKeys = $maxKeys;
        $this->encodingType = $encodingType;
        $this->isTruncated = $isTruncated;
        $this->nextKeyMarker = $nextKeyMarker;
        $this->nextVersionIdMarker = $nextVersionIdMarker;
        $this->commonPrefixes = $commonPrefixes;
        $this->versions = $versions;
        $this->deleteMarkers = $deleteMarkers;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * @return string
     */
    public function getKeyMarker()
    {
        return $this->keyMarker;
    }

    /**
     * @return string
     */
    public function getVersionIDMarker()
    {
        return $this->versionIdMarker;
    }

    /**
     * @return int
     */
    public function getMaxKeys()
    {
        return $this->maxKeys;
    }

    /**
     * @return string
     */
    public function getEncodingType()
    {
        return $this->encodingType;
    }

    /**
     * @return bool
     */
    public function isTruncated()
    {
        return $this->isTruncated;
    }

    /**
     * @return string
     */
    public function getNextKeyMarker()
    {
        return $this->nextKeyMarker;
    }

    /**
     * @return string
     */
    public function getNextVersionIDMarker()
    {
        return $this->nextVersionIdMarker;
    }

    /**
     * @return ListedCommonPrefix[]
     */
    public function getCommonPrefixes()
    {
        return $this->commonPrefixes;
    }

    /**
     * @return ListedObjectVersion[]
     */
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * @return ListedDeleteMarker[]
     */
    public function getDeleteMarkers()
    {
        return $this->deleteMarkers;
    }
}