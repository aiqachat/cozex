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

class Grant
{
    /**
     * @var Grantee
     */
    private $grantee;
    /**
     * @var string
     */
    private $permission;

    /**
     * @param Grantee $grantee
     * @param string $permission
     */
    public function __construct(Grantee $grantee = null, $permission = '')
    {
        $this->grantee = $grantee;
        $this->permission = $permission;
    }

    /**
     * @return Grantee|null
     */
    public function getGrantee()
    {
        return $this->grantee;
    }

    /**
     * @param Grantee|null $grantee
     */
    public function setGrantee($grantee)
    {
        $this->grantee = $grantee;
    }

    /**
     * @return string
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * @param string $permission
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;
    }
}