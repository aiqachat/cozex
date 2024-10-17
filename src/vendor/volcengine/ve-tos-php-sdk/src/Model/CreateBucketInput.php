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

class CreateBucketInput
{
    /**
     * @var string
     */
    private $bucket;
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
    private $grantWrite;
    /**
     * @var string
     */
    private $grantWriteAcp;
    /**
     * @var string
     */
    private $storageClass;
    /**
     * @var string
     */
    private $azRedundancy;

    /**
     * @param string $bucket
     * @param string $acl
     * @param string $grantFullControl
     * @param string $grantRead
     * @param string $grantReadAcp
     * @param string $grantWrite
     * @param string $grantWriteAcp
     * @param string $storageClass
     * @param string $azRedundancy
     */
    public function __construct($bucket = '', $acl = '', $grantFullControl = '',
                                $grantRead = '', $grantReadAcp = '', $grantWrite = '', $grantWriteAcp = '', $storageClass = '', $azRedundancy = '')
    {
        $this->bucket = $bucket;
        $this->acl = $acl;
        $this->grantFullControl = $grantFullControl;
        $this->grantRead = $grantRead;
        $this->grantReadAcp = $grantReadAcp;
        $this->grantWrite = $grantWrite;
        $this->grantWriteAcp = $grantWriteAcp;
        $this->storageClass = $storageClass;
        $this->azRedundancy = $azRedundancy;
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
    public function getGrantWrite()
    {
        return $this->grantWrite;
    }

    /**
     * @param string $grantWrite
     */
    public function setGrantWrite($grantWrite)
    {
        $this->grantWrite = $grantWrite;
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
     * @return string
     */
    public function getStorageClass()
    {
        return $this->storageClass;
    }

    /**
     * @param string $storageClass
     */
    public function setStorageClass($storageClass)
    {
        $this->storageClass = $storageClass;
    }

    /**
     * @return string
     */
    public function getAzRedundancy()
    {
        return $this->azRedundancy;
    }

    /**
     * @param string $azRedundancy
     */
    public function setAzRedundancy($azRedundancy)
    {
        $this->azRedundancy = $azRedundancy;
    }
}