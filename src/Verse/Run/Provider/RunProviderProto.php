<?php


namespace Verse\Run\Provider;


use Verse\Run\RunModuleProto;

abstract class RunProviderProto extends RunModuleProto
{
    abstract public function prepare();
    abstract public function run();
}