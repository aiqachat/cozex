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

use Tos\Exception\TosClientException;

trait EnumChecker
{
    protected static function checkAcl($acl)
    {
        switch ($acl) {
            case Enum::ACLPrivate:
            case Enum::ACLPublicRead:
            case Enum::ACLPublicReadWrite:
            case Enum::ACLAuthenticatedRead:
            case Enum::ACLBucketOwnerRead:
            case Enum::ACLBucketOwnerFullControl:
                return true;
            default:
                throw new TosClientException('invalid acl type');
        }
    }

    protected static function checkStorageClass($storageClass)
    {
        switch ($storageClass) {
            case Enum::StorageClassStandard:
            case Enum::StorageClassIa:
            case Enum::StorageClassArchiveFr:
                return true;
            default:
                throw new TosClientException('invalid storage class type');
        }
    }

    protected static function checkMetadataDirective($metadataDirective)
    {
        switch ($metadataDirective) {
            case Enum::MetadataDirectiveCopy:
            case Enum::MetadataDirectiveReplace:
                return true;
            default:
                throw new TosClientException('invalid metadata directive type');
        }
    }

    protected static function checkAzRedundancy($azRedundancy)
    {
        switch ($azRedundancy) {
            case Enum::AzRedundancySingleAz:
            case Enum::AzRedundancyMultiAz:
                return true;
            default:
                throw new TosClientException('invalid az redundancy type');
        }
    }

    protected static function checkPermission($permission)
    {
        switch ($permission) {
            case Enum::PermissionRead:
            case Enum::PermissionWrite:
            case Enum::PermissionReadAcp:
            case Enum::PermissionWriteAcp:
            case Enum::PermissionFullControl:
                return true;
            default:
                throw new TosClientException('invalid permission type');
        }
    }

    protected static function checkGrantee($grantee)
    {
        switch ($grantee) {
            case Enum::GranteeGroup:
            case Enum::GranteeUser:
                return true;
            default:
                throw new TosClientException('invalid grantee type');
        }
    }

    protected static function checkCanned($canned)
    {
        switch ($canned) {
            case Enum::CannedAllUsers:
            case Enum::CannedAuthenticatedUsers:
                return true;
            default:
                throw new TosClientException('invalid canned type');
        }
    }

    protected static function checkHttpMethod($httpMethod)
    {
        switch ($httpMethod) {
            case Enum::HttpMethodGet:
            case Enum::HttpMethodPut:
            case Enum::HttpMethodPost:
            case Enum::HttpMethodDelete:
            case Enum::HttpMethodHead:
                return true;
            default:
                throw new TosClientException('invalid http method type');
        }
    }

    protected static function checkSSECAlgorithm($ssecAlgorithm)
    {
        if ($ssecAlgorithm !== 'AES256') {
            throw new TosClientException('invalid encryption-decryption algorithm');
        }
        return true;
    }


    protected static function checkServerSideEncryption($serverSideEncryption)
    {
        if ($serverSideEncryption !== 'AES256') {
            throw new TosClientException('invalid encryption-decryption algorithm');
        }
        return true;
    }

    protected static function checkEncodingType($encodingType)
    {
        if ($encodingType !== 'url') {
            throw new TosClientException('invalid encoding type');
        }
        return true;
    }

    protected static function checkTaggingDirectiveType($taggingDirective)
    {
        switch ($taggingDirective) {
            case Enum::TaggingDirectiveCopy:
            case Enum::TaggingDirectiveReplace:
                return true;
            default:
                throw new TosClientException('invalid tagging directive type');
        }
    }

}