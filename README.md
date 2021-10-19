# Laravel schedule report

Пакет для сохранения и вывода отчета о выполненных по расписанию задачах (`\Console\Kernel::schedule`). История сохраняется в редис на 30 дней.

## Установка

Запустить:
```bash
composer require "koop1214/laravel-schedule-report"
```
---
### Laravel без auto-discovery:
Зарегистрировать service-provider в config/app.php:
```php
  Koop\ScheduleReport\Providers\ServiceProvider::class,
```

---
Опубликовать конфиг:
```bash
php artisan vendor:publish --provider="Koop\ScheduleReport\Providers\ServiceProvider"
```
Задать подключение к редис в `config/schedulereport.php` или `.env`
```php
'connection' => env('SCHEDULE_REPORT_REDIS', 'default'),
```

### Lumen:
Для Lumen зарегистрировать Provider в `bootstrap/app.php`:

```php
 $app->register(Koop\ScheduleReport\Providers\ServiceProvider::class);
```

Для изменения конфига скопировать файл в папку конфигов и активировать:

```php
$app->configure('schedulereport');
```

## Использование
```shell
php artisan schedule:report
php artisan schedule:report --date=2021-10-18 
```

