<?php

namespace Tos\Model;

class UploadPartFromFileInput extends UploadPartBasicInput
{
    /**
     * @var string
     */
    private $filePath;
    /**
     * @var int
     */
    private $offset;
    /**
     * @var int
     */
    private $partSize;

    /**
     * @param string $bucket
     * @param string $key
     * @param string $uploadId
     * @param int $partNumber
     * @param string $filePath
     */
    public function __construct($bucket = '', $key = '', $uploadId = '', $partNumber = 1, $filePath = '')
    {
        parent::__construct($bucket, $key, $uploadId, $partNumber);
        $this->filePath = $filePath;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
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
     * @return int
     */
    public function getPartSize()
    {
        return $this->partSize;
    }

    /**
     * @param int $partSize
     */
    public function setPartSize($partSize)
    {
        $this->partSize = $partSize;
    }

}