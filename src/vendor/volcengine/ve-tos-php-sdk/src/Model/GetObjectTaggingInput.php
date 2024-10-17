<?php
/**
 * Copyright (2024) Volcengine
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

class GetObjectTaggingInput
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
     * @param string $bucket
     * @param string $key
     * @param string $versionId
     */
    public function __construct($bucket = '', $key = '', $versionId = '')
    {
        $this->bucket = $bucket;
        $this->key = $key;
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

}