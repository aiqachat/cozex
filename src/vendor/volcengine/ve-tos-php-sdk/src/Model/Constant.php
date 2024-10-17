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

class Constant
{
    const HeaderPrefix = 'x-tos-';
    const HeaderPrefixMeta = 'x-tos-meta-';
    const HeaderAcl = Constant::HeaderPrefix . 'acl';
    const HeaderStorageClass = Constant::HeaderPrefix . 'storage-class';
    const HeaderWebsiteRedirectLocation = Constant::HeaderPrefix . 'website-redirect-location';
    const HeaderAzRedundancy = Constant::HeaderPrefix . 'az-redundancy';
    const HeaderGrantFullControl = Constant::HeaderPrefix . 'grant-full-control';
    const HeaderGrantRead = Constant::HeaderPrefix . 'grant-read';
    const HeaderGrantReadAcp = Constant::HeaderPrefix . 'grant-read-acp';
    const HeaderGrantWrite = Constant::HeaderPrefix . 'grant-write';
    const HeaderGrantWriteAcp = Constant::HeaderPrefix . 'grant-write-acp';

    const HeaderSecurityToken = Constant::HeaderPrefix . 'security-token';
    const HeaderRequestDate = Constant::HeaderPrefix . 'date';
    const HeaderBucketRegion = Constant::HeaderPrefix . 'bucket-region';
    const HeaderSSECAlgorithm = Constant::HeaderPrefix . 'server-side-encryption-customer-algorithm';
    const HeaderSSECKey = Constant::HeaderPrefix . 'server-side-encryption-customer-key';
    const HeaderSSECKeyMD5 = Constant::HeaderPrefix . 'server-side-encryption-customer-key-MD5';
    const HeaderCopySourceSSECAlgorithm = Constant::HeaderPrefix . 'copy-source-server-side-encryption-customer-algorithm';
    const HeaderCopySourceSSECKey = Constant::HeaderPrefix . 'copy-source-server-side-encryption-customer-key';
    const HeaderCopySourceSSECKeyMD5 = Constant::HeaderPrefix . 'copy-source-server-side-encryption-customer-key-MD5';

    const HeaderServerSideEncryption = Constant::HeaderPrefix . 'server-side-encryption';
    const HeaderVersionId = Constant::HeaderPrefix . 'version-id';
    const HeaderCopySourceVersionId = Constant::HeaderPrefix . 'copy-source-version-id';
    const HeaderHashCrc64ecma = Constant::HeaderPrefix . 'hash-crc64ecma';
    const HeaderRequestId = Constant::HeaderPrefix . 'request-id';
    const HeaderId2 = Constant::HeaderPrefix . 'id-2';
    const HeaderDeleteMarker = Constant::HeaderPrefix . 'delete-marker';
    const HeaderObjectType = Constant::HeaderPrefix . 'object-type';
    const HeaderNextAppendOffset = Constant::HeaderPrefix . 'next-append-offset';

    const HeaderCopySourceIfMatch = Constant::HeaderPrefix . 'copy-source-if-match';
    const HeaderCopySourceIfModifiedSince = Constant::HeaderPrefix . 'copy-source-if-modified-since';
    const HeaderCopySourceIfNoneMatch = Constant::HeaderPrefix . 'copy-source-if-none-match';
    const HeaderCopySourceIfUnmodifiedSince = Constant::HeaderPrefix . 'copy-source-if-unmodified-since';
    const HeaderCopySource = Constant::HeaderPrefix . 'copy-source';
    const HeaderCopySourceRange = Constant::HeaderPrefix . 'copy-source-range';
    const HeaderMetadataDirective = Constant::HeaderPrefix . 'metadata-directive';
    const HeaderTagging = Constant::HeaderPrefix . 'tagging';

    const HeaderTaggingDirective = Constant::HeaderPrefix . 'tagging-directive';
    const HeaderUserAgent = 'User-Agent';
    const HeaderConnection = 'Connection';
    const HeaderContentLength = 'Content-Length';
    const HeaderContentMD5 = 'Content-MD5';
    const HeaderCacheControl = 'Cache-Control';
    const HeaderContentEncoding = 'Content-Encoding';
    const HeaderContentDisposition = 'Content-Disposition';
    const HeaderContentLanguage = 'Content-Language';
    const HeaderContentType = 'Content-Type';
    const HeaderContentTypeLower = 'content-type';
    const HeaderExpires = 'Expires';
    const HeaderContentSHA256 = Constant::HeaderPrefix . 'content-sha256';
    const HeaderHost = 'Host';
    const HeaderHostLower = 'host';
    const HeaderLocation = 'Location';
    const HeaderAuthorization = 'Authorization';
    const HeaderETag = 'ETag';
    const HeaderIfMatch = 'If-Match';
    const HeaderIfModifiedSince = 'If-Modified-Since';
    const HeaderIfNoneMatch = 'If-None-Match';
    const HeaderIfUnmodifiedSince = 'If-Unmodified-Since';
    const HeaderContentRange = 'Content-Range';
    const HeaderLastModified = 'Last-Modified';
    const HeaderRange = 'Range';

    const QueryPrefix = Constant::HeaderPrefix;
    const QueryVersionId = 'versionId';
    const QueryPartNumber = 'partNumber';
    const QueryResponseCacheControl = 'response-cache-control';
    const QueryResponseContentDisposition = 'response-content-disposition';
    const QueryResponseContentEncoding = 'response-content-encoding';
    const QueryResponseContentLanguage = 'response-content-language';
    const QueryResponseContentType = 'response-content-type';
    const QueryResponseExpires = 'response-expires';

    const QueryProcess = Constant::QueryPrefix . 'process';
    const QuerySaveBucket = Constant::QueryPrefix . 'save-bucket';

    const QuerySaveObject = Constant::QueryPrefix . 'save-object';
}