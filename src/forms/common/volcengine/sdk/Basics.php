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

    public function getHeader($ak, $sk, $body, $query, $path = null): array
    {
        if (!$path) {
            $path = '/';
        }
        $headers = [];

        $ldt = gmdate('Ymd\THis\Z');
        $sdt = substr($ldt, 0, 8);
        $headers['X-Date'] = $ldt;

        $bodyHash = hash('sha256', $body);
        $headers['X-Content-Sha256'] = $bodyHash;

        $signedHeaders = [];
        foreach ($headers as $key => $value) {
            $signedHeaders[strtolower($key)] = $value;
        }
        ksort($signedHeaders);

        $signed_str = '';
        foreach ($signedHeaders as $k => $v) {
            $signed_str .= $k . ':' . $v . "\n";
        }

        $credentialScope = "$sdt/$this->region/$this->service/request";
        $signedHeadersString = implode(';', array_keys($signedHeaders));
        $canon = implode("\n", array($this->method, $path, $query, $signed_str, $signedHeadersString, $bodyHash));
        $hash = hash('sha256', $canon);
        $toSign = implode("\n", array("HMAC-SHA256", $ldt, $credentialScope, $hash));
        $dateKey = hash_hmac('sha256', $sdt, $sk, true);
        $regionKey = hash_hmac('sha256', $this->region, $dateKey, true);
        $serviceKey = hash_hmac('sha256', $this->service, $regionKey, true);
        $signingKey = hash_hmac('sha256', 'request', $serviceKey, true);
        $signature = hash_hmac('sha256', $toSign, $signingKey);
        $credential = $ak . '/' . $credentialScope;
        $headers['Authorization'] = "HMAC-SHA256 Credential={$credential}, SignedHeaders={$signedHeadersString}, Signature={$signature}";
        return $headers;
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
