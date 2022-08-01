<?php

namespace Aoeng\Laravel\Meituan\Facades;

use Aoeng\Laravel\Tronscan\Tronscan;
use Illuminate\Support\Facades\Facade as LaravelFacade;

/**
 * @method static array activities($timeLimit = true)
 * @method static array link($activity, $utmMedium = null, $promotionId = null, $userLevel = null, $demandQrInfo = false)
 * @method static array cpsOrders($param = [], $page = 1, $size = 20)
 * @method static array cpaOrders($param = [], $page = 1, $size = 20)
 * @method static array errorOrders($param = [], $page = 1, $size = 20)
 * @method static array request($path, $param = [], $method = 'GET', $sign = false)
 */
class MeituanPub extends LaravelFacade
{
    protected static function getFacadeAccessor()
    {
        return 'meituan-pub';
    }

}
