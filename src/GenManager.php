<?php
/**
 * GenManager 
 * 负责生成数据
 * @package 
 * @version 0.1 
 * @copyright 2014-2015 
 * @license MIT
 */
namespace Dbfiller;
use Exception;
use Dbfiller\Gens\Gen;
use Dbfiller\Gens\FormatParser;

class GenManager
{
    private $gens;

    public function __construct()
    {
        $this->gens=new Gen();
        $this->gens->init();
    }

    /**
     * fillEnd 
     * 重置唯一数据的计数
     * @access public
     * @return void
     */
    public function fillEnd()
    {
        $this->gens->reset();
    }

    /**
     * dataWithDatasource 
     * 通过数据源生成数据，读取另外一个表的数据
     * @param mixed $from 
     * @param mixed $clouser 
     * @access public
     * @return void
     */
    public function dataWithDatasource($name,$from,$closure)
    {
        $ctx=new FormatParser();
        $ctx->initWithDatasource($name,$from,$closure);
        return $this->gens->make($ctx);
    }

    /**
     * dataWithFormat 
     * 根据配置文件的格式生成数据
     * @param mixed $name 
     * @param mixed $define 
     * @access public
     * @return void
     */
    public function dataWithFormat($name,$define)
    {
        $format=$define['format'];
        $obj=$this;
        if(is_string($format)){
            $result=preg_replace_callback(
                FormatParser::formatPattern(),
                function($matches) use($obj,$name){
                    $ctx=new FormatParser();
                    $ctx->initWithFormat($name,$matches);
                    return $obj->gens->make($ctx);
                },
                $format
            );
            if($result==$format){
                throw new Exception("Error format:".$format);
            }
            return $result;
        }
        else{
            return call_user_func($format);
        }
    }
    
    /**
     * dataDefault 
     * 根据数据表字段定义生成数据
     * @param mixed $field 
     * @access public
     * @return void
     */
    public function dataDefault($field)
    {
        if(preg_match(FormatParser::typePattern(),$field->Type,$match)){
            $ctx=new FormatParser();
            $ctx->initWithField($field,$match);
            return $this->gens->make($ctx);
        }
        else{
            throw new Exception("[BUG] can't parse the defination of table field");
        }
    }

}
