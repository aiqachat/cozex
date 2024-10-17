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

use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Tos\Exception\TosClientException;
use Tos\Exception\TosServerException;
use Tos\Helper\StreamReader;

trait OutputParser
{

    protected static function &parseCreateBucketOutput(ResponseInterface &$response)
    {
        $requestInfo = self::getRequestInfo($response);
        self::checkResponse($response, $requestInfo);
        $output = new CreateBucketOutput($requestInfo, self::getHeaderLine($response, Constant::HeaderLocation));
        return $output;
    }

    protected static function &parseHeadBucketOutput(ResponseInterface &$response)
    {
        $requestInfo = self::getRequestInfo($response);
        self::checkResponse($response, $requestInfo);
        $output = new HeadBucketOutput($requestInfo, self::getHeaderLine($response, Constant::HeaderBucketRegion),
            self::getHeaderLine($response, Constant::HeaderStorageClass), self::getHeaderLine($response, Constant::HeaderAzRedundancy));
        return $output;
    }

    protected static function &parseDeleteBucketOutput(ResponseInterface &$response)
    {
        $requestInfo = self::getRequestInfo($response);
        self::checkResponse($response, $requestInfo);
        $output = new DeleteBucketOutput($requestInfo);
        return $output;
    }

    protected static function &parseListBucketsOutput(ResponseInterface &$response)
    {
        $requestInfo = self::getRequestInfo($response);
        $result = self::checkResponse($response, $requestInfo);
        $buckets = [];
        if (isset($result['Buckets']) && is_array($result['Buckets'])) {
            foreach ($result['Buckets'] as $item) {
                $buckets[] = new ListedBucket(isset($item['CreationDate']) ? strval($item['CreationDate']) : '',
                    isset($item['Name']) ? strval($item['Name']) : '',
                    isset($item['Location']) ? strval($item['Location']) : '',
                    isset($item['ExtranetEndpoint']) ? strval($item['ExtranetEndpoint']) : '',
                    isset($item['IntranetEndpoint']) ? strval($item['IntranetEndpoint']) : '');
            }
        }

        $output = new ListBucketsOutput($requestInfo, $buckets, self::transOwner($result));
        return $output;
    }

    protected static function &parseCopyObjectOutput(ResponseInterface &$response)
    {
        $requestInfo = self::getRequestInfo($response);
        $result = self::checkResponse($response, $requestInfo);
        if (isset($result['ETag'])) {
            $output = new CopyObjectOutput($requestInfo, strval($result['ETag']),
                self::transISO8601TimeInArray($result, 'LastModified'),
                self::getHeaderLine($response, Constant::HeaderCopySourceVersionId),
                self::getHeaderLine($response, Constant::HeaderVersionId),
                self::getHeaderLine($response, Constant::HeaderSSECAlgorithm),
                self::getHeaderLine($response, Constant::HeaderSSECKeyMD5));
            return $output;
        }

        throw new TosServerException($requestInfo,
            isset($error['Code']) ? $error['Code'] : '',
            isset($error['Message']) ? $error['Message'] : 'copy object does not return etag expectly',
            isset($error['HostId']) ? $error['HostId'] : '',
            isset($error['Resource']) ? $error['Resource'] : '');
    }

    protected static function &parsePutObjectOutput(ResponseInterface &$response)
    {
        return self::parsePutObjectFromFileOutput($response);
    }

    protected static function &parsePutObjectFromFileOutput(ResponseInterface &$response)
    {
        $requestInfo = self::getRequestInfo($response);
        self::checkResponse($response, $requestInfo);

        $output = new PutObjectFromFileOutput($requestInfo, self::getHeaderLine($response, Constant::HeaderETag),
            self::getHeaderLine($response, Constant::HeaderSSECAlgorithm), self::getHeaderLine($response, Constant::HeaderSSECKeyMD5),
            self::getHeaderLine($response, Constant::HeaderVersionId), self::getHeaderLine($response, Constant::HeaderHashCrc64ecma));
        return $output;
    }

