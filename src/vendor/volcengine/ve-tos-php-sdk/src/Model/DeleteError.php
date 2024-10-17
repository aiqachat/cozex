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

class DeleteError
{
    /**
     * @var string
     */
    private $key;
    /**
     * @var string
     */
    private $versionId;
    /**
     * @var string
     */
    private $code;
    /**
     * @var string
     */
    private $message;

    /**
     * @param string $key
     * @param string $versionId
     * @param string $code
     * @param string $message
     */
    public function __construct($key = '', $versionId = '', $code = '', $message = '')
    {
        $this->key = $key;
        $this->versionId = $versionId;
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getVersionID()
    {
        return $this->versionId;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}