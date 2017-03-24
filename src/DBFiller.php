<?php
/**
 * DBFiller 
 * Laravel的外观模式
 * @uses Facade
 * @package 
 * @version 0.0.1
 * @copyright 
 * @author Ryan
 * @license MIT
 */
namespace Dbfiller;
use Exception;

use Illuminate\Support\Facades\Facade;

class DBFiller extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'dbfiller';
    }
}
