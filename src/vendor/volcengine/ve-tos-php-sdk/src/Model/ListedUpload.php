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

class ListedUpload
{
    /**
     * @var string
     */
    private $key;
    /**
     * @var string
     */
    private $uploadId;
    /**
     * @var Owner
     */
    private $owner;
    /**
     * @var string
     */
    private $storageClass;
    /**
     * @var int
     */
    private $initiated;

    /**
     * @param string $key
     * @param string $uploadId
     * @param Owner $owner
     * @param string $storageClass
     * @param int $initiated
     */
    public function __construct($key = '', $uploadId = '', Owner &$owner = null, $storageClass = '', $initiated = 0)
    {
        $this->key = $key;
        $this->uploadId = $uploadId;
        $this->owner = $owner;
        $this->storageClass = $storageClass;
        $this->initiated = $initiated;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getUploadID()
    {
        return $this->uploadId;
    }

    /**
     * @return Owner
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
     * @return int
     */
    public function getInitiated()
    {
        return $this->initiated;
    }

}