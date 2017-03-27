<?php
namespace Dbfiller\Gens;
use Exception;

class GenChar extends Gen implements IGen
{
    private $chars="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    const MAX_WORDS=500;
    const DEFAULT_CHAR_LEN=32;

    public function boot(){
        $obj=$this;
        parent::register([FormatParser::DB_TEXT,FormatParser::DB_LONGTEXT,FormatParser::DB_MEDIUMTEXT],function(FormatParser $ctx) use($obj) {
            return $obj->genChar($ctx->name,$ctx->param,$ctx->unique,$ctx->unsigned,true);
        });
        parent::register([FormatParser::DB_VARCHAR,FormatParser::DB_CHAR],function(FormatParser $ctx) use($obj){
            return $obj->genChar($ctx->name,$ctx->param,$ctx->unique,$ctx->unsigned,false);
        });
    } 

    protected function genChar($name,$param,$uniq,$unsigned,$word=false)
    {
        static $gen_counter=1; 
        if($gen_counter++%20==1){
            $this->chars=str_shuffle($this->chars);
        }
        $width=0;
        if(false!==strpos($param,",")){
            $range=explode(",",$param);
            $maxbit=$range[0];
            $minbit=$range[1];
            if($maxbit==$minbit)
                $width=$maxbit;
            else
                $width=mt_rand($minbit,$maxbit);
        }
        else{
            $width=mt_rand(1,intval($param)>1?intval($param):self::DEFAULT_CHAR_LEN);
        }
        $result="";
        if($uniq){
            $result=$this->incr($name);
        }

        $counter=0;
        while($counter++<self::MAX_WORDS){
            $ww=mt_rand(1,8);
            $start=mt_rand(0,strlen($this->chars)-$ww);
            $words=substr($this->chars,$start,$ww);
            $result.=($word&&$result?",":"").$words;
            if(strlen($result)>$width){
                return substr($result,0,$width);
            }

        }
        return $result;
    }

}
