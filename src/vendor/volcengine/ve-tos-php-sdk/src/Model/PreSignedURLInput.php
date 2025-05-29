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

class PreSignedURLInput
{
    /**
     * @var string
     */
    private $httpMethod;
    /**
     * @var string
     */
    private $bucket;
    /**
     * @var string
     */
    private $key;
    /**
     * @var int
     */
    private $expires;
    /**
     * @var string[]
     */
    private $header;
    /**
     * @var string[]
     */
    private $query;
    /**
     * @var string
     */
    private $alternativeEndpoint;
    /**
     * @var bool|null
     */
    private $isCustomDomain = null;

    /**
     * @param string $httpMethod
     * @param string $bucket
     * @param string $key
     * @param int $expires
     */
    public function __construct($httpMethod = Enum::HttpMethodGet, $bucket = '', $key = '', $expires = 3600)
    {
        $this->httpMethod = $httpMethod;
        $this->bucket = $bucket;
        $this->key = $key;
        $this->expires = $expires;
    }

    /**
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * @param string $httpMethod
     */
    public function setHttpMethod($httpMethod)
    {
        $this->httpMethod = $httpMethod;
    }

    /**
     * @return string
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @param string $bucket
     */
    public function setBucket($bucket)
    {
        $this->bucket = $bucket;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return int
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * @param int $expires
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;
    }

    /**
     * @return string[]
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param string[] $header
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     * @return string[]
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string[] $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return string
     */
    public function getAlternativeEndpoint()
    {
        return $this->alternativeEndpoint;
    }

    /**
     * @param string $alternativeEndpoint
     */
    public function setAlternativeEndpoint($alternativeEndpoint)
    {
        $this->alternativeEndpoint = $alternativeEndpoint;
    }

    /**
     * @return bool|null
     */
    public function isCustomDomain()
    {
        return $this->isCustomDomain;
    }

    /**
     * @param bool|null $isCustomDomain
     */
    public function setCustomDomain($isCustomDomain)
    {
        $this->isCustomDomain = $isCustomDomain;
    }

}