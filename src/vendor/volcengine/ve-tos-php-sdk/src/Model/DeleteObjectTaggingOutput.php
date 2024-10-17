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

class DeleteObjectTaggingOutput
{
    use RequestInfoHolder;

    /**
     * @var string
     */
    private $versionId;

    /**
     * @param string $versionId
     */
    public function __construct(RequestInfo &$requestInfo, $versionId = '')
    {
        $this->requestInfo = $requestInfo;
        $this->versionId = $versionId;
    }

    /**
     * @return string
     */
    public function getVersionID()
    {
        return $this->versionId;
    }
}