    protected static function &parseGetObjectOutput(ResponseInterface &$response, $stream = false)
    {
        $requestInfo = self::getRequestInfo($response);
        $content = self::checkResponse($response, $requestInfo, $stream, false);
        $content = new StreamReader($content, intval(self::getHeaderLine($response, Constant::HeaderContentLength)));
        $output = new GetObjectOutput($requestInfo, self::getHeaderLine($response, Constant::HeaderContentRange),
            self::getHeaderLine($response, Constant::HeaderETag), self::transRFC1123TimeInHeader($response, Constant::HeaderLastModified),
            boolval(self::getHeaderLine($response, Constant::HeaderDeleteMarker)), self::getHeaderLine($response, Constant::HeaderSSECAlgorithm),
            self::getHeaderLine($response, Constant::HeaderSSECKeyMD5), self::getHeaderLine($response, Constant::HeaderVersionId),
            self::getHeaderLine($response, Constant::HeaderWebsiteRedirectLocation), self::getHeaderLine($response, Constant::HeaderObjectType),
            self::getHeaderLine($response, Constant::HeaderHashCrc64ecma), self::getHeaderLine($response, Constant::HeaderStorageClass),
            self::parseMeta($response), $content->getSize(),
            self::getHeaderLine($response, Constant::HeaderCacheControl), rawurldecode(self::getHeaderLine($response, Constant::HeaderContentDisposition)),
            self::getHeaderLine($response, Constant::HeaderContentEncoding), self::getHeaderLine($response, Constant::HeaderContentLanguage),
            self::getHeaderLine($response, Constant::HeaderContentType), self::transRFC1123TimeInHeader($response, Constant::HeaderExpires), $content);
        return $output;
    }

    protected static function &parseGetObjectToFileOutput(ResponseInterface &$response, $filePath, $doMkdir, $bucket, $key, $stream = false)
    {
        $requestInfo = self::getRequestInfo($response);
        $content = self::checkResponse($response, $requestInfo, $stream, false);
        $content = new StreamReader($content, intval(self::getHeaderLine($response, Constant::HeaderContentLength)));
        if ($doMkdir) {
            if (!is_dir($filePath)) {
                mkdir($filePath, 0755, true);
            }
        } else {
            $file = null;
            $tempFilePath = null;
            try {
                $tempFilePath = $filePath . '.' . base64_encode(md5($bucket . '.' . $key)) . '.download';
                $file = fopen($tempFilePath, 'w');
                Utils::copyToStream(Utils::streamFor($content), Utils::streamFor($file));
                if (is_resource($file)) {
                    fclose($file);
                }
                if (!rename($tempFilePath, $filePath)) {
                    throw new TosClientException(sprintf('rename temp file %s to %s failed, will unlink temp file', $tempFilePath, $filePath));
                }
            } finally {
                if (is_resource($file)) {
                    fclose($file);
                }

                if ($tempFilePath && file_exists($tempFilePath)) {
                    unlink($tempFilePath);
                }

                if ($content) {
                    $content->close();
                }
            }
        }
        $output = new GetObjectToFileOutput($requestInfo, self::getHeaderLine($response, Constant::HeaderContentRange),
            self::getHeaderLine($response, Constant::HeaderETag), self::transRFC1123TimeInHeader($response, Constant::HeaderLastModified),
            boolval(self::getHeaderLine($response, Constant::HeaderDeleteMarker)), self::getHeaderLine($response, Constant::HeaderSSECAlgorithm),
            self::getHeaderLine($response, Constant::HeaderSSECKeyMD5), self::getHeaderLine($response, Constant::HeaderVersionId),
            self::getHeaderLine($response, Constant::HeaderWebsiteRedirectLocation), self::getHeaderLine($response, Constant::HeaderObjectType),
            self::getHeaderLine($response, Constant::HeaderHashCrc64ecma), self::getHeaderLine($response, Constant::HeaderStorageClass),
            self::parseMeta($response), $content->getSize(),
            self::getHeaderLine($response, Constant::HeaderCacheControl), rawurldecode(self::getHeaderLine($response, Constant::HeaderContentDisposition)),
            self::getHeaderLine($response, Constant::HeaderContentEncoding), self::getHeaderLine($response, Constant::HeaderContentLanguage),
            self::getHeaderLine($response, Constant::HeaderContentType), self::transRFC1123TimeInHeader($response, Constant::HeaderExpires), $filePath);
        return $output;
    }

