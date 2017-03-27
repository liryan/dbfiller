<?php
namespace Dbfiller\Gens;
use Exception;

class GenFloat extends Gen implements IGen
{

    public function boot()
    {
        $obj=$this;

        parent::register([FormatParser::DB_FLOAT,FormatParser::DB_DECIMAL,FormatParser::DB_DOUBLE],function(FormatParser $ctx) use($obj) {
            return $obj->genFloat($ctx->param,$ctx->unsigned);
        });

    } 

    protected function genFloat($param,$unsigned)
    {
        $intbit=8;
        $pointbit=2;
        $min=0;
        $max=0;
        if(false!==strpos($param,",")){
            $range=explode(",",$param);
            $pointbit=$range[1];
            $intbit=mt_rand(0,$range[0]);
            $max=pow(10,$pointbit+$intbit)-1;
        }
        elseif(intval($param)>0){
            $max=pow(10,intval($param))-1;
        }
        else{
            $max=pow(10,$intbit)-1;
        }
        if($unsigned){
            return sprintf("%.0${pointbit}f",mt_rand(0,$max)/pow(10,$pointbit));
        }
        else{
            if(mt_rand(0,9)%2==1){
                return sprintf("%.0${pointbit}f",mt_rand(0,$max)/pow(10,$pointbit));
            }
            return 0-sprintf("%.0${pointbit}f",mt_rand(0,$max)/pow(10,$pointbit));
        }
    }

}
