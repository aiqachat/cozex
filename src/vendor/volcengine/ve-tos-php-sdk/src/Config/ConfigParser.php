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

namespace Tos\Config;

use Tos\Exception\TosClientException;
use Tos\Helper\Helper;

class ConfigParser
{
    /**
     * @var array
     */
    private static $endpoints = [
        'cn-beijing' => 'tos-cn-beijing.volces.com',
        'cn-guangzhou' => 'tos-cn-guangzhou.volces.com',
        'cn-shanghai' => 'tos-cn-shanghai.volces.com',
        'ap-southeast-1' => 'tos-ap-southeast-1.volces.com',
    ];
    /**
     * @var string
     */
    private $version = '2.1.12';

    /**
     * @var string
     */
    private $userAgent;

    /**
     * @var string
     */
    private $ak;
    /**
     * @var string
     */
    private $sk;
    /**
     * @var string
     */
    private $securityToken;
    /**
     * @var string
     */
    private $schema;
    /**
     * @var string
     */
    private $domain;
    /**
     * @var string
     */
    private $region;
    /**
     * @var float
     */
    private $connectionTimeout = 10000;
    /**
     * @var float
     */
    private $socketTimeout = 30000;
    /**
     * @var bool
     */
    private $enableVerifySSL = true;
    /**
     * @var bool
     */
    private $autoRecognizeContentType = true;
    /**
     * @var bool
     */
    private $isCustomDomain = false;

    public function __construct(array $config)
    {
        if (!isset($config['region'])) {
            throw new TosClientException('empty region');
        }

        $this->region = trim(strval($config['region']));
        if ($this->region === '') {
            throw new TosClientException('empty region');
        }

        $endpoint = '';
        if (isset($config['endpoint'])) {
            $endpoint = trim(strval($config['endpoint']));
        }

        if ($endpoint === '' && isset(self::$endpoints[$this->region])) {
            $endpoint = self::$endpoints[$this->region];
        }

        if ($endpoint === '') {
            throw new TosClientException('no endpoint specified');
        }

        $result = Helper::splitEndpoint($endpoint);
        $this->schema = $result['schema'];
        $this->domain = $result['domain'];

        if (isset($config['ak'])) {
            $this->ak = trim(strval($config['ak']));
        }

        if (isset($config['sk'])) {
            $this->sk = trim(strval($config['sk']));
        }

        if (isset($config['securityToken'])) {
            $this->securityToken = trim(strval($config['securityToken']));
        }

        if (isset($config['connectionTimeout']) && ($connectionTimeout = floatval($config['connectionTimeout'])) >= 0) {
            $this->connectionTimeout = $connectionTimeout;
        }

        if (isset($config['socketTimeout']) && ($socketTimeout = floatval($config['socketTimeout'])) >= 0) {
            $this->socketTimeout = $socketTimeout;
        }

        if (isset($config['enableVerifySSL'])) {
            $this->enableVerifySSL = boolval($config['enableVerifySSL']);
        }

        if (isset($config['autoRecognizeContentType'])) {
            $this->autoRecognizeContentType = boolval($config['autoRecognizeContentType']);
        }

        if (isset($config['isCustomDomain'])) {
            $this->isCustomDomain = boolval($config['isCustomDomain']);
        }

        $this->userAgent = 've-tos-php-sdk/' . $this->version . ' (' . PHP_OS . '/' . php_uname('m') . ';' . PHP_VERSION . ')';
    }

    /**
     * @return string
     */
    public function getEndpoint($bucket = '', $key = '', $schema = '', $domain = '', $mustAddKey = false, $isCustomDomain = null)
    {
        if($isCustomDomain === null){
            $isCustomDomain = $this->isCustomDomain;
        }

        if (!$schema) {
            $schema = $this->schema;
        }

        if (!$domain) {
            $domain = $this->domain;
        }

        $endpoint = $schema;
        if (($bkt = strval($bucket)) !== '' && !$isCustomDomain) {
            $endpoint .= $bkt . '.' . $domain;
            if ($key !== '') {
                $endpoint .= '/' . Helper::urlencodeWithSafe($key);
            }
        } else {
            $endpoint .= $domain;
            if ($mustAddKey && $key !== '') {
                $endpoint .= '/' . Helper::urlencodeWithSafe($key);
            }
        }
        return $endpoint;
    }

    /**
     * @return string
     */
    public function getHost($bucket = '', $domain = '', $isCustomDomain = null)
    {
        if($isCustomDomain === null){
            $isCustomDomain = $this->isCustomDomain;
        }

        if (!$domain) {
            $domain = $this->domain;
        }
        if (($bkt = strval($bucket)) !== '' && !$isCustomDomain) {
            $host = $bkt . '.' . $domain;
        } else {
            $host = $domain;
        }
        return $host;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @return string
     */
    public function getAk()
    {
        return $this->ak;
    }

    /**
     * @return string
     */
    public function getSk()
    {
        return $this->sk;
    }

    /**
     * @return string
     */
    public function getSecurityToken()
    {
        return $this->securityToken;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @return float|int
     */
    public function getConnectionTimeout()
    {
        return $this->connectionTimeout;
    }


    /**
     * @return float|int
     */
    public function getSocketTimeout()
    {
        return $this->socketTimeout;
    }

    /**
     * @return bool
     */
    public function isEnableVerifySSL()
    {
        return $this->enableVerifySSL;
    }

    /**
     * @return bool
     */
    public function isAutoRecognizeContentType()
    {
        return $this->autoRecognizeContentType;
    }

    /**
     * @return bool
     */
    public function isCustomDomain()
    {
        return $this->isCustomDomain;
    }
}