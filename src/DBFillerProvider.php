<?php
/**
 * DBFillerProvider 
 * 服务提供者
 * @uses ServiceProvider
 * @package 
 * @version 0.0.1
 * @copyright 2014-2015
 * @author Ryan
 * @license MIT
 */
namespace Dbfiller;
use \Illuminate\Support\ServiceProvider;

class DBFillerProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('dbfiller', function () {
            return new Filler();
        });
    }

    public function boot()
    {
        $this->commands([
            FillerCommand::class
        ]);

        $this->publishes([
            __DIR__.'/config/dbfiller.php'=>config_path('dbfiller.php'),
        ]);
    }
}
