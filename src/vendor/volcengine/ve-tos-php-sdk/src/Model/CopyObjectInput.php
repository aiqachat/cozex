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

class CopyObjectInput
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
    private $srcBucket;
    /**
     * @var string
     */
    private $srcKey;
    /**
     * @var string
     */
    private $srcVersionId;
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
    private $copySourceIfMatch;
    /**
     * @var int
     */
    private $copySourceIfModifiedSince;
    /**
     * @var string
     */
    private $copySourceIfNoneMatch;
    /**
     * @var int
     */
    private $copySourceIfUnmodifiedSince;
    /**
     * @var string
     */
    private $copySourceSsecAlgorithm;
    /**
     * @var string
     */
    private $copySourceSsecKey;
    /**
     * @var string
     */
    private $copySourceSsecKeyMD5;
    /**
     * @var string
     */
    private $ssecAlgorithm;
    /**
     * @var string
     */
    private $ssecKey;
    /**
     * @var string
     */
    private $ssecKeyMD5;
    /**
     * @var string
     */
    private $serverSideEncryption;

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
     * @var string
     */
    private $metadataDirective;
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
     * @var string
     */
    private $tagging;
    /**
     * @var string
     */
    private $taggingDirective;

    /**
     * @param string $bucket
     * @param string $key
     * @param string $srcBucket
     * @param string $srcKey
     * @param string $srcVersionId
     */
    public function __construct($bucket, $key, $srcBucket, $srcKey, $srcVersionId = '')
    {
        $this->bucket = $bucket;
        $this->key = $key;
        $this->srcBucket = $srcBucket;
        $this->srcKey = $srcKey;
        $this->srcVersionId = $srcVersionId;
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
    public function getSrcBucket()
    {
        return $this->srcBucket;
    }

    /**
     * @param string $srcBucket
     */
    public function setSrcBucket($srcBucket)
    {
        $this->srcBucket = $srcBucket;
    }

    /**
     * @return string
     */
    public function getSrcKey()
    {
        return $this->srcKey;
    }

    /**
     * @param string $srcKey
     */
    public function setSrcKey($srcKey)
    {
        $this->srcKey = $srcKey;
    }

    /**
     * @return string
     */
    public function getSrcVersionID()
    {
        return $this->srcVersionId;
    }

    /**
     * @param string $srcVersionId
     */
    public function setSrcVersionID($srcVersionId)
    {
        $this->srcVersionId = $srcVersionId;
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
    public function getCopySourceIfMatch()
    {
        return $this->copySourceIfMatch;
    }

    /**
     * @param string $copySourceIfMatch
     */
    public function setCopySourceIfMatch($copySourceIfMatch)
    {
        $this->copySourceIfMatch = $copySourceIfMatch;
    }

    /**
     * @return int
     */
    public function getCopySourceIfModifiedSince()
    {
        return $this->copySourceIfModifiedSince;
    }

    /**
     * @param int $copySourceIfModifiedSince
     */
    public function setCopySourceIfModifiedSince($copySourceIfModifiedSince)
    {
        $this->copySourceIfModifiedSince = $copySourceIfModifiedSince;
    }

    /**
     * @return string
     */
    public function getCopySourceIfNoneMatch()
    {
        return $this->copySourceIfNoneMatch;
    }

    /**
     * @param string $copySourceIfNoneMatch
     */
    public function setCopySourceIfNoneMatch($copySourceIfNoneMatch)
    {
        $this->copySourceIfNoneMatch = $copySourceIfNoneMatch;
    }

    /**
     * @return int
     */
    public function getCopySourceIfUnmodifiedSince()
    {
        return $this->copySourceIfUnmodifiedSince;
    }

    /**
     * @param int $copySourceIfUnmodifiedSince
     */
    public function setCopySourceIfUnmodifiedSince($copySourceIfUnmodifiedSince)
    {
        $this->copySourceIfUnmodifiedSince = $copySourceIfUnmodifiedSince;
    }

    /**
     * @return string
     */
    public function getCopySourceSSECAlgorithm()
    {
        return $this->copySourceSsecAlgorithm;
    }

    /**
     * @param string $copySourceSsecAlgorithm
     */
    public function setCopySourceSSECAlgorithm($copySourceSsecAlgorithm)
    {
        $this->copySourceSsecAlgorithm = $copySourceSsecAlgorithm;
    }

    /**
     * @return string
     */
    public function getCopySourceSSECKey()
    {
        return $this->copySourceSsecKey;
    }

    /**
     * @param string $copySourceSsecKey
     */
    public function setCopySourceSSECKey($copySourceSsecKey)
    {
        $this->copySourceSsecKey = $copySourceSsecKey;
    }

    /**
     * @return string
     */
    public function getCopySourceSSECKeyMD5()
    {
        return $this->copySourceSsecKeyMD5;
    }

    /**
     * @param string $copySourceSsecKeyMD5
     */
    public function setCopySourceSSECKeyMD5($copySourceSsecKeyMD5)
    {
        $this->copySourceSsecKeyMD5 = $copySourceSsecKeyMD5;
    }

    /**
     * @return string
     */
    public function getSSECAlgorithm()
    {
        return $this->ssecAlgorithm;
    }

    /**
     * @param string $ssecAlgorithm
     */
    public function setSSECAlgorithm($ssecAlgorithm)
    {
        $this->ssecAlgorithm = $ssecAlgorithm;
    }

    /**
     * @return string
     */
    public function getSSECKey()
    {
        return $this->ssecKey;
    }

    /**
     * @param string $ssecKey
     */
    public function setSSECKey($ssecKey)
    {
        $this->ssecKey = $ssecKey;
    }

    /**
     * @return string
     */
    public function getSSECKeyMD5()
    {
        return $this->ssecKeyMD5;
    }

    /**
     * @param string $ssecKeyMD5
     */
    public function setSSECKeyMD5($ssecKeyMD5)
    {
        $this->ssecKeyMD5 = $ssecKeyMD5;
    }

    /**
     * @return string
     */
    public function getServerSideEncryption()
    {
        return $this->serverSideEncryption;
    }

    /**
     * @param string $serverSideEncryption
     */
    public function setServerSideEncryption($serverSideEncryption)
    {
        $this->serverSideEncryption = $serverSideEncryption;
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
     * @return string
     */
    public function getMetadataDirective()
    {
        return $this->metadataDirective;
    }

    /**
     * @param string $metadataDirective
     */
    public function setMetadataDirective($metadataDirective)
    {
        $this->metadataDirective = $metadataDirective;
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

    /**
     * @return string
     */
    public function getTagging()
    {
        return $this->tagging;
    }

    /**
     * @param string $tagging
     */
    public function setTagging($tagging)
    {
        $this->tagging = $tagging;
    }

    /**
     * @return string
     */
    public function getTaggingDirective()
    {
        return $this->taggingDirective;
    }

    /**
     * @param string $taggingDirective
     */
    public function setTaggingDirective($taggingDirective)
    {
        $this->taggingDirective = $taggingDirective;
    }
}