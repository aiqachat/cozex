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

use Psr\Http\Message\StreamInterface;

class AppendObjectInput
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
     * @var int
     */
    private $offset;
    /**
     * @var StreamInterface
     */
    private $content;
    /**
     * @var int
     */
    private $contentLength;
    /**
     * @var string
     */
    private $cacheControl;
    /**
     * @var string
     */
    private $contentDisposition;
    /**
     * @var string
     */
    private $contentEncoding;
    /**
     * @var string
     */
    private $contentLanguage;
    /**
     * @var string
     */
    private $contentType;
    /**
     * @var int
     */
    private $expires;
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
     * @var string[]
     */
    private $meta;
    /**
     * @var string
     */
    private $websiteRedirectLocation;
    /**
     * @var string
     */
    private $storageClass;

    /**
     * @param string $bucket
     * @param string $key
     * @param int $offset
     * @param StreamInterface|string|null $content
     */
    public function __construct($bucket = '', $key = '', $offset = 0, $content = null)
    {
        $this->bucket = $bucket;
        $this->key = $key;
        $this->offset = $offset;
        $this->content = $content;
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
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return StreamInterface|string|null
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param StreamInterface|string|null $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return int
     */
    public function getContentLength()
    {
        return $this->contentLength;
    }

    /**
     * @param int $contentLength
     */
    public function setContentLength($contentLength)
    {
        $this->contentLength = $contentLength;
    }

    /**
     * @return string
     */
    public function getCacheControl()
    {
        return $this->cacheControl;
    }

    /**
     * @param string $cacheControl
     */
    public function setCacheControl($cacheControl)
    {
        $this->cacheControl = $cacheControl;
    }

    /**
     * @return string
     */
    public function getContentDisposition()
    {
        return $this->contentDisposition;
    }

    /**
     * @param string $contentDisposition
     */
    public function setContentDisposition($contentDisposition)
    {
        $this->contentDisposition = $contentDisposition;
    }

    /**
     * @return string
     */
    public function getContentEncoding()
    {
        return $this->contentEncoding;
    }

    /**
     * @param string $contentEncoding
     */
    public function setContentEncoding($contentEncoding)
    {
        $this->contentEncoding = $contentEncoding;
    }

    /**
     * @return string
     */
    public function getContentLanguage()
    {
        return $this->contentLanguage;
    }

    /**
     * @param string $contentLanguage
     */
    public function setContentLanguage($contentLanguage)
    {
        $this->contentLanguage = $contentLanguage;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
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
     * @return string[]
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param string[] $meta
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;
    }

    /**
     * @return string
     */
    public function getWebsiteRedirectLocation()
    {
        return $this->websiteRedirectLocation;
    }

    /**
     * @param string $websiteRedirectLocation
     */
    public function setWebsiteRedirectLocation($websiteRedirectLocation)
    {
        $this->websiteRedirectLocation = $websiteRedirectLocation;
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
}