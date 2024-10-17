<?php

namespace Tos\Model;

class UploadFileOutput
{
    use RequestInfoHolder;

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
    private $uploadId;
    /**
     * @var string
     */
    private $etag;
    /**
     * @var string
     */
    private $location;
    /**
     * @var string
     */
    private $versionId;
    /**
     * @var string
     */
    private $hashCrc64ecma;
    /**
     * @var string
     */
    private $ssecAlgorithm;
    /**
     * @var string
     */
    private $ssecKeyMD5;
    /**
     * @var string
     */
    private $encodingType;

    /**
     * @param RequestInfo $requestInfo
     * @param string $bucket
     * @param string $key
     * @param string $uploadId
     * @param string $etag
     * @param string $location
     * @param string $versionId
     * @param string $hashCrc64ecma
     * @param string $ssecAlgorithm
     * @param string $ssecKeyMD5
     * @param string $encodingType
     */
    public function __construct(RequestInfo &$requestInfo, $bucket = '', $key = '', $uploadId = '',
                                            $etag = '', $location = '', $versionId = '', $hashCrc64ecma = '', $ssecAlgorithm = '', $ssecKeyMD5 = '', $encodingType = '')
    {
        $this->requestInfo = $requestInfo;
        $this->bucket = $bucket;
        $this->key = $key;
        $this->uploadId = $uploadId;
        $this->etag = $etag;
        $this->location = $location;
        $this->versionId = $versionId;
        $this->hashCrc64ecma = $hashCrc64ecma;
        $this->ssecAlgorithm = $ssecAlgorithm;
        $this->ssecKeyMD5 = $ssecKeyMD5;
        $this->encodingType = $encodingType;
    }

    /**
     * @return string
     */
    public function getBucket()
    {
        return $this->bucket;
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
    public function getUploadID()
    {
        return $this->uploadId;
    }

    /**
     * @return string
     */
    public function getETag()
    {
        return $this->etag;
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
    public function getVersionID()
    {
        return $this->versionId;
    }

    /**
     * @return string
     */
    public function getHashCrc64ecma()
    {
        return $this->hashCrc64ecma;
    }

    /**
     * @return string
     */
    public function getSSECAlgorithm()
    {
        return $this->ssecAlgorithm;
    }

    /**
     * @return string
     */
    public function getSSECKeyMD5()
    {
        return $this->ssecKeyMD5;
    }

    /**
     * @return string
     */
    public function getEncodingType()
    {
        return $this->encodingType;
    }

}