    protected static function &parseDeleteObjectOutput(ResponseInterface &$response)
    {
        $requestInfo = self::getRequestInfo($response);
        self::checkResponse($response, $requestInfo);
        $output = new DeleteObjectOutput($requestInfo, boolval(self::getHeaderLine($response, Constant::HeaderDeleteMarker)),
            self::getHeaderLine($response, Constant::HeaderVersionId));
        return $output;
    }

    protected static function &parseDeleteMultiObjectsOutput(ResponseInterface &$response)
    {
        $requestInfo = self::getRequestInfo($response);
        $result = self::checkResponse($response, $requestInfo);

        $deleted = [];
        $error = [];
        if (isset($result['Deleted']) && is_array($result['Deleted'])) {
            foreach ($result['Deleted'] as $item) {
                $deleted[] = new Deleted(isset($item['Key']) ? strval($item['Key']) : '',
                    isset($item['VersionId']) ? strval($item['VersionId']) : '',
                    isset($item['DeleteMarker']) ? boolval($item['DeleteMarker']) : false,
                    isset($item['DeleteMarkerVersionId']) ? strval($item['DeleteMarkerVersionId']) : '');
            }
        }

        if (isset($result['Error']) && is_array($result['Error'])) {
            foreach ($result['Error'] as $item) {
                $error[] = new DeleteError(isset($item['Key']) ? strval($item['Key']) : '',
                    isset($item['VersionId']) ? strval($item['VersionId']) : '',
                    isset($item['Code']) ? strval($item['Code']) : '',
                    isset($item['Message']) ? strval($item['Message']) : '');
            }
        }

        $output = new DeleteMultiObjectsOutput($requestInfo, $deleted, $error);
        return $output;
    }

    protected static function &parseGetObjectACLOutput(ResponseInterface &$response)
    {
        $requestInfo = self::getRequestInfo($response);
        $result = self::checkResponse($response, $requestInfo);
        $grants = [];
        if (isset($result['Grants']) && is_array($result['Grants'])) {
            foreach ($result['Grants'] as $item) {
                $grants[] = new Grant(isset($item['Grantee']) ?
                    new Grantee(isset($item['Grantee']['ID']) ? strval($item['Grantee']['ID']) : '',
                        isset($item['Grantee']['Type']) ? strval($item['Grantee']['Type']) : '',
                        isset($item['Grantee']['Canned']) ? strval($item['Grantee']['Canned']) : '') : null,
                    isset($item['Permission']) ? strval($item['Permission']) : '');
            }
        }

        $output = new GetObjectACLOutput($requestInfo, self::getHeaderLine($response, Constant::HeaderVersionId), self::transOwner($result), $grants);
        return $output;
    }

    protected static function &parseHeadObjectOutput(ResponseInterface &$response)
    {
        $requestInfo = self::getRequestInfo($response);
        self::checkResponse($response, $requestInfo);

        $output = new HeadObjectOutput($requestInfo,
            self::getHeaderLine($response, Constant::HeaderETag), self::transRFC1123TimeInHeader($response, Constant::HeaderLastModified),
            boolval(self::getHeaderLine($response, Constant::HeaderDeleteMarker)), self::getHeaderLine($response, Constant::HeaderSSECAlgorithm),
            self::getHeaderLine($response, Constant::HeaderSSECKeyMD5), self::getHeaderLine($response, Constant::HeaderVersionId),
            self::getHeaderLine($response, Constant::HeaderWebsiteRedirectLocation), self::getHeaderLine($response, Constant::HeaderObjectType),
            self::getHeaderLine($response, Constant::HeaderHashCrc64ecma), self::getHeaderLine($response, Constant::HeaderStorageClass),
            self::parseMeta($response), intval(self::getHeaderLine($response, Constant::HeaderContentLength)),
            self::getHeaderLine($response, Constant::HeaderCacheControl), rawurldecode(self::getHeaderLine($response, Constant::HeaderContentDisposition)),
            self::getHeaderLine($response, Constant::HeaderContentEncoding), self::getHeaderLine($response, Constant::HeaderContentLanguage),
            self::getHeaderLine($response, Constant::HeaderContentType), self::transRFC1123TimeInHeader($response, Constant::HeaderExpires));
        return $output;
    }

