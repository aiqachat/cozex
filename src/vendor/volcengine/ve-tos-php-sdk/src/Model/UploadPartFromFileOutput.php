<?php

namespace Tos\Model;

class UploadPartFromFileOutput extends UploadPartOutput
{
    /**
     * @param RequestInfo $requestInfo
     * @param int $partNumber
     * @param string $etag
     * @param string $ssecAlgorithm
     * @param string $ssecKeyMD5
     * @param string $hashCrc64ecma
     */
    public function __construct(RequestInfo &$requestInfo, $partNumber = 1, $etag = '', $ssecAlgorithm = '', $ssecKeyMD5 = '', $hashCrc64ecma = '')
    {
        parent::__construct($requestInfo, $partNumber, $etag, $ssecAlgorithm, $ssecKeyMD5, $hashCrc64ecma);
    }
}