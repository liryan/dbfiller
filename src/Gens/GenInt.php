<?php
namespace Dbfiller\Gens;
use Exception;

class GenInt extends Gen implements IGen
{
    public function boot()
    {
        $obj=$this;
        parent::register([FormatParser::DB_INT],function(FormatParser $ctx) use($obj){
            if(strpos($ctx->param,",")!==false){
                return $obj->genIntByBytes($ctx->name,$ctx->param,$ctx->unique,$ctx->unsigned);
            }
            return $obj->genIntByBytes($ctx->name,4,$ctx->unique,$ctx->unsigned);
        });
        parent::register([FormatParser::DB_TINYINT],function($ctx) use($obj){
            return $obj->genIntByBytes($ctx->name,1,$ctx->unique,$ctx->unsigned);
        });
        parent::register([FormatParser::DB_SMALLINT],function($ctx) use($obj){
            return $obj->genIntByBytes($ctx->name,2,$ctx->unique,$ctx->unsigned);
        });
        parent::register([FormatParser::DB_MEDIUMINT],function($ctx) use($obj){
            return $obj->genIntByBytes($ctx->name,2,$ctx->unique,$ctx->unsigned);
        });
        parent::register([FormatParser::DB_BIGINT],function($ctx) use($obj){
            return $obj->genIntByBytes($ctx->name,4,$ctx->unique,$ctx->unsigned);
            //return $obj->genIntByBytes($ctx->name,4,$ctx->unique,$ctx->unsigned); //need PHP 64bit
        });
    }

    protected function genIntByBytes($name,$param,$uniq,$unsigned)
    {
        $min=1;
        $max=0;
        if(false!==strpos($param,",")){
            $range=explode(",",$param);
            $min=min($range);
            $max=max($range);
            $bits=mt_rand($min,$max);

            $min=pow(10,($bits-1));
            $max=pow(10,$bits)-1;
        }
        else{
            $max=pow(2,8*intval($param))-1;
        }
        if($uniq){
            return $min+$this->incr($name);
        }
        else{
            if($unsigned){
                return mt_rand($min,$max);
            }
            else{
                if(mt_rand(1,10)%2==1){
                    return mt_rand($min,intval($max/2));
                }
                return 0-mt_rand($min,intval($max/2));
            }
        }
    }
}
