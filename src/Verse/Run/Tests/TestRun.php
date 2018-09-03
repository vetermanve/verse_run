<?php


namespace Run\Tests;


use Run\Channel\MemoryStoreChannel;
use Run\Component\UnexpectedShutdownHandler;
use Run\RunCore;
use Run\RuntimeLog;
use Testing\TestBase;

require_once __DIR__.'/../../Testing/bootstrap.php';

class TestRun extends TestBase
{
//    public function testShutdown () 
//    {
//        $storage = new MemoryStoreChannel();
//        $core = new RunCore();
//        $core->addComponent(new UnexpectedShutdownHandler());
//        $core->setDataChannel($storage);
//        $core->setRuntime(new RuntimeLog());
//        $core->prepare();
//        
//        $_ENV->asdf();
//    }

    public function testEventDispatch () 
    {
        
    }
}