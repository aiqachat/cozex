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

class UploadPartInput extends UploadPartBasicInput
{
    /**
     * @var StreamInterface
     */
    private $content;
    /**
     * @var int
     */
    private $contentLength;

    /**
     *
     * @param string $bucket
     * @param string $key
     * @param string $uploadId
     * @param int $partNumber
     * @param StreamInterface|string|null $content
     */
    public function __construct($bucket = '', $key = '', $uploadId = '', $partNumber = 1, $content = null)
    {
        parent::__construct($bucket, $key, $uploadId, $partNumber);
        $this->content = $content;
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
}