<?php


namespace Verse\Run\Tests;


use Verse\Run\Channel\MemoryStoreChannel;
use Verse\Run\Component\UnexpectedShutdownHandler;
use Verse\Run\RunCore;
use Verse\Run\RuntimeLog;
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