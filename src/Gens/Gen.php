<?php
/**
 * Gen 
 * 生成器基类
 * @package 
 * @version 0.0.1
 * @copyright 2014-2015
 * @author Ryan
 * @license MIT
 */
namespace Dbfiller\Gens;

use Exception;
use Closure;

class Gen
{
    private $genid;
    protected static $map_type_gen;
    protected static $instance=[];

    /**
     * with 
     * 注册生成器帮助函数
     * @param mixed $instance 
     * @static
     * @access public
     * @return void
     */
    public static function with($instance)
    {
        if(isset(static::$instance[get_class($instance)])){
            return $instance;
        }
        static::$instance[get_class($instance)]=$instance;
        return $instance;
    }

    /**
     * init 
     * 注册生成器
     * @access public
     * @return void
     */
    public function init()
    {
        static::with(new GenInt())->boot();
        static::with(new GenChar())->boot();
        static::with(new GenTime())->boot();
        static::with(new GenBlob())->boot();
        static::with(new GenBit())->boot();
        static::with(new GenEnum())->boot();
        static::with(new GenFloat())->boot();
        static::with(new GenDatasource())->boot();
    }

    /**
     * reset 
     * 重置计数
     * @access public
     * @return void
     */
    public function reset()
    {
        foreach(static::$instance as $inst){
            $inst->genid=[];
        }    
    }

    /**
     * register 
     * 注册对应字段类型的生成器
     * @param mixed $types 
     * @param Closure $gen 
     * @access protected
     * @return void
     */
    protected function register($types,Closure $gen)
    {
        if(!static::$map_type_gen){
            static::$map_type_gen=[];
        }
        foreach($types as $k){
            static::$map_type_gen[$k]=$gen;
        } 
    }

    /**
     * do 
     * 开始生成
     * @param FormatParser $ctx 
     * @access public
     * @return void
     */
    public function make(FormatParser $ctx)
    {
        if(isset(static::$map_type_gen[$ctx->type])){
            return call_user_func(static::$map_type_gen[$ctx->type],$ctx);
        }
        else{
            throw new Exception('[BUG] Not find the type of "'.$ctx->type.'"');
        }
    }

    /**
     * incr 
     * 自增
     * @param mixed $name 
     * @access public
     * @return void
     */
    public function incr($name)
    {
        if(!isset($this->genid[$name])){
            $this->genid[$name]=0;
        }
        return 1+($this->genid[$name]++);
    }
}
