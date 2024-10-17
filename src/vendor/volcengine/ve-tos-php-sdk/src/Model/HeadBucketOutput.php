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

class HeadBucketOutput
{
    use RequestInfoHolder;

    /**
     * @var string
     */
    private $region;

    /**
     * @var string
     */
    private $storageClass;

    /**
     * @var string
     */
    private $azRedundancyType;

    /**
     * @param RequestInfo $requestInfo
     * @param string $region
     * @param string $storageClass
     * @param string $azRedundancyType
     */
    public function __construct(RequestInfo &$requestInfo, $region = '', $storageClass = '', $azRedundancyType = '')
    {
        $this->requestInfo = $requestInfo;
        $this->region = $region;
        $this->storageClass = $storageClass;
        $this->azRedundancyType = $azRedundancyType;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @return string
     */
    public function getStorageClass()
    {
        return $this->storageClass;
    }

    /**
     * @return string
     */
    public function getAzRedundancyType()
    {
        return $this->azRedundancyType;
    }
}