<?php
/**
 * Filler 
 * 入口函数，读取数据库，读取配置文件，生成数据，批量插库
 * @package 
 * @version 0.0.1
 * @copyright 2014-2015
 * @author Ryan
 * @license MIT
 */
namespace Dbfiller;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Dbfiller\Gens\Gen;

class Filler
{
    private $config;
    private $gen_manager;
    const DEFAULT_ID="id";

    public function __construct()
    {
        $this->config=Config::get('dbfiller');
        $this->gen_manager=new GenManager();
    }

    /**
     * doFill 
     * 入口函数
     * @access public
     * @return void
     */
    public function fire()
    {
        Helper::info("Start..");
        foreach($this->config as $name=>$tab){
            $fields=DB::select("desc `$name`");
            $define=$tab['define'];
            Helper::info("Fill table:[$name]");
            $batchdata=[];

            for($i=0;$i<$tab['total'];$i++){
                $row=[];
                foreach($fields as $field){
                    if(0==strcasecmp($field->Extra,'auto_increment'))
                        continue;
                    $row[$field->Field]=$this->genRowWith($field,$define);
                }
                $batchdata[]=$row;
                if($i%100==99){
                    DB::table($name)->insert($batchdata);
                    Helper::info("Inserted rows ".$i);
                    $batchdata=[];
                }
            }

            if(count($batchdata)>0){
                DB::table($name)->insert($batchdata);
                Helper::info("Inserted rows ".sizeof($batchdata));
            }

            $this->gen_manager->fillEnd();
            Helper::info("Fill table:[$name] completed");
        }
    }

    /**
     * genRowWith 
     * 调用数据生成器
     * @param mixed $field 
     * @param mixed $define 
     * @access private
     * @return void
     */
    private function genRowWith($field,$define){
        if(isset($define[$field->Field])){
            $format_def=$define[$field->Field];
            if(isset($format_def['from'])){
                $from=explode(".",$format_def['from']);
                if(sizeof($from)!==3){
                    throw new Exception('Config error,'.$name."=>['define'=>".$field->Field."=>from");
                }
                $obj=$this;
                return $this->gen_manager->dataWithDataSource(
                    $from,
                    function($table,$field,$isrand,$position,$size) use($obj){
                        return $obj->fetchData($table,$field,$isrand,$position,$size);
                    }
                );
            }
            else{
                return $this->gen_manager->dataWithFormat($field->Field,$format_def);
            }
        }
        else{
            return $this->gen_manager->dataDefault($field);
        }
    }

    /**
     * fetchData 
     * 生成器从其他表中读取数据 ,业务做数据的缓存
     * @param mixed $table  表
     * @param mixed $field  字段
     * @param mixed $isrand 是否随机
     * @param mixed $start  起始点
     * @param mixed $size   读取多少
     * @access public
     * @return void
     */
    public function fetchData($table,$field,$isrand,$start,$size)
    {
        $conf=$this->config[$table];
        $key=self::DEFAULT_ID;

        if(isset($conf['key'])){
            $key=$conf['key'];
        }

        if($isrand==false){
            return DB::table($table)->select($field)->where($key,">",'0')->skip($start)->take($size)->get();
        }
        else{
            $data=DB::table($table)->select(DB::raw("max($key) as up,min($key) as down,count(*) as num"))->where($key,">",0)->get();
            if($data){

                $randkey=mt_rand($data[0]->down,$data[0]->up);
                if($randkey>($data[0]->up-$data[0]->down)/2){
                    $data=DB::table($table)->select($field)->where($key,"<",$randkey)->orderBy($key,'desc')->skip($start)->take($size)->get();
                }
                else{
                    $data=DB::table($table)->select($field)->where($key,">",$randkey)->orderBy($key,'asc')->skip($start)->take($size)->get();
                }

                $result=[];
                foreach($data as $row){
                    $result[]=$row->$field; 
                }
                return $result;
            }
            else{
                throw new Exception('Not found any data in table:'.$table);
            }
        }
        return [];
    }

}
