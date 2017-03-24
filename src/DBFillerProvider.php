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

      	$path_func='';
        if(function_exists('config_path')){
            $path_func="config_path";
        }
        else{
            $path_func=function($path){
                return app()->basePath().'/config/'.$path;
            };
        }
        $this->publishes([
            __DIR__.'/config/dbfiller.php'=>call_user_func($path_func,'dbfiller.php'),
        ]);


    }
}