    protected static function &parseAppendObjectOutput(ResponseInterface &$response)
    {
        $requestInfo = self::getRequestInfo($response);
        self::checkResponse($response, $requestInfo);
        $output = new AppendObjectOutput($requestInfo, self::getHeaderLine($response, Constant::HeaderVersionId),
            intval(self::getHeaderLine($response, Constant::HeaderNextAppendOffset)), self::getHeaderLine($response, Constant::HeaderHashCrc64ecma));
        return $output;
    }

    protected static function &parseListObjectsOutput(ResponseInterface &$response)
    {
        $requestInfo = self::getRequestInfo($response);
        $result = self::checkResponse($response, $requestInfo);

        $commonPrefixes = [];
        if (isset($result['CommonPrefixes']) && is_array($result['CommonPrefixes'])) {
            foreach ($result['CommonPrefixes'] as $item) {
                if (isset($item['Prefix'])) {
                    $commonPrefixes[] = new ListedCommonPrefix(strval($item['Prefix']));
                }
            }
        }

        $contents = [];
        if (isset($result['Contents']) && is_array($result['Contents'])) {
            foreach ($result['Contents'] as $item) {
                $contents[] = new ListedObject(isset($item['Key']) ? strval($item['Key']) : '',
                    self::transISO8601TimeInArray($item, 'LastModified'), isset($item['ETag']) ? strval($item['ETag']) : '',
                    isset($item['Size']) ? intval($item['Size']) : 0, self::transOwner($item),
                    isset($item['StorageClass']) ? strval($item['StorageClass']) : '',
                    isset($item['HashCrc64ecma']) ? strval($item['HashCrc64ecma']) : '');
            }
        }

        $output = new ListObjectsOutput($requestInfo, isset($result['Name']) ? strval($result['Name']) : '',
            isset($result['Prefix']) ? strval($result['Prefix']) : '', isset($result['Delimiter']) ? strval($result['Delimiter']) : '',
            isset($result['Marker']) ? strval($result['Marker']) : '', isset($result['MaxKeys']) ? intval($result['MaxKeys']) : 0,
            isset($result['EncodingType']) ? strval($result['EncodingType']) : '', isset($result['IsTruncated']) && boolval($result['IsTruncated']),
            isset($result['NextMarker']) ? strval($result['NextMarker']) : '', $commonPrefixes, $contents);
        return $output;
    }

    protected static function &parseListObjectVersionsOutput(ResponseInterface &$response)
    {
        $requestInfo = self::getRequestInfo($response);
        $result = self::checkResponse($response, $requestInfo);

        $commonPrefixes = [];
        if (isset($result['CommonPrefixes']) && is_array($result['CommonPrefixes'])) {
            foreach ($result['CommonPrefixes'] as $item) {
                if (isset($item['Prefix'])) {
                    $commonPrefixes[] = new ListedCommonPrefix(strval($item['Prefix']));
                }
            }
        }

        $versions = [];
        if (isset($result['Versions']) && is_array($result['Versions'])) {
            foreach ($result['Versions'] as $item) {
                $versions[] = new ListedObjectVersion(isset($item['Key']) ? strval($item['Key']) : '',
                    self::transISO8601TimeInArray($item, 'LastModified'), isset($item['ETag']) ? strval($item['ETag']) : '',
                    isset($item['IsLatest']) && boolval($item['IsLatest']), isset($item['Size']) ? intval($item['Size']) : 0,
                    self::transOwner($item), isset($item['StorageClass']) ? strval($item['StorageClass']) : '',
                    isset($item['VersionId']) ? strval($item['VersionId']) : '', isset($item['HashCrc64ecma']) ? strval($item['HashCrc64ecma']) : '');
            }
        }

        $deleteMarkers = [];
        if (isset($result['DeleteMarkers']) && is_array($result['DeleteMarkers'])) {
            foreach ($result['DeleteMarkers'] as $item) {
                $deleteMarkers[] = new ListedDeleteMarker(isset($item['Key']) ? strval($item['Key']) : '',
                    self::transISO8601TimeInArray($item, 'LastModified'), isset($item['IsLatest']) && boolval($item['IsLatest']),
                    self::transOwner($item), isset($item['VersionId']) ? strval($item['VersionId']) : '');
            }
        }

        $output = new ListObjectVersionsOutput($requestInfo, isset($result['Name']) ? strval($result['Name']) : '',
            isset($result['Prefix']) ? strval($result['Prefix']) : '', isset($result['Delimiter']) ? strval($result['Delimiter']) : '',
            isset($result['KeyMarker']) ? strval($result['KeyMarker']) : '', isset($result['VersionIdMarker']) ? strval($result['VersionIdMarker']) : '',
            isset($result['MaxKeys']) ? intval($result['MaxKeys']) : 0, isset($result['EncodingType']) ? strval($result['EncodingType']) : '',
            isset($result['IsTruncated']) && boolval($result['IsTruncated']), isset($result['NextKeyMarker']) ? strval($result['NextKeyMarker']) : '',
            isset($result['NextVersionIdMarker']) ? strval($result['NextVersionIdMarker']) : '',
            $commonPrefixes, $versions, $deleteMarkers);
        return $output;
    }

    protected static function &parsePutObjectACLOutput(ResponseInterface &$response)
    {
        $requestInfo = self::getRequestInfo($response);
        self::checkResponse($response, $requestInfo);
        $output = new PutObjectACLOutput($requestInfo);
        return $output;
    }

    protected static function &parseSetObjectMetaOutput(ResponseInterface &$response)
    {
        $requestInfo = self::getRequestInfo($response);
        self::checkResponse($response, $requestInfo);
        $output = new SetObjectMetaOutput($requestInfo);
        return $output;
    }

    protected static function &parseCreateMultipartUploadOutput(ResponseInterface &$response)
    {
        $requestInfo = self::getRequestInfo($response);
        $result = self::checkResponse($response, $requestInfo);

        $output = new CreateMultipartUploadOutput($requestInfo, isset($result['Bucket']) ? strval($result['Bucket']) : '',
            isset($result['Key']) ? strval($result['Key']) : '', isset($result['UploadId']) ? strval($result['UploadId']) : '',
            self::getHeaderLine($response, Constant::HeaderSSECAlgorithm), self::getHeaderLine($response, Constant::HeaderSSECKeyMD5),
            isset($result['EncodingType']) ? strval($result['EncodingType']) : '');
        return $output;
    }

    protected static function &parseUploadPartOutput(ResponseInterface &$response, $partNumber)
    {
        return self::parseUploadPartFromFileOutput($response, $partNumber);
    }

    protected static function &parseUploadPartFromFileOutput(ResponseInterface &$response, $partNumber)
    {
        $requestInfo = self::getRequestInfo($response);
        self::checkResponse($response, $requestInfo);
        $output = new UploadPartFromFileOutput($requestInfo, $partNumber,
            self::getHeaderLine($response, Constant::HeaderETag), self::getHeaderLine($response, Constant::HeaderSSECAlgorithm),
            self::getHeaderLine($response, Constant::HeaderSSECKeyMD5), self::getHeaderLine($response, Constant::HeaderHashCrc64ecma));
        return $output;
    }

    protected static function &parseCompleteMultipartUploadOutput(ResponseInterface &$response)
    {
        $requestInfo = self::getRequestInfo($response);
        $result = self::checkResponse($response, $requestInfo);
        $output = new CompleteMultipartUploadOutput($requestInfo, isset($result['Bucket']) ? strval($result['Bucket']) : '',
            isset($result['Key']) ? strval($result['Key']) : '', isset($result['ETag']) ? strval($result['ETag']) : '',
            isset($result['Location']) ? strval($result['Location']) : '', self::getHeaderLine($response, Constant::HeaderVersionId),
            self::getHeaderLine($response, Constant::HeaderHashCrc64ecma));
        return $output;
    }

    protected static function &parseAbortMultipartUploadOutput(ResponseInterface &$response)
    {
        $requestInfo = self::getRequestInfo($response);
        self::checkResponse($response, $requestInfo);
        $output = new AbortMultipartUploadOutput($requestInfo);
        return $output;
    }

    protected static function &parseUploadPartCopyOutput(ResponseInterface &$response, $partNumber)
    {
        $requestInfo = self::getRequestInfo($response);
        $result = self::checkResponse($response, $requestInfo);
        if (isset($result['ETag'])) {
            $output = new UploadPartCopyOutput($requestInfo, $partNumber,
                strval($result['ETag']), self::transISO8601TimeInArray($result, 'LastModified'),
                self::getHeaderLine($response, Constant::HeaderCopySourceVersionId),
                self::getHeaderLine($response, Constant::HeaderSSECAlgorithm),
                self::getHeaderLine($response, Constant::HeaderSSECKeyMD5));
            return $output;
        }

        throw new TosServerException($requestInfo,
            isset($error['Code']) ? $error['Code'] : '',
            isset($error['Message']) ? $error['Message'] : 'upload part copy does not return etag expectly',
            isset($error['HostId']) ? $error['HostId'] : '',
            isset($error['Resource']) ? $error['Resource'] : '');
    }

    protected static function &parseListMultipartUploadsOutput(ResponseInterface &$response)
    {
        $requestInfo = self::getRequestInfo($response);
        $result = self::checkResponse($response, $requestInfo);

        $commonPrefixes = [];
        if (isset($result['CommonPrefixes']) && is_array($result['CommonPrefixes'])) {
            foreach ($result['CommonPrefixes'] as $item) {
                if (isset($item['Prefix'])) {
                    $commonPrefixes[] = new ListedCommonPrefix(strval($item['Prefix']));
                }
            }
        }

        $uploads = [];
        if (isset($result['Uploads']) && is_array($result['Uploads'])) {
            foreach ($result['Uploads'] as $item) {
                $uploads[] = new ListedUpload(isset($item['Key']) ? strval($item['Key']) : '',
                    isset($item['UploadId']) ? strval($item['UploadId']) : '', self::transOwner($item),
                    isset($item['StorageClass']) ? strval($item['StorageClass']) : '',
                    self::transISO8601TimeInArray($item, 'Initiated'));
            }
        }

        $output = new ListMultipartUploadsOutput($requestInfo, isset($result['Bucket']) ? strval($result['Bucket']) : '',
            isset($result['Prefix']) ? strval($result['Prefix']) : '', isset($result['Delimiter']) ? strval($result['Delimiter']) : '',
            isset($result['KeyMarker']) ? strval($result['KeyMarker']) : '', isset($result['UploadIdMarker']) ? strval($result['UploadIdMarker']) : '',
            isset($result['MaxUploads']) ? intval($result['MaxUploads']) : 0, isset($result['EncodingType']) ? strval($result['EncodingType']) : '',
            isset($result['IsTruncated']) && boolval($result['IsTruncated']), isset($result['NextKeyMarker']) ? strval($result['NextKeyMarker']) : '',
            isset($result['NextUploadIdMarker']) ? strval($result['NextUploadIdMarker']) : '',
            $commonPrefixes, $uploads);
        return $output;
    }

    protected static function &parseListPartsOutput(ResponseInterface &$response)
    {
        $requestInfo = self::getRequestInfo($response);
        $result = self::checkResponse($response, $requestInfo);

        $parts = [];
        if (isset($result['Parts']) && is_array($result['Parts'])) {
            foreach ($result['Parts'] as $item) {
                $parts[] = new UploadedPart(isset($item['PartNumber']) ? intval($item['PartNumber']) : 0,
                    isset($item['ETag']) ? strval($item['ETag']) : '', isset($item['Size']) ? intval($item['Size']) : 0,
                    self::transISO8601TimeInArray($item, 'LastModified'));
            }
        }

        $output = new ListPartsOutput($requestInfo, isset($result['Bucket']) ? strval($result['Bucket']) : '',
            isset($result['Key']) ? strval($result['Key']) : '', isset($result['UploadId']) ? strval($result['UploadId']) : '',
            isset($result['PartNumberMarker']) ? intval($result['PartNumberMarker']) : 0,
            isset($result['MaxParts']) ? intval($result['MaxParts']) : 0,
            isset($result['IsTruncated']) && boolval($result['IsTruncated']), isset($result['NextPartNumberMarker']) ? intval($result['NextPartNumberMarker']) : 0,
            isset($result['StorageClass']) ? strval($result['StorageClass']) : '',
            self::transOwner($result), $parts);
        return $output;
    }

    protected static function &parsePutObjectTaggingOutput(ResponseInterface &$response)
    {
        $requestInfo = self::getRequestInfo($response);
        self::checkResponse($response, $requestInfo);
        $output = new PutObjectTaggingOutput($requestInfo, self::getHeaderLine($response, Constant::HeaderVersionId));
        return $output;
    }

    protected static function &parseGetObjectTaggingOutput(ResponseInterface &$response)
    {
        $requestInfo = self::getRequestInfo($response);
        $result = self::checkResponse($response, $requestInfo);
        $tags = [];
        if (isset($result['TagSet']) && isset($result['TagSet']['Tags']) && is_array($result['TagSet']['Tags'])) {
            foreach ($result['TagSet']['Tags'] as $item) {
                $tags[] = new Tag(isset($item['Key']) ? strval($item['Key']) : '',
                    isset($item['Value']) ? strval($item['Value']) : '');
            }
        }

        $output = new GetObjectTaggingOutput($requestInfo, self::getHeaderLine($response, Constant::HeaderVersionId), new TagSet($tags));
        return $output;
    }

    protected static function &parseDeleteObjectTaggingOutput(ResponseInterface &$response)
    {
        $requestInfo = self::getRequestInfo($response);
        self::checkResponse($response, $requestInfo);
        $output = new DeleteObjectTaggingOutput($requestInfo, self::getHeaderLine($response, Constant::HeaderVersionId));
        return $output;
    }

    /**
     * @param ResponseInterface &$response
     * @param RequestInfo $requestInfo
     * @param bool $parseContents
     * @return mixed
     */
    protected static function checkResponse(ResponseInterface &$response, RequestInfo &$requestInfo, $stream = false, $parseContents = true)
    {
        $body = $response->getBody();
        if ($stream) {
            $body = new StreamReader($body, intval(self::getHeaderLine($response, Constant::HeaderContentLength)));
        }
        if ($requestInfo->getStatusCode() >= 300) {
            if (($contents = self::readContents($body, $requestInfo)) && ($error = json_decode($contents, true))) {
//                echo $error['canonical_request'] . PHP_EOL;
                throw new TosServerException($requestInfo,
                    isset($error['Code']) ? $error['Code'] : '',
                    isset($error['Message']) ? $error['Message'] : '',
                    isset($error['HostId']) ? $error['HostId'] : '',
                    isset($error['Resource']) ? $error['Resource'] : '');
            }
            throw new TosServerException($requestInfo);
        }


        if ($parseContents) {
            if ($contents = self::readContents($body, $requestInfo)) {
                $result = json_decode($contents, true);
                if (json_last_error() !== 0) {
                    throw new TosClientException(sprintf('unable to do serialization/deserialization, %s', json_last_error_msg()));
                }
                return $result;
            }
            return [];
        }

        return $body;
    }

    protected static function readContents(StreamInterface &$body, RequestInfo &$requestInfo)
    {
        try {
            return $body->getContents();
        } catch (\RuntimeException $ex) {
            throw new TosServerException($requestInfo, '', sprintf('check response error, %s', $ex->getMessage()));
        } finally {
            $body->close();
        }
    }

    /**
     * @param ResponseInterface &$response
     * @return RequestInfo
     */
    protected static function &getRequestInfo(ResponseInterface &$response)
    {
        $requestInfo = new RequestInfo(self::getHeaderLine($response, Constant::HeaderRequestId), self::getHeaderLine($response, Constant::HeaderId2),
            $response->getStatusCode(), $response->getHeaders());
        return $requestInfo;
    }

    protected static function getHeaderLine(ResponseInterface &$response, $name)
    {
        return $response->hasHeader($name) ? $response->getHeaderLine($name) : '';
    }

    protected static function transRFC1123TimeInHeader(ResponseInterface &$response, $name)
    {
        if ($val = self::getHeaderLine($response, $name)) {
            return intval(strtotime($val, 0));
        }
        return 0;
    }

    protected static function transISO8601TimeInArray($result, $name)
    {
        if (isset($result[$name]) && ($val = strval($result[$name]))) {
            return intval(strtotime($val, 0));
        }
        return 0;
    }

    protected static function &transOwner($result)
    {
        $owner = null;
        if (isset($result['Owner']) && isset($result['Owner']['ID'])) {
            $owner = new Owner(strval($result['Owner']['ID']));
        }
        return $owner;
    }

    protected static function &parseMeta(ResponseInterface &$response)
    {
        $meta = [];
        foreach ($response->getHeaders() as $key => $value) {
            if (strpos($key, Constant::HeaderPrefixMeta) === 0) {
                $meta[rawurldecode(substr($key, strlen(Constant::HeaderPrefixMeta)))] = rawurldecode(implode(', ', $value));
            }
        }
        return $meta;
    }
}