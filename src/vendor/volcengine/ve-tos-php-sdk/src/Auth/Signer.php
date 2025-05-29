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

namespace Tos\Auth;

use Tos\Helper\Helper;
use Tos\Model\Constant;
use Tos\Model\HttpRequest;

trait Signer
{
    private static $emptyHashPayload = 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855';
    private static $algorithm = 'TOS4-HMAC-SHA256';
    private static $unsignedPayload = 'UNSIGNED-PAYLOAD';

    protected static function sign(HttpRequest &$request, $host, $ak, $sk, $securityToken, $region)
    {
        if ($request->headers === null) {
            $request->headers = [];
        }

        $request->headers[Constant::HeaderHost] = $host;

        $longDate = null;
        $shortDate = null;
        $credentialScope = null;
        self::prepareDateAndCredentialScope($longDate, $shortDate, $credentialScope, $region);

        if ($ak && $sk && $securityToken) {
            $request->headers[Constant::HeaderSecurityToken] = strval($securityToken);
        }
        $request->headers[Constant::HeaderRequestDate] = $longDate;
        $signedHeaders = null;
        $canonicalRequest = self::getCanonicalRequest($request, '', $signedHeaders);

        if (!$ak || !$sk) {
            return;
        }
        $stringToSign = self::getStringToSign($canonicalRequest, $longDate, $credentialScope);
        $signature = self::getSignature($stringToSign, $shortDate, $sk, $region);
        $request->headers[Constant::HeaderAuthorization] = self::$algorithm .
            ' Credential=' . $ak . '/' . $credentialScope . ', SignedHeaders=' . $signedHeaders . ', Signature=' . $signature;
    }

    protected static function prepareDateAndCredentialScope(&$longDate, &$shortDate, &$credentialScope, $region)
    {
        $longDate = gmdate('Ymd\THis\Z', time());
        $shortDate = substr($longDate, 0, 8);
        $credentialScope = $shortDate . '/' . $region . '/tos/request';
    }

    private static function getSignature($stringToSign, $shortDate, $sk, $region)
    {
        $dateKey = hash_hmac('sha256', $shortDate, $sk, true);
        $regionKey = hash_hmac('sha256', $region, $dateKey, true);
        $serviceKey = hash_hmac('sha256', 'tos', $regionKey, true);
        $signingKey = hash_hmac('sha256', 'request', $serviceKey, true);
        return hash_hmac('sha256', $stringToSign, $signingKey);
    }

    private static function getStringToSign($canonicalRequest, $longDate, $credentialScope)
    {
        $stringToSign = self::$algorithm . "\n" . $longDate . "\n" . $credentialScope . "\n";
        $stringToSign .= hash('sha256', $canonicalRequest);
        return $stringToSign;
    }

    private static function getCanonicalHeaders(HttpRequest &$request, &$signedHeaders, array &$signedHeader)
    {
        $canonicalHeaders = '';
        $contentSha256 = '';
        ksort($request->headers);
        foreach ($request->headers as $key => $val) {
            $lowerKey = strtolower($key);
            if($lowerKey === Constant::HeaderContentSHA256){
                $contentSha256 = $val;
            }
            if ($lowerKey !== Constant::HeaderHostLower && $lowerKey !== Constant::HeaderContentTypeLower &&
                strpos($lowerKey, Constant::HeaderPrefix) !== 0) {
                continue;
            }
            $signedHeader[$key] = $val;
            $signedHeaders .= $lowerKey . ';';
            $canonicalHeaders .= $lowerKey . ':' . trim(strval($val)) . "\n";
        }
        $signedHeaders = substr($signedHeaders, 0, strlen($signedHeaders) - 1);
        return [$canonicalHeaders, $contentSha256];
    }

    private static function getCanonicalRequest(HttpRequest &$request, $canonicalHeaders = '', &$signedHeaders = '', $query = false, $contentSha256='')
    {
        $canonicalRequest = strtoupper($request->method) . "\n";
        $canonicalRequest .= '/';
        if ($request->key) {
            $canonicalRequest .= Helper::urlencodeWithSafe($request->key);
        }
        $canonicalRequest .= "\n";

        if (is_array($request->queries)) {
            ksort($request->queries);
            $index = 0;
            foreach ($request->queries as $key => $val) {
                $encodedKey = rawurlencode($key);
                $val = rawurlencode(strval($val));
                $request->queries[$key] = $val;
                $canonicalRequest .= $encodedKey . '=' . $val;
                if ($index !== count($request->queries) - 1) {
                    $canonicalRequest .= '&';
                }
                $index++;
            }
        }
        $canonicalRequest .= "\n";

        if ($canonicalHeaders && $signedHeaders) {
            $canonicalRequest .= $canonicalHeaders;
            $canonicalRequest .= "\n";
        } else {
            $signedHeaders = '';
            $headers = [];
            foreach ($request->headers as $key => $val) {
                $headers[strtolower($key)] = $val;
            }
            ksort($headers);
            foreach ($headers as $key => $val) {
                if ($key !== Constant::HeaderHostLower && $key !== Constant::HeaderContentTypeLower &&
                    strpos($key, Constant::HeaderPrefix) !== 0) {
                    continue;
                }

                $signedHeaders .= $key . ';';
                $canonicalRequest .= $key . ':' . trim(strval($val)) . "\n";
            }
            $canonicalRequest .= "\n";
            $signedHeaders = substr($signedHeaders, 0, strlen($signedHeaders) - 1);
        }

        $canonicalRequest .= $signedHeaders;
        $canonicalRequest .= "\n";

        if($contentSha256){
            $canonicalRequest .= $contentSha256;
        }else if ($query) {
            $canonicalRequest .= self::$unsignedPayload;
        } else {
            $canonicalRequest .= self::$emptyHashPayload;
        }
        return $canonicalRequest;
    }
}
