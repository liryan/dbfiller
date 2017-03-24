<?php
namespace Dbfiller\Gens;
use Exception;

class GenTime extends Gen implements IGen
{
    public function boot()
    {
        $obj=$this;

        parent::register([FormatParser::DB_DATETIME],function(FormatParser $ctx) use($obj) {
            return $obj->genDatetime();
        });

        parent::register([FormatParser::DB_DATE],function(FormatParser $ctx) use($obj) {
            return $obj->genDate();
        });

        parent::register([FormatParser::DB_YEAR],function(FormatParser $ctx) use($obj) {
            return $obj->genYear($ctx->param);
        });

        parent::register([FormatParser::DB_TIMESTAMP],function(FormatParser $ctx) use($obj) {
            return $obj->genTime($ctx->param);
        });

        parent::register([FormatParser::DB_TIME],function(FormatParser $ctx) use($obj) {
            return $obj->genDayTime($ctx->param);
        });
    } 

    protected function genTime($param)
    {
        $len=intval($param);
        $time=Date('YmdHis',time()-mt_rand(0,12*30*24*3600));
        if($len>0)
            return substr($time,0,$len);
        return $time;
    }

    protected function genDatetime()
    {
        return date('Y-m-d H:i:s',time()-mt_rand(0,12*30*24*3600));
    }

    protected function genDate()
    {
        return date('Y-m-d',time()-mt_rand(0,12*30*24*3600));
    }

    protected function genYear($param)
    {
        $year=Date('Y',time()-mt_rand(0,12*30*24*3600));
        $param=intval($param);
        if(intval($param) > strlen($year)){
            $param=strlen($year);
        }
        return substr($year,strlen($year)-$param,$param);
    }

    protected function genDayTime()
    {
        return Date('H:i:s',time()-mt_rand(0,12*30*24*3600));
    }
}
