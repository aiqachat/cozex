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

namespace Tos\Exception;

use Tos\Model\RequestInfo;
use Tos\Model\RequestInfoHolder;

class TosServerException extends TosException
{
    use RequestInfoHolder;

    /**
     * @var string
     */
    private $errorCode;

    /**
     * @var string
     */
    private $hostId;

    /**
     * @var string
     */
    private $resource;

    public function __construct(RequestInfo &$requestInfo = null, $errorCode = '', $message = '',
                                            $hostId = '', $resource = '')
    {
        parent::__construct($message);
        $this->requestInfo = $requestInfo;
        $this->errorCode = $errorCode;
        $this->hostId = $hostId;
        $this->resource = $resource;
    }


    /**
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return string
     */
    public function getHostId()
    {
        return $this->hostId;
    }

    /**
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }
}