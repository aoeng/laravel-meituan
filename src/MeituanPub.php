<?php

namespace Aoeng\Laravel\Meituan;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Http;

class MeituanPub
{
    protected $baseUrl = 'https://union.dianping.com/api/';

    protected $version = '1.0';

    protected $appKey;
    protected $utmSource;
    protected $callbackSecret;


    public function __construct(Application $app)
    {
        $this->appKey = config('meituan.app_key', '');
        $this->utmSource = config('meituan.utm_source', '');
    }


    public function getActivities($timeLimit = true)
    {
        return $this->request('board/activities', ['utmSource' => $this->utmSource, 'timeLimit' => $timeLimit], 'POST')->json();
    }

    public function getLink($activity, $utmMedium = null, $promotionId = null, $userLevel = null, $demandQrInfo = false)
    {
        $param = [
            'utmSource'    => $this->utmSource,
            'utmMedium'    => $this->encrypt($utmMedium),
            'activity'     => $activity,
            'promotionId'  => $promotionId ?? config('meituan.promotion_id', ''),
            'pageLevel'    => $utmMedium == null ? 1 : 2,
            'demandQrInfo' => $demandQrInfo,
        ];
        $utmMedium && $param['userLevel'] = $userLevel ?? config('meituan.user_level', 1);


        return $this->request('promotion/link', $param, 'POST');
    }


    public function request($path, $param = [], $method = 'GET', $sign = false)
    {

        $url = $this->baseUrl . $path;
        $timestamp = time();

        $queryParam = [
            'requestId'   => (string)rand(1, 99999999),
            'utmSource'   => $this->utmSource,
            'version'     => $this->version,
            'accessToken' => $this->encrypt($this->utmSource . $timestamp),
            'timestamp'   => $timestamp,
        ];

        if ($sign) {
            $queryParam['version'] = '2.0';
            $queryParam['signMethod'] = 'hmac';
            $queryParam['sign'] = $this->sign(array_merge($queryParam, $param));
        }

        info('请求：', [$url . '?' . http_build_query($queryParam), $param]);
        if ($method == 'GET') {
            return Http::get($url . '?' . http_build_query($queryParam), $param)->json();
        } else {
            return Http::post($url . '?' . http_build_query($queryParam), $param)->json();
        }
    }

    protected function sign(array $params, $secret = null): string
    {
        $secret == null && $secret = $this->appKey;
        unset($params["sign"]);
        ksort($params);
        $str = $secret; // $secret为分配的密钥
        foreach ($params as $key => $value) {
            $str .= $key . (string)$value;
        }
        $str .= $secret;
        return md5($str);
    }


    private function encrypt($data)
    {
        if (empty($data)) {
            return null;
        }

        $str = openssl_encrypt($data, 'AES-128-ECB', $this->appKey);
        return bin2hex(base64_decode($str));
    }

}
