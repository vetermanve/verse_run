<?php


namespace Run\Component;


use Run\RunModuleProto;

abstract class RunComponentProto extends RunModuleProto
{
    abstract public function run();
}