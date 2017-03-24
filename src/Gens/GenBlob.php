<?php

namespace Dbfiller\Gens;
use Exception;

class GenBlob extends Gen implements IGen
{

    public function boot()
    {
        $obj=$this;

        parent::register(
            [
                FormatParser::DB_TINYBLOB,
                FormatParser::DB_BLOB,
                FormatParser::DB_LONGBLOB,
                FormatParser::DB_MEDIUMBLOB,
                FormatParser::DB_BINARY,
                FormatParser::DB_VARBINARY,
            ],
            function(FormatParser $ctx) use($obj) {
                return $obj->genBinary();
            }
        );
    } 

    protected function genBinary()
    {
        return mt_rand(0,8);
    }

}
