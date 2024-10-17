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

class DeleteObjectOutput
{
    use RequestInfoHolder;

    /**
     * @var bool
     */
    private $deleteMarker;

    /**
     * @var string
     */
    private $versionId;

    /**
     * @param RequestInfo $requestInfo
     * @param bool $deleteMarker
     * @param string $versionId
     */
    public function __construct(RequestInfo &$requestInfo, $deleteMarker = false, $versionId = '')
    {
        $this->requestInfo = $requestInfo;
        $this->deleteMarker = $deleteMarker;
        $this->versionId = $versionId;
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
    public function getVersionID()
    {
        return $this->versionId;
    }
}