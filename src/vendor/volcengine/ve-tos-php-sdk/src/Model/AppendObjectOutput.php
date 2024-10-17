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

class AppendObjectOutput
{
    use RequestInfoHolder;

    /**
     * @var string
     */
    private $versionId;
    /**
     * @var int
     */
    private $nextAppendOffset;
    /**
     * @var string
     */
    private $hashCrc64ecma;

    /**
     * @param RequestInfo $requestInfo
     * @param string $versionId
     * @param int $nextAppendOffset
     * @param string $hashCrc64ecma
     */
    public function __construct(RequestInfo &$requestInfo, $versionId = '', $nextAppendOffset = 0, $hashCrc64ecma = '')
    {
        $this->requestInfo = $requestInfo;
        $this->versionId = $versionId;
        $this->nextAppendOffset = $nextAppendOffset;
        $this->hashCrc64ecma = $hashCrc64ecma;
    }

    /**
     * @return string
     */
    public function getVersionID()
    {
        return $this->versionId;
    }

    /**
     * @return int
     */
    public function getNextAppendOffset()
    {
        return $this->nextAppendOffset;
    }

    /**
     * @return string
     */
    public function getHashCrc64ecma()
    {
        return $this->hashCrc64ecma;
    }
}