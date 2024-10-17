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

class ListBucketsOutput
{
    use RequestInfoHolder;

    /**
     * @var ListedBucket[]
     */
    private $buckets;

    /**
     * @var Owner
     */
    private $owner;

    /**
     * @param RequestInfo $requestInfo
     * @param array $buckets
     * @param Owner|null $owner
     */
    public function __construct(RequestInfo &$requestInfo, array &$buckets = [], Owner &$owner = null)
    {
        $this->requestInfo = $requestInfo;
        $this->buckets = $buckets;
        $this->owner = $owner;
    }

    /**
     * @return ListedBucket[]
     */
    public function getBuckets()
    {
        return $this->buckets;
    }

    /**
     * @return Owner|null
     */
    public function getOwner()
    {
        return $this->owner;
    }
}