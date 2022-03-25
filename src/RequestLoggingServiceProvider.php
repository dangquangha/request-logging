<?php

namespace Workable\RequestLogging;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Workable\RequestLogging\Console\RefererReportCommand;
use Workable\RequestLogging\Console\RobotsCounterReportCommand;
use Workable\RequestLogging\Console\UserSearchReportCommand;
use Workable\RequestLogging\Middleware\RefererLogMiddleware;
use Workable\RequestLogging\Middleware\RobotsCounterMiddleware;
use Workable\RequestLogging\Middleware\SearchDailyMiddleware;

class RequestLoggingServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(\Illuminate\Routing\Router $router)
    {
        $this->loadRoutesFrom(__DIR__ . '/Routes/robots_counter_api.php');
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
        $this->publishes([
            __DIR__ . '/Config/request_log.php' => config_path('request_log.php'),
            __DIR__ . '/Config/robots_counter.php' => config_path('robots_counter.php'),
        ]);
        $router->aliasMiddleware('robots.counter', RobotsCounterMiddleware::class);
        $router->aliasMiddleware('search.daily', SearchDailyMiddleware::class);
        $router->aliasMiddleware('refer.daily', RefererLogMiddleware::class);

        $configs = require (__DIR__ . '/Config/request_log.php');
        foreach($configs as $key => $config)
        {
            if (!Config::get('logging.channels.' . $key)) {
                Config::set('logging.channels.' . $key, $config);
            }
        }

        if ($this->app->runningInConsole()) {
            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);
                $schedule->command('robot:report --date=today')->daily();
            });
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            RobotsCounterReportCommand::class,
            UserSearchReportCommand::class,
            RefererReportCommand::class
        ]);
    }

}
