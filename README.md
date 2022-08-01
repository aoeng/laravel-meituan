## 美团分销联盟 Laravel 扩展

### Install

```bash 
composer require aoeng/laravel-meituan

php artisan vendor:publish --tag=meituan
```


### Used
```php
use \Aoeng\Laravel\Meituan\Facades\MeituanPub;

// 这个分销联盟做的太拉跨了
MeituanPub::activities();
MeituanPub::link();
MeituanPub::cpaOrders();
MeituanPub::request();

```

### 还不是很完善 欢迎Pull requests
