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

class ListObjectsInput
{
    /**
     * @var string
     */
    private $bucket;
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
     * @var bool
     */
    private $reverse;
    /**
     * @var string
     */
    private $encodingType;

    /**
     * @param string $bucket
     * @param int $maxKeys
     * @param string $prefix
     * @param string $marker
     */
    public function __construct($bucket = '', $maxKeys = 0, $prefix = '', $marker = '')
    {
        $this->bucket = $bucket;
        $this->maxKeys = $maxKeys;
        $this->prefix = $prefix;
        $this->marker = $marker;
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
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * @param string $delimiter
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
    }

    /**
     * @return string
     */
    public function getMarker()
    {
        return $this->marker;
    }

    /**
     * @param string $marker
     */
    public function setMarker($marker)
    {
        $this->marker = $marker;
    }

    /**
     * @return int
     */
    public function getMaxKeys()
    {
        return $this->maxKeys;
    }

    /**
     * @param int $maxKeys
     */
    public function setMaxKeys($maxKeys)
    {
        $this->maxKeys = $maxKeys;
    }

    /**
     * @return bool
     */
    public function isReverse()
    {
        return $this->reverse;
    }

    /**
     * @param bool $reverse
     */
    public function setReverse($reverse)
    {
        $this->reverse = $reverse;
    }

    /**
     * @return string
     */
    public function getEncodingType()
    {
        return $this->encodingType;
    }

    /**
     * @param string $encodingType
     */
    public function setEncodingType($encodingType)
    {
        $this->encodingType = $encodingType;
    }

}