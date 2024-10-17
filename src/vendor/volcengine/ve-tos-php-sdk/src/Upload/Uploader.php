<?php

namespace Tos\Upload;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tos\Exception\TosClientException;
use Tos\Exception\TosServerException;
use Tos\Model\AbortMultipartUploadInput;
use Tos\Model\CompleteMultipartUploadInput;
use Tos\Model\RequestInfo;
use Tos\Model\UploadedPart;
use Tos\Model\UploadFileInput;
use Tos\Model\UploadFileOutput;
use Tos\Model\UploadPartFromFileInput;

trait Uploader
{

    public function &uploadFile(UploadFileInput $input)
    {
        $uploadId = null;
        $checkpointFile = null;
        $needClean = false;
        try {
            list($filePath, $partSize, $taskNum, $checkpointFile) = self::transUploadFileInput($input);
            $checkpointContent = $this->loadCheckpoint($checkpointFile, $input, $filePath, $partSize);

            if (isset($checkpointContent['upload_id'])) {
                $uploadId = $checkpointContent['upload_id'];
            } else {
                $createMultipartUploadOutput = $this->createMultipartUpload($input);
                $uploadId = $createMultipartUploadOutput->getUploadID();

                $checkpointContent['upload_id'] = $uploadId;
                if ($createMultipartUploadOutput->getSSECAlgorithm()) {
                    $checkpointContent['ssec_algorithm'] = $createMultipartUploadOutput->getSSECAlgorithm();
                }
                if ($createMultipartUploadOutput->getSSECKeyMD5()) {
                    $checkpointContent['ssec_key_md5'] = $createMultipartUploadOutput->getSSECKeyMD5();
                }
                if ($createMultipartUploadOutput->getEncodingType()) {
                    $checkpointContent['encoding_type'] = $createMultipartUploadOutput->getEncodingType();
                }
                $this->writeCheckpoint($checkpointFile, $checkpointContent, $input);
            }

            $parts = $this->concurrentUploadPart($uploadId, $filePath, $partSize, $taskNum, $input, $checkpointFile, $checkpointContent);
            $completeMultipartUploadOutput = $this->completeMultipartUpload(new CompleteMultipartUploadInput($input->getBucket(), $input->getKey(), $uploadId, $parts));
            $requestInfo = new RequestInfo($completeMultipartUploadOutput->getRequestId(), $completeMultipartUploadOutput->getID2(),
                $completeMultipartUploadOutput->getStatusCode(), $completeMultipartUploadOutput->getHeader());
            $output = new UploadFileOutput($requestInfo, $input->getBucket(),
                $input->getKey(), $completeMultipartUploadOutput->getETag(),
                $completeMultipartUploadOutput->getLocation(), $completeMultipartUploadOutput->getVersionID(),
                $completeMultipartUploadOutput->getHashCrc64ecma(), $input->getSSECAlgorithm(),
                $input->getSSECKeyMD5(), $input->getEncodingType());
            $uploadId = null;
            $needClean = true;
            return $output;
        } catch (TosClientException $ex) {
            throw $ex;
        } catch (TosServerException $ex) {
            if ($ex->getStatusCode() >= 300 && $ex->getStatusCode() < 500 && $ex->getStatusCode() !== 429) {
                $needClean = true;
            }
            throw $ex;
        } catch (\Exception $ex) {
            throw new TosClientException(sprintf('unknown error, %s', $ex->getMessage()), $ex);
        } finally {
            if ($needClean || !$input->isEnableCheckpoint()) {
                if ($uploadId) {
                    $this->doCleanForUploadFile($input->getBucket(), $input->getKey(), $uploadId);
                }
                if ($input->isEnableCheckpoint() && $checkpointFile && file_exists($checkpointFile)) {
                    unlink($checkpointFile);
                }
            }
        }
    }

