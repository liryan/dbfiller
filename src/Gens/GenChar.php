<?php
namespace Dbfiller\Gens;
use Exception;

class GenChar extends Gen implements IGen
{
    private $chars="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    public const MAX_WORDS=500;
    public const DEFAULT_CHAR_LEN=32;

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
        if($gen_counter++%100==1){
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

    protected function genDatetime($name,$param,$uniq,$unsigned)
    {
        return date('Y-m-d H:i:s',time()-mt_rand(0,12*30*24*3600));
    }

    protected function genDate($name,$param,$uniq,$unsigned)
    {
        return date('Y-m-d',time()-mt_rand(0,12*30*24*3600));
    }

    protected function genFloat($name,$param,$uniq,$unsigned)
    {
        $intbit=8;
        $floatbit=2;
        if(false!==strpos($param,",")){
            $range=explode(",",$param);
            $intbit=$range[0];
            $floatbit=$range[1];
        }
        if($unsigned){
            return mt_rand(0,pow(2,$intbit)-1)/pow(10,$floatbit);
        }
        else{
            if(mt_rand(1,10)%2==1){
                return mt_rand(0,floor( (pow(2,$intbit)-1)/2 ))/pow(10,$floatbit);
            }
            return 0-mt_rand(0,floor( (pow(2,$intbit)-1)/2 ))/pow(10,$floatbit);
        }
    }

    protected function genBinary($name,$param,$uniq,$unsigned)
    {
        return mt_rand(0,999999);
    }

    protected function genTime($name,$param,$uniq,$unsigned)
    {
        return time()-mt_rand(0,12*30*24*3600);
    }

    protected function genEnum($name,$param,$uniq,$unsigned)
    {
        $values=explode(",",str_replace("'","",$param));
        return $values[mt_rand(0,count($values)-1)];
    }

    protected function genYear($name,$param,$uniq,$unsigned)
    {
        $year=Date('Y',time()-mt_rand(0,12*30*24*3600));
        $param=intval($param);
        if(intval($param) > strlen($year)){
            $param=strlen($year);
        }
        return substr($year,strlen($year)-$param,$param);
    }

    protected function genBit($name,$param,$uniq,$unsigned)
    {
        return mt_rand(0,pow(2,intval($param)));
    }

}
