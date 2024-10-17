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

class ListedBucket
{
    /**
     * @var string
     */
    private $creationDate;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $location;

    /**
     * @var string
     */
    private $extranetEndpoint;

    /**
     * @var string
     */
    private $intranetEndpoint;

    /**
     * @param string $creationDate
     * @param string $name
     * @param string $location
     * @param string $extranetEndpoint
     * @param string $intranetEndpoint
     */
    public function __construct($creationDate = '', $name = '', $location = '', $extranetEndpoint = '', $intranetEndpoint = '')
    {
        $this->creationDate = $creationDate;
        $this->name = $name;
        $this->location = $location;
        $this->extranetEndpoint = $extranetEndpoint;
        $this->intranetEndpoint = $intranetEndpoint;
    }

    /**
     * @return string
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return string
     */
    public function getExtranetEndpoint()
    {
        return $this->extranetEndpoint;
    }

    /**
     * @return string
     */
    public function getIntranetEndpoint()
    {
        return $this->intranetEndpoint;
    }
}