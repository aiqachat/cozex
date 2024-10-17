<?php

namespace Tos\Model;

class UploadFileInput extends CreateMultipartUploadInput
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * @var int
     */
    private $partSize = 20 * 1024 * 1024;

    /**
     * @var int
     */
    private $taskNum = 1;

    /**
     * @var bool
     */
    private $enableCheckpoint;

    /**
     * @var string
     */
    private $checkpointFile;

    /**
     * @param string $filePath
     */
    public function __construct($bucket = '', $key = '', $filePath = '')
    {
        parent::__construct($bucket, $key);
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
     * @return float|int
     */
    public function getPartSize()
    {
        return $this->partSize;
    }

    /**
     * @param float|int $partSize
     */
    public function setPartSize($partSize)
    {
        $this->partSize = $partSize;
    }

    /**
     * @return int
     */
    public function getTaskNum()
    {
        return $this->taskNum;
    }

    /**
     * @param int $taskNum
     */
    public function setTaskNum($taskNum)
    {
        $this->taskNum = $taskNum;
    }

    /**
     * @return bool
     */
    public function isEnableCheckpoint()
    {
        return $this->enableCheckpoint;
    }

    /**
     * @param bool $enableCheckpoint
     */
    public function setEnableCheckpoint($enableCheckpoint)
    {
        $this->enableCheckpoint = $enableCheckpoint;
    }

    /**
     * @return string
     */
    public function getCheckpointFile()
    {
        return $this->checkpointFile;
    }

    /**
     * @param string $checkpointFile
     */
    public function setCheckpointFile($checkpointFile)
    {
        $this->checkpointFile = $checkpointFile;
    }

}