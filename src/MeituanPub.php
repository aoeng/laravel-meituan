<?php

namespace Aoeng\Laravel\Meituan;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MeituanPub
{
    protected $baseUrl = 'https://union.dianping.com/';

    protected $version = '1.0';

    protected $appKey;
    protected $utmSource;
    protected $callbackSecret;


    public function __construct(Application $app)
    {
        $this->appKey = config('meituan.app_key', '');
        $this->utmSource = config('meituan.utm_source', '');
    }


    public function activities($timeLimit = true)
    {
        return $this->request('api/board/activities', ['utmSource' => $this->utmSource, 'timeLimit' => $timeLimit], 'POST');
    }

    public function link($activity, $utmMedium = null, $promotionId = null, $userLevel = null, $demandQrInfo = false)
    {
        $param = [
            'utmSource'   => $this->utmSource,
            'utmMedium'   => $this->encrypt($utmMedium ?? 'base'),
            'activity'    => $activity,
            'promotionId' => $promotionId ?? config('meituan.promotion_id', ''),
            'pageLevel'   => $utmMedium == null ? 1 : 2,
        ];
        if (!empty($utmMedium)) {
            $param['demandQrInfo'] = $demandQrInfo;
            $param['userLevel'] = $userLevel ?? config('meituan.user_level', 1);
        }

        return $this->request('api/promotion/link', $param, 'POST');
    }

    public function cpsOrders($param = [], $page = 1, $size = 20)
    {
        $param = array_merge(compact('page', 'size'), $param);

        return $this->request('data/promote/verify/item/new', $param, 'POST', true);
    }

    public function cpaOrders($param = [], $page = 1, $size = 20)
    {
        $param = array_merge(compact('page', 'size'), $param);

        return $this->request('data/promote/verify/cpa/new', $param, 'POST', true);
    }

    public function errorOrders($param = [], $page = 1, $size = 20)
    {
        $param = array_merge(compact('page', 'size'), $param);

        return $this->request('data/promote/abnormal/item/new', $param, 'POST', true);
    }


    public function request($path, $param = [], $method = 'GET', $sign = false)
    {

        $url = $this->baseUrl . $path;
        $timestamp = time();

        $queryParam = [
            'requestId' => (string)rand(1, 99999999),
            'utmSource' => $this->utmSource,
            'version'   => $this->version,
            'timestamp' => $timestamp,
        ];

        if ($sign) {
            $queryParam['version'] = '2.0';
            $queryParam['signMethod'] = 'hmac';
            $queryParam['sign'] = $this->sign(array_merge($queryParam, $param));
        } else {
            $queryParam['accessToken'] = $this->encrypt($this->utmSource . $timestamp);
        }

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
        $str = '';
        foreach ($params as $key => $value) {
            $str .= $key . $value;
        }
        return Str::upper(hash_hmac('md5', $str, $secret));
    }


    private function encrypt($data)
    {
        $str = openssl_encrypt($data, 'AES-128-ECB', $this->appKey);
        return bin2hex(base64_decode($str));
    }

}
