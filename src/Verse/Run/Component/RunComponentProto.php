<?php


namespace Verse\Run\Component;


use Verse\Run\RunModuleProto;

abstract class RunComponentProto extends RunModuleProto
{
    abstract public function run();
}