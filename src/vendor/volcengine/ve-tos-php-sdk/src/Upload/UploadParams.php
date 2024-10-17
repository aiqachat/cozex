<?php

namespace Tos\Upload;

use GuzzleHttp\Promise\PromiseInterface;
use Tos\Model\UploadedPart;
use Tos\Model\UploadFileInput;
use Tos\Model\UploadPartFromFileInput;

class UploadParams
{
    /**
     * @var UploadFileInput
     */
    public $input;
    /**
     * @var UploadPartFromFileInput[]
     */
    public $inputs;

    /**
     * @var UploadedPart[]
     */
    public $parts;
    /**
     * @var string
     */
    public $checkpointFile;
    /**
     * @var array
     */
    public $checkpointContent;
    /**
     * @var PromiseInterface
     */
    public $promise;
    /**
     * @var bool
     */
    private $canceled;

    public function rejectAndCancel($ex, $force = false)
    {
        if ($this->input->isEnableCheckpoint() && !$force) {
            return;
        }

        if ($this->canceled) {
            return;
        }

        if ($this->promise) {
            $this->promise->reject($ex);
            $this->promise->cancel();
            $this->canceled = true;
        }
    }

}