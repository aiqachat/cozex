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

class DeleteMultiObjectsInput
{
    /**
     * @var string
     */
    private $bucket;
    /**
     * @var ObjectTobeDeleted[]
     */
    private $objects;
    /**
     * @var bool
     */
    private $quiet;

    /**
     * @param string $bucket
     * @param ObjectTobeDeleted[] $objects
     * @param bool $quiet
     */
    public function __construct($bucket = '', array $objects = [], $quiet = false)
    {
        $this->bucket = $bucket;
        $this->objects = $objects;
        $this->quiet = $quiet;
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
     * @return ObjectTobeDeleted[]
     */
    public function getObjects()
    {
        return $this->objects;
    }

    /**
     * @param ObjectTobeDeleted[] $objects
     */
    public function setObjects($objects)
    {
        $this->objects = $objects;
    }

    /**
     * @return bool
     */
    public function isQuiet()
    {
        return $this->quiet;
    }

    /**
     * @param bool $quiet
     */
    public function setQuiet($quiet)
    {
        $this->quiet = $quiet;
    }
}