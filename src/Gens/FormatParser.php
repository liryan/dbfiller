<?php
namespace Dbfiller\Gens;
use Exception;

class FormatParser{
    public const FLAG_UNIQ=     '#';
    public const FLAG_NORM=     '%';

    public const FLAG_STR=      's';
    public const FLAG_FL=       'f';
    public const FLAG_UFL=      'p';
    public const FLAG_INT=      'd';
    public const FLAG_UINT=     'u';
    
    public const DB_BIT=        'bit';
    public const DB_INT=        'int';
    public const DB_TINYINT=    'tinyint';
    public const DB_SMALLINT=   'smallint';
    public const DB_BIGINT=     'bigint';
    public const DB_MEDIUMINT=  'mediumint';
    public const DB_TINYBLOB=   'tinyblob';
    public const DB_BLOB=       'blob';
    public const DB_LONGBLOB=   'longblob';
    public const DB_MEDIUMBLOB= 'mediumblob';
    public const DB_BINARY=     'binary';
    public const DB_VARBINARY=  'varbinary';
    public const DB_TEXT=       'text';
    public const DB_LONGTEXT=   'longtext';
    public const DB_MEDIUMTEXT= 'mediumtext';
    public const DB_VARCHAR=    'varchar';
    public const DB_CHAR=       'char';
    public const DB_ENUM=       'enum';
    public const DB_SET=        'set';
    public const DB_DATETIME=   'datetime';
    public const DB_TIME=       'time';
    public const DB_DATE=       'date';
    public const DB_YEAR=       'year';
    public const DB_TIMESTAMP=  'timestamp';
    public const DB_FLOAT=      'float';
    public const DB_DOUBLE=     'double';
    public const DB_DECIMAL=    'decimal';

    public const DATA_SOURCE=   'datasource';


    public  $param;
    public  $unsigned;
    public  $name;
    public  $unique;
    public  $type;
    private $flag;
    public  $table;
    public  $datasource;

    public static function formatPattern()
    {
        return  "/(".FormatParser::FLAG_UNIQ."|".FormatParser::FLAG_NORM.
                ")([0-9]+)(\-[0-9]+)?([".
                FormatParser::FLAG_UINT.
                FormatParser::FLAG_STR.
                FormatParser::FLAG_FL.
                FormatParser::FLAG_INT.
                FormatParser::FLAG_UFL."]{1})/i";
    }

    public static function typePattern()
    {
        return '/([a-z]+)(\([^\)]+\))?( unsigned)?/i';
    }

    public function initWithDefine($field,$matches)
    {
        $this->type=$matches[1];
        $this->param='';
        $this->unsigned=false;
        if(isset($matches[2])){
            $this->param=trim($matches[2],",()");
        }
        if( 
            (isset($matches[3]) && 0==strcasecmp(trim($matches[3]),'unsigned')) ||
            (isset($matches[4]) && 0==strcasecmp(trim($matches[4]),'unsigned')) 
        ){
            $this->unsigned=true;
        }
        $this->name=$field->Field;
    }

    public function initWithFormat($name,$matches)
    {
        if($matches[1]==FormatParser::FLAG_UNIQ){
            $this->unique=true;
        }
        if($matches[1]==FormatParser::FLAG_NORM){
            $this->unique=false;
        }
        if($matches[3]){
            $this->param=sprintf("%d,%d",$matches[2],$matches[3]);
        }
        else{
            $this->param=sprintf("%d,%d",$matches[2],$matches[2]);
        }
        $this->name=$name;
        $this->flag=$matches[4];
        $this->unsigned=true;
        switch($this->flag){
        case FormatParser::FLAG_STR:
            $this->type='char';
            break;
        case FormatParser::FLAG_INT:
            $this->unsigned=false;
            $this->type='int';
        case FormatParser::FLAG_UINT:
            $this->type='int';
            $this->unsigned=true;
            break;
        case FormatParser::FLAG_FL:
            $this->unsigned=false;
            $this->type='float';
            break;
        case FormatParser::FLAG_UFL:
            $this->unsigned=true;
            $this->type='float';
        } 
    }

    public function initWithDatasource($from,$clouser)
    {
        switch($from[0]){
            case FormatParser::FLAG_NORM:
            $this->unique=false;
            break;
            case FormatParser::FLAG_UNIQ:
            $this->unique=true;
            break;
        } 
        $this->datasource=$clouser;
        $this->type='datasource';
        $this->table=$from[1];
        $this->name=$from[2];
    }
}

