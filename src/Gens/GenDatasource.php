<?php
namespace Dbfiller\Gens;
use Exception;

class GenDataSource extends Gen implements IGen
{
    private $data_cache;

    const MAX_CACHE_COUNT=500;

    public function boot()
    {
        $this->data_cache=[];
        $obj=$this;

        parent::register(['datasource'],function(FormatParser $ctx) use($obj) {
            return $obj->genData($ctx);
        });
    } 

    public function genData($ctx)
    {
        $isrand="";
        if($ctx->unique==true)
            $isrand=false;
        else
            $isrand=true;
        if(!isset($this->data_cache[$ctx->name]) || 
            count($this->data_cache[$ctx->name]['data'])==0){

            $position=!isset($this->data_cache[$ctx->name])?0:$this->data_cache[$ctx->name]['position'];
            if($isrand){
                $position=0;
            }
            $data=call_user_func($ctx->datasource,$ctx->table,$ctx->name,$isrand,$position,self::MAX_CACHE_COUNT);
            $this->data_cache[$ctx->name]=['position'=>$position+self::MAX_CACHE_COUNT,'data'=>$data];
        }

        return array_shift($this->data_cache[$ctx->name]['data']);
    }

}
