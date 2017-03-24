<?php

namespace Dbfiller\Gens;
use Exception;

class GenBit extends Gen implements IGen
{

    public function boot()
    {
        $obj=$this;

        parent::register([FormatParser::DB_BIT],function(FormatParser $ctx) use($obj) {
            return $obj->genBit($ctx->param);
        });
    } 

    protected function genBit($param)
    {
        return mt_rand(0,pow(2,intval($param))-1);
    }

}
