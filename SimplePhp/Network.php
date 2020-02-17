<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/2/17
 * Time: 16:57
 */

namespace SimplePhp;


class Network
{
    private $header;
    private $cookie;
    private $cookie_jar;
    private $body;
    private $connect;
    private $url;
    private $raw;

    public function __construct()
    {
        $this->cookie_jar = tempnam('./tmp', 'cookie');
    }

    public static function get(string $url, array $header = array(), string $cookie = ""): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;
    }

    public function post(string $url, array $data = array(), array $header = array(), $cookie = array()): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;
    }

    public function request(string $method = "get", array $data = array(), array $header = array(), array $cookie = array()): Network
    {
        return $this;
    }

    public static function checkConnect($url): bool
    {
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);//设置超时时间
        curl_exec($handle);
        //检查是否404（网页找不到）
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);
        if ($httpCode == 404) {
            return false;
        } else {
            return true;
        }
    }
}