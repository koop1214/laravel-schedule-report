<?php

namespace Koop\ScheduleReport\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Koop\ScheduleReport\Commands\ScheduleReportCommand;

use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Console\Events\ScheduledTaskStarting;
use Koop\ScheduleReport\Listeners\ScheduledTaskFinishedListener;
use Koop\ScheduleReport\Listeners\ScheduledTaskStartingListener;
use Koop\ScheduleReport\Storages\ScheduleReportStorage;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ScheduledTaskStarting::class => [
            ScheduledTaskStartingListener::class,
        ],
        ScheduledTaskFinished::class => [
            ScheduledTaskFinishedListener::class,
        ],
    ];

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/schedulereport.php', 'schedulereport');

        // Redis persistent
        $this->app->when([
            ScheduleReportStorage::class,
        ])->needs(\Redis::class)
            ->give(function (\Illuminate\Contracts\Foundation\Application $app) {
                $connection = $app['config']->get('schedulereport.redis.connection', 'default');
                return $app->make('redis')->connection($connection)->client();
            });
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $events = $this->app['events'];

        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                $events->listen($event, $listener);
            }
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                ScheduleReportCommand::class,
            ]);
        }

        $this->publishes(
            [__DIR__ . '/../../config/schedulereport.php' => config_path('schedulereport.php')]
        );
    }
}