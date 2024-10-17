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

class UploadedPart
{
    /**
     * @var int
     */
    private $partNumber;
    /**
     * @var string
     */
    private $etag;
    /**
     * @var int
     */
    private $size;
    /**
     * @var int
     */
    private $lastModified;

    /**
     * @param int $partNumber
     * @param string $etag
     * @param int $size
     * @param int $lastModified
     */
    public function __construct($partNumber = 1, $etag = '', $size = 0, $lastModified = 0)
    {
        $this->partNumber = $partNumber;
        $this->etag = $etag;
        $this->size = $size;
        $this->lastModified = $lastModified;
    }

    /**
     * @return int
     */
    public function getPartNumber()
    {
        return $this->partNumber;
    }

    /**
     * @param int $partNumber
     */
    public function setPartNumber($partNumber)
    {
        $this->partNumber = $partNumber;
    }

    /**
     * @return string
     */
    public function getETag()
    {
        return $this->etag;
    }

    /**
     * @param string $etag
     */
    public function setETag($etag)
    {
        $this->etag = $etag;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * @param int $lastModified
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;
    }
}