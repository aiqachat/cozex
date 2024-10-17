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

class Deleted
{
    /**
     * @var string
     */
    private $key;
    /**
     * @var string
     */
    private $versionId;
    /**
     * @var bool
     */
    private $deleteMarker;
    /**
     * @var string
     */
    private $deleteMarkerVersionId;

    /**
     * @param string $key
     * @param string $versionId
     * @param bool $deleteMarker
     * @param string $deleteMarkerVersionId
     */
    public function __construct($key = '', $versionId = '', $deleteMarker = false, $deleteMarkerVersionId = '')
    {
        $this->key = $key;
        $this->versionId = $versionId;
        $this->deleteMarker = $deleteMarker;
        $this->deleteMarkerVersionId = $deleteMarkerVersionId;
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
    public function getVersionID()
    {
        return $this->versionId;
    }

    /**
     * @return bool
     */
    public function isDeleteMarker()
    {
        return $this->deleteMarker;
    }

    /**
     * @return string
     */
    public function getDeleteMarkerVersionID()
    {
        return $this->deleteMarkerVersionId;
    }

}