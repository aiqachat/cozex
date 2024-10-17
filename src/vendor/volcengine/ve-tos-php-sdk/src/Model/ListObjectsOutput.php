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

class ListObjectsOutput
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
    private $marker;
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
    private $nextMarker;
    /**
     * @var ListedCommonPrefix[]
     */
    private $commonPrefixes;
    /**
     * @var ListedObject[]
     */
    private $contents;

    /**
     *
     * @param RequestInfo $requestInfo
     * @param string $name
     * @param string $prefix
     * @param string $delimiter
     * @param string $marker
     * @param int $maxKeys
     * @param string $encodingType
     * @param bool $isTruncated
     * @param string $nextMarker
     * @param ListedCommonPrefix[] $commonPrefixes
     * @param ListedObject[] $contents
     */
    public function __construct(RequestInfo &$requestInfo, $name = '', $prefix = '', $delimiter = '',
                                            $marker = '', $maxKeys = 0, $encodingType = '', $isTruncated = false,
                                            $nextMarker = '', array &$commonPrefixes = [], array &$contents = [])
    {
        $this->requestInfo = $requestInfo;
        $this->name = $name;
        $this->prefix = $prefix;
        $this->delimiter = $delimiter;
        $this->marker = $marker;
        $this->maxKeys = $maxKeys;
        $this->encodingType = $encodingType;
        $this->isTruncated = $isTruncated;
        $this->nextMarker = $nextMarker;
        $this->commonPrefixes = $commonPrefixes;
        $this->contents = $contents;
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
    public function getMarker()
    {
        return $this->marker;
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
    public function getNextMarker()
    {
        return $this->nextMarker;
    }

    /**
     * @return ListedCommonPrefix[]
     */
    public function getCommonPrefixes()
    {
        return $this->commonPrefixes;
    }

    /**
     * @return ListedObject[]
     */
    public function getContents()
    {
        return $this->contents;
    }
}