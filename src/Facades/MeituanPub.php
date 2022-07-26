<?php

namespace Aoeng\Laravel\Meituan\Facades;

use Aoeng\Laravel\Tronscan\Tronscan;
use Illuminate\Support\Facades\Facade as LaravelFacade;

/**
 * @method static array getActivities($timeLimit = true)
 * @method static array getLink($activity, $utmMedium = null, $promotionId = null, $userLevel = null, $demandQrInfo = false)
 * @method static array request($path, $param = [], $method = 'GET', $sign = false)
 */
class MeituanPub extends LaravelFacade
{
    protected static function getFacadeAccessor()
    {
        return 'meituan-pub';
    }

}
