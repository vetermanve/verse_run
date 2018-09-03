<?php


namespace Run\Provider;


use Run\RunModuleProto;

abstract class RunProviderProto extends RunModuleProto
{
    abstract public function prepare();
    abstract public function run();
    
//    public function log ($str, $data = []) 
//    {
//        $this->runtime->debug($str, $data);
//    }
}