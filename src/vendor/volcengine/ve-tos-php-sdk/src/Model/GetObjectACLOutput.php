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

class GetObjectACLOutput
{
    use RequestInfoHolder;

    /**
     * @var string
     */
    private $versionId;
    /**
     * @var Owner
     */
    private $owner;
    /**
     * @var Grant[]
     */
    private $grants;

    /**
     * @param RequestInfo $requestInfo
     * @param string $versionId
     * @param Owner $owner
     * @param Grant[] $grants
     */
    public function __construct(RequestInfo &$requestInfo, $versionId = '', Owner &$owner = null, array &$grants = [])
    {
        $this->requestInfo = $requestInfo;
        $this->versionId = $versionId;
        $this->owner = $owner;
        $this->grants = $grants;
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

    /**
     * @return Owner|null
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param Owner|null $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return Grant[]
     */
    public function getGrants()
    {
        return $this->grants;
    }

    /**
     * @param Grant[] $grants
     */
    public function setGrants($grants)
    {
        $this->grants = $grants;
    }
}