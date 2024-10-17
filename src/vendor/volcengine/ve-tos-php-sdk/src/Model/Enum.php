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

class Enum
{
    const ACLPrivate = 'private';
    const ACLPublicRead = 'public-read';
    const ACLPublicReadWrite = 'public-read-write';
    const ACLAuthenticatedRead = 'authenticated-read';
    const ACLBucketOwnerRead = 'bucket-owner-read';
    const ACLBucketOwnerFullControl = 'bucket-owner-full-control';

    const StorageClassStandard = 'STANDARD';
    const StorageClassIa = 'IA';
    const StorageClassArchiveFr = 'ARCHIVE_FR';

    const MetadataDirectiveCopy = 'COPY';
    const MetadataDirectiveReplace = 'REPLACE';

    const AzRedundancySingleAz = 'single-az';
    const AzRedundancyMultiAz = 'multi-az';

    const PermissionRead = 'READ';
    const PermissionWrite = 'WRITE';
    const PermissionReadAcp = 'READ_ACP';
    const PermissionWriteAcp = 'WRITE_ACP';
    const PermissionFullControl = 'FULL_CONTROL';

    const GranteeGroup = 'Group';
    const GranteeUser = 'CanonicalUser';

    const CannedAllUsers = 'AllUsers';
    const CannedAuthenticatedUsers = 'AuthenticatedUsers';

    const HttpMethodGet = 'GET';
    const HttpMethodPut = 'PUT';
    const HttpMethodPost = 'POST';
    const HttpMethodDelete = 'DELETE';
    const HttpMethodHead = 'HEAD';

    const TaggingDirectiveCopy = 'Copy';
    const TaggingDirectiveReplace = 'Replace';
}