    private function validateCheckpoint(array &$checkpointContent, UploadFileInput &$input, $filePath, $partSize)
    {
        if (!isset($checkpointContent['upload_id']) || !$checkpointContent['upload_id']) {
            return false;
        }

        if (!isset($checkpointContent['bucket']) || $checkpointContent['bucket'] !== $input->getBucket()) {
            return false;
        }

        if (!isset($checkpointContent['key']) || $checkpointContent['key'] !== $input->getKey()) {
            return false;
        }

        if (!isset($checkpointContent['part_size']) || intval($checkpointContent['part_size']) !== $partSize) {
            return false;
        }

        if (!isset($checkpointContent['file_path']) || $checkpointContent['file_path'] !== $filePath) {
            return false;
        }


        if (!isset($checkpointContent['file_info'])) {
            return false;
        }


        if (!isset($checkpointContent['file_info']['file_size']) || intval($checkpointContent['file_info']['file_size']) !== filesize($filePath)) {
            return false;
        }

        if (!isset($checkpointContent['file_info']['last_modified']) || intval($checkpointContent['file_info']['last_modified']) !== filemtime($filePath)) {
            return false;
        }

        $expect = strval($input->getEncodingType());
        $actual = isset($checkpointContent['encoding_type']) ? $checkpointContent['encoding_type'] : '';
        if ($expect !== $actual) {
            return false;
        }

        $expect = strval($input->getSSECAlgorithm());
        $actual = isset($checkpointContent['ssec_algorithm']) ? $checkpointContent['ssec_algorithm'] : '';
        if ($expect !== $actual) {
            return false;
        }

        $expect = strval($input->getSSECKeyMD5());
        $actual = isset($checkpointContent['ssec_key_md5']) ? $checkpointContent['ssec_key_md5'] : '';
        if ($expect !== $actual) {
            return false;
        }

        if (!isset($checkpointContent['parts_info']) || !is_array($checkpointContent['parts_info'])) {
            return false;
        }

        foreach ($checkpointContent['parts_info'] as $partInfo) {
            if (!isset($partInfo['part_number']) || !isset($partInfo['part_size'])) {
                return false;
            }

            if (!isset($partInfo['offset']) || !isset($partInfo['etag'])) {
                return false;
            }

            if (!isset($partInfo['hash_crc64ecma']) || !isset($partInfo['is_completed'])) {
                return false;
            }
        }

        return true;
    }

    private function &loadCheckpoint($checkpointFile, UploadFileInput &$input, $filePath, $partSize)
    {
        if ($input->isEnableCheckpoint()) {
            if (is_file($checkpointFile)) {
                $contents = file_get_contents($checkpointFile);
                $checkpointContent = json_decode($contents, true);
                if (json_last_error() !== 0) {
                    unlink($checkpointFile);
                }

                if ($this->validateCheckpoint($checkpointContent, $input, $filePath, $partSize)) {
                    return $checkpointContent;
                }

                if (isset($checkpointContent['bucket']) && isset($checkpointContent['key']) && isset($checkpointContent['upload_id'])) {
                    $this->doCleanForUploadFile($checkpointContent['bucket'], $checkpointContent['key'], $checkpointContent['upload_id']);
                }

                unlink($checkpointFile);
            } else if (is_dir($checkpointFile)) {
                unlink($checkpointFile);
            }
        }

        $checkpointContent = [
            'bucket' => $input->getBucket(),
            'key' => $input->getKey(),
            'part_size' => $partSize,
            'file_path' => $filePath,
            'file_info' => [
                'last_modified' => filemtime($filePath),
                'file_size' => filesize($filePath),
            ],
            'parts_info' => [],
        ];

        return $checkpointContent;
    }

    private function writeCheckpoint($checkpointFile, array &$checkpointContent, UploadFileInput &$input)
    {
        if (!$input->isEnableCheckpoint()) {
            return;
        }

        $contents = json_encode($checkpointContent);
        if (!$contents || json_last_error() !== 0) {
            throw new TosClientException(sprintf('unable to do serialization/deserialization to create checkpoint file, %s', json_last_error_msg()));
        }
        file_put_contents($checkpointFile, $contents);
    }

    private function doCleanForUploadFile($bucket, $key, $uploadId)
    {
        try {
            $this->abortMultipartUpload(new AbortMultipartUploadInput($bucket, $key, $uploadId));
        } catch (\Exception $ex) {
        }
    }

