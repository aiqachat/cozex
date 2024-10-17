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

class PutObjectACLInput
{
    /**
     * @var string
     */
    private $bucket;
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
    private $acl;
    /**
     * @var string
     */
    private $grantFullControl;
    /**
     * @var string
     */
    private $grantRead;
    /**
     * @var string
     */
    private $grantReadAcp;
    /**
     * @var string
     */
    private $grantWriteAcp;
    /**
     * @var Owner
     */
    private $owner;
    /**
     * @var Grant[]
     */
    private $grants;

    /**
     * @param string $bucket
     * @param string $key
     * @param string $acl
     * @param string $versionId
     */
    public function __construct($bucket = '', $key = '', $acl = '', $versionId = '')
    {
        $this->bucket = $bucket;
        $this->key = $key;
        $this->acl = $acl;
        $this->versionId = $versionId;
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
     * @return string
     */
    public function getACL()
    {
        return $this->acl;
    }

    /**
     * @param string $acl
     */
    public function setACL($acl)
    {
        $this->acl = $acl;
    }

    /**
     * @return string
     */
    public function getGrantFullControl()
    {
        return $this->grantFullControl;
    }

    /**
     * @param string $grantFullControl
     */
    public function setGrantFullControl($grantFullControl)
    {
        $this->grantFullControl = $grantFullControl;
    }

    /**
     * @return string
     */
    public function getGrantRead()
    {
        return $this->grantRead;
    }

    /**
     * @param string $grantRead
     */
    public function setGrantRead($grantRead)
    {
        $this->grantRead = $grantRead;
    }

    /**
     * @return string
     */
    public function getGrantReadAcp()
    {
        return $this->grantReadAcp;
    }

    /**
     * @param string $grantReadAcp
     */
    public function setGrantReadAcp($grantReadAcp)
    {
        $this->grantReadAcp = $grantReadAcp;
    }

    /**
     * @return string
     */
    public function getGrantWriteAcp()
    {
        return $this->grantWriteAcp;
    }

    /**
     * @param string $grantWriteAcp
     */
    public function setGrantWriteAcp($grantWriteAcp)
    {
        $this->grantWriteAcp = $grantWriteAcp;
    }

    /**
     * @return Owner
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param Owner $owner
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