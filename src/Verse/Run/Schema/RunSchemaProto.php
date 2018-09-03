<?php


namespace Verse\Run\Schema;


use Verse\Run\RunModuleProto;

abstract class RunSchemaProto extends RunModuleProto
{
    abstract public function configure();
    
    public function log ($info) 
    {
        $this->runtime->debug($info);
    }
}