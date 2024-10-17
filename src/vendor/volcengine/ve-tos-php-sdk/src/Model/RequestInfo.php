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

class RequestInfo
{
    /**
     * @var string
     */
    protected $requestId;

    /**
     * @var string
     */
    protected $id2;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var string[][]
     */
    protected $header;

    /**
     * @param string $requestId
     * @param string $id2
     * @param int $statusCode
     * @param array $header
     */
    public function __construct($requestId = '', $id2 = '', $statusCode = 0, array $header = [])
    {
        $this->requestId = $requestId;
        $this->id2 = $id2;
        $this->statusCode = $statusCode;
        $this->header = $header;
    }

    /**
     * @return string
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * @return string
     */
    public function getID2()
    {
        return $this->id2;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return string[][]
     */
    public function getHeader()
    {
        return $this->header;
    }

}