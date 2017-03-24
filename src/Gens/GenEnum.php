<?php
namespace Dbfiller\Gens;
use Exception;

class GenEnum extends Gen implements IGen
{

    public function boot()
    {
        $obj=$this;

        parent::register([FormatParser::DB_ENUM,FormatParser::DB_SET],function(FormatParser $ctx) use($obj) {
            return $obj->genEnum($ctx->param);
        });
    } 

    protected function genEnum($param)
    {
        $values=explode(",",str_replace("'","",$param));
        return $values[mt_rand(0,count($values)-1)];
    }

}
