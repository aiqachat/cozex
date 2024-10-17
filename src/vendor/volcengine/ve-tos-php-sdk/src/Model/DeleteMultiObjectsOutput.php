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

class DeleteMultiObjectsOutput
{
    use RequestInfoHolder;

    /**
     * @var Deleted []
     */
    private $deleted;

    /**
     * @var DeleteError []
     */
    private $error;

    /**
     * @param RequestInfo $requestInfo
     * @param Deleted[] $deleted
     * @param DeleteError[] $error
     */
    public function __construct(RequestInfo &$requestInfo, array $deleted = [], array $error = [])
    {
        $this->requestInfo = $requestInfo;
        $this->deleted = $deleted;
        $this->error = $error;
    }

    /**
     * @return Deleted[]
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @return DeleteError[]
     */
    public function getError()
    {
        return $this->error;
    }
}