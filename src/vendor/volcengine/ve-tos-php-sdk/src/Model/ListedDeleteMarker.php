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

class ListedDeleteMarker
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
     * @var bool
     */
    private $isLatest;
    /**
     * @var Owner
     */
    private $owner;
    /**
     * @var string
     */
    private $versionId;

    /**
     * @param string $key
     * @param int $lastModified
     * @param bool $isLatest
     * @param Owner $owner
     * @param string $versionId
     */
    public function __construct($key = '', $lastModified = 0, $isLatest = false, Owner &$owner = null, $versionId = '')
    {
        $this->key = $key;
        $this->lastModified = $lastModified;
        $this->isLatest = $isLatest;
        $this->owner = $owner;
        $this->versionId = $versionId;
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
     * @return bool
     */
    public function isLatest()
    {
        return $this->isLatest;
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
    public function getVersionID()
    {
        return $this->versionId;
    }

}