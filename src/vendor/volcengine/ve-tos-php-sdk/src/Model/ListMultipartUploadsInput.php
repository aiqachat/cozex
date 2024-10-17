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

class ListMultipartUploadsInput
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
    private $keyMarker;
    /**
     * @var string
     */
    private $uploadIdMarker;
    /**
     * @var int
     */
    private $maxUploads;
    /**
     * @var string
     */
    private $encodingType;

    /**
     * @param string $bucket
     * @param int $maxUploads
     * @param string $prefix
     * @param string $keyMarker
     * @param string $uploadIdMarker
     */
    public function __construct($bucket = '', $maxUploads = 0, $prefix = '', $keyMarker = '', $uploadIdMarker = '')
    {
        $this->bucket = $bucket;
        $this->maxUploads = $maxUploads;
        $this->prefix = $prefix;
        $this->keyMarker = $keyMarker;
        $this->uploadIdMarker = $uploadIdMarker;
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
    public function getKeyMarker()
    {
        return $this->keyMarker;
    }

    /**
     * @param string $keyMarker
     */
    public function setKeyMarker($keyMarker)
    {
        $this->keyMarker = $keyMarker;
    }

    /**
     * @return string
     */
    public function getUploadIDMarker()
    {
        return $this->uploadIdMarker;
    }

    /**
     * @param string $uploadIdMarker
     */
    public function setUploadIDMarker($uploadIdMarker)
    {
        $this->uploadIdMarker = $uploadIdMarker;
    }

    /**
     * @return int
     */
    public function getMaxUploads()
    {
        return $this->maxUploads;
    }

    /**
     * @param int $maxUploads
     */
    public function setMaxUploads($maxUploads)
    {
        $this->maxUploads = $maxUploads;
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