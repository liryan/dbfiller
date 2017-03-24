<?php
namespace Dbfiller;
class Helper{
    public static function info($msg,$intval=0)
    {
        static $now=0;
        $out=false;
        if($intval==0){
            $out=true;
        }
        else if(time()-$now>$intval){
            $now=time();
            $out=true;
        }
        if($out)
            echo Date('Y-m-d H:i:s')."\t".$msg."\n";
    }
}
