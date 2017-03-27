<?php
/**
 * FillerCommand 
 * 给Artisan增加命令
 * @uses Command
 * @package 
 * @version 0.0.1
 * @copyright 2014-2015
 * @author Ryan
 * @license MIT
 */
namespace Dbfiller;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class FillerCommand extends Command
{
    protected $name = 'mysql.filler';
    protected $signature = 'mysql.filler {format?} {--count=?}';
    protected $description = 'start fill database with given data';

    public function fire()
    {
        $format=$this->argument('format');
        if($format){
            $count=$this->option('count');
            DBFiller::test($format,$count);
        }
        else{
            DBFiller::fire();
        }
    }
}
