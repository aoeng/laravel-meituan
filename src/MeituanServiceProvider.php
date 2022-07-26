<?php

namespace Aoeng\Laravel\Meituan;


use Illuminate\Support\ServiceProvider;

class MeituanServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/meituan.php' => config_path('meituan.php'),
        ], 'meituan');

    }

    public function register()
    {
        $this->app->singleton('meituan-pub', function ($app) {
            return new MeituanPub($app);
        });


    }

}