    private function &concurrentUploadPart($uploadId, $filePath, $partSize, $taskNum, UploadFileInput &$input, $checkpointFile, array &$checkpointContent)
    {
        $fileSize = filesize($filePath);
        if ($fileSize === 0) {
            $partCount = 1;
            $lastPartSize = 0;
        } else {
            $partCount = intval($fileSize / $partSize);
            if (($lastPartSize = $fileSize % $partSize) !== 0) {
                $partCount++;
            } else {
                $lastPartSize = $partSize;
            }
        }

        $inputs = [];
        $requests = [];
        $partInfoMap = [];
        foreach ($checkpointContent['parts_info'] as $partInfo) {
            $partInfoMap[$partInfo['part_number']] = $partInfo;
        }

        $parts = [];
        $partInfos = [];
        for ($i = 0; $i < $partCount; $i++) {
            $partNumber = $i + 1;
            $offset = intval($partSize * $i);
            $ps = $i === $partCount - 1 ? intval($lastPartSize) : intval($partSize);
            if (isset($partInfoMap[$partNumber]) && ($partInfo = $partInfoMap[$partNumber])
                && $partInfo['is_completed'] && intval($partInfo['part_size']) === $ps && intval($partInfo['offset']) === $offset) {
                $parts[] = new UploadedPart($partNumber, $partInfo['etag']);
                $partInfos[] = $partInfo;
                continue;
            }
            $uinput = new UploadPartFromFileInput($input->getBucket(), $input->getKey(), $uploadId, $partNumber, $filePath);
            $uinput->setSSECAlgorithm($input->getSSECAlgorithm());
            $uinput->setSSECKey($input->getSSECKey());
            $uinput->setSSECKeyMD5($input->getSSECKeyMD5());
            $uinput->setOffset($offset);
            $uinput->setPartSize($ps);
            $inputs[] = $uinput;

            $request = $this->transUploadPartFromFileInput($uinput);
            list($method, $requestUri, $headers, $body) = $this->prepareRequest($request);
            $requests[] = new Request($method, $requestUri, $headers, $body);
        }

        $checkpointContent['parts_info'] = $partInfos;
        $params = new UploadParams();
        $params->input = $input;
        $params->inputs = $inputs;
        $params->parts = $parts;
        $params->checkpointFile = $checkpointFile;
        $params->checkpointContent = $checkpointContent;

        $firstEx = null;

        $pool = new Pool($this->client, $requests, [
            'concurrency' => $taskNum,
            'fulfilled' => function (Response $response, $index) use (&$params, &$firstEx) {
                try {
                    $uinput = $params->inputs[$index];
                    $partNumber = $uinput->getPartNumber();
                    $uploadPartOutput = $this->parseUploadPartFromFileOutput($response, $partNumber);
                    $params->parts[] = new UploadedPart($partNumber, $uploadPartOutput->getETag());
                    $params->checkpointContent['parts_info'][] = [
                        'part_number' => $partNumber,
                        'part_size' => $uinput->getPartSize(),
                        'offset' => $uinput->getOffset(),
                        'etag' => $uploadPartOutput->getETag(),
                        'hash_crc64ecma' => $uploadPartOutput->getHashCrc64ecma(),
                        'is_completed' => true,
                    ];
                    $this->writeCheckpoint($params->checkpointFile, $params->checkpointContent, $params->input);
                } catch (TosClientException $ex) {
                    if (!$firstEx) {
                        $firstEx = $ex;
                    }
                    $params->rejectAndCancel($ex);
                } catch (TosServerException $ex) {
                    if (!$firstEx) {
                        $firstEx = $ex;
                    }
                    $params->rejectAndCancel($ex, $ex->getStatusCode() >= 300
                        && $ex->getStatusCode() < 500 && $ex->getStatusCode() !== 429);
                }
            },
            'rejected' => function (RequestException $ex, $index) use (&$params, &$firstEx) {
                if (!$firstEx) {
                    $firstEx = $ex;
                }
                $params->rejectAndCancel($ex, $ex->hasResponse() && $ex->getResponse()->getStatusCode() >= 300
                    && $ex->getResponse()->getStatusCode() < 500 && $ex->getResponse()->getStatusCode() !== 429);
            },
        ]);

        $promise = $pool->promise();
        $params->promise = $promise;
        $promise->wait();

        if ($firstEx) {
            throw $firstEx;
        }
        return $params->parts;
    }
}