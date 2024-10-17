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

class ListedObject
{
    /**
     * @var string
     */
    private $key;
    /**
     * @var int
     */
    private $lastModified;
    /**
     * @var string
     */
    private $etag;
    /**
     * @var int
     */
    private $size;
    /**
     * @var Owner
     */
    private $owner;
    /**
     * @var string
     */
    private $storageClass;
    /**
     * @var string
     */
    private $hashCrc64ecma;

    /**
     * @param string $key
     * @param int $lastModified
     * @param string $etag
     * @param int $size
     * @param Owner $owner
     * @param string $storageClass
     * @param string $hashCrc64ecma
     */
    public function __construct($key = '', $lastModified = 0, $etag = '', $size = 0, Owner &$owner = null, $storageClass = '', $hashCrc64ecma = '')
    {
        $this->key = $key;
        $this->lastModified = $lastModified;
        $this->etag = $etag;
        $this->size = $size;
        $this->owner = $owner;
        $this->storageClass = $storageClass;
        $this->hashCrc64ecma = $hashCrc64ecma;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
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
    public function getETag()
    {
        return $this->etag;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return Owner|null
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return string
     */
    public function getStorageClass()
    {
        return $this->storageClass;
    }

    /**
     * @return string
     */
    public function getHashCrc64ecma()
    {
        return $this->hashCrc64ecma;
    }
}