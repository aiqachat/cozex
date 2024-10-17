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

class SetObjectMetaInput
{
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
    private $versionId;

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
     * @var string[]
     */
    private $meta;

    /**
     * @param $bucket
     * @param $key
     * @param $meta
     * @param $versionId
     */
    public function __construct($bucket = '', $key = '', $meta = [], $versionId = '')
    {
        $this->bucket = $bucket;
        $this->key = $key;
        $this->meta = $meta;
        $this->versionId = $versionId;
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
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getVersionID()
    {
        return $this->versionId;
    }

    /**
     * @param string $versionId
     */
    public function setVersionID($versionId)
    {
        $this->versionId = $versionId;
    }

    /**
     * @return string
     */
    public function getCacheControl()
    {
        return $this->cacheControl;
    }

    /**
     * @param string $cacheControl
     */
    public function setCacheControl($cacheControl)
    {
        $this->cacheControl = $cacheControl;
    }

    /**
     * @return string
     */
    public function getContentDisposition()
    {
        return $this->contentDisposition;
    }

    /**
     * @param string $contentDisposition
     */
    public function setContentDisposition($contentDisposition)
    {
        $this->contentDisposition = $contentDisposition;
    }

    /**
     * @return string
     */
    public function getContentEncoding()
    {
        return $this->contentEncoding;
    }

    /**
     * @param string $contentEncoding
     */
    public function setContentEncoding($contentEncoding)
    {
        $this->contentEncoding = $contentEncoding;
    }

    /**
     * @return string
     */
    public function getContentLanguage()
    {
        return $this->contentLanguage;
    }

    /**
     * @param string $contentLanguage
     */
    public function setContentLanguage($contentLanguage)
    {
        $this->contentLanguage = $contentLanguage;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return int
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * @param int $expires
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;
    }

    /**
     * @return string[]
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param string[] $meta
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;
    }
}