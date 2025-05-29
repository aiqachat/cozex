<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\sdk;

/**
 * @property string $method
 * @property string $action
 * @property string $version
 * @property string $host
 */
abstract class Basics
{
    use Config;

    public function __construct($array = [])
    {
        foreach ($array as $key => $item) {
            $this->$key = $item;
        }
        $this->setIdent();
    }

    public function getHeader($ak, $sk, $body, $path, $query = []): array
    {
        $contentType = 'application/json';
        $header = [];
        // 初始化签名结果的结构体
        $xDate = gmdate('Ymd\THis\Z');
        $shortXDate = substr($xDate, 0, 8);
        $xContentSha256 = hash('sha256', $body);
        $signResult = [
            'Host' => $this->host,
            'X-Content-Sha256' => $xContentSha256,
            'X-Date' => $xDate,
            'Content-Type' => $contentType
        ];
        // 第四步：计算 Signature 签名。
        $signedHeaderStr = join(';', ['content-type', 'host', 'x-content-sha256', 'x-date']);
        $canonicalRequestStr = join("\n", [
            $this->method,
            $path,
            http_build_query($query),
            join("\n", ['content-type:' . $contentType, 'host:' . $this->host, 'x-content-sha256:' . $xContentSha256, 'x-date:' . $xDate]),
            '',
            $signedHeaderStr,
            $xContentSha256
        ]);
        $hashedCanonicalRequest = hash("sha256", $canonicalRequestStr);
        $credentialScope = join('/', [$shortXDate, $this->region, $this->service, 'request']);
        $stringToSign = join("\n", ['HMAC-SHA256', $xDate, $credentialScope, $hashedCanonicalRequest]);
        $kDate = hash_hmac("sha256", $shortXDate, $sk, true);
        $kRegion = hash_hmac("sha256", $this->region, $kDate, true);
        $kService = hash_hmac("sha256", $this->service, $kRegion, true);
        $kSigning = hash_hmac("sha256", 'request', $kService, true);
        $signature = hash_hmac("sha256", $stringToSign, $kSigning);
        $signResult['Authorization'] = sprintf("HMAC-SHA256 Credential=%s, SignedHeaders=%s, Signature=%s", $ak . '/' . $credentialScope, $signedHeaderStr, $signature);
        return array_merge($header, $signResult);
    }

    public function getAttribute(): array
    {
        $reflectionClass = new \ReflectionClass($this);
        $properties = $reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC);

        $newData = [];
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($this);
            if($value !== null){
                $newData[$property->getName()] = $value;
            }
        }
        return $newData;
    }

    /**
     * @param $name
     * @param $value
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->{$name} = $value;
        }
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }
        return null;
    }

    abstract function setIdent();
}
