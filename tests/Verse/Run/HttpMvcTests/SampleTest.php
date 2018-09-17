<?php

namespace Verse\Run\HttpMvcTests;

use Verse\Run\Channel\MemoryStoreChannel;
use Verse\Run\Component\CreateDependencyContainer;
use Verse\Run\Processor\SimpleRestProcessor;
use Verse\Run\Provider\RegularHttpRequestProvider;
use Verse\Run\RequestRouter\BasicMvcRequestRouter;
use Verse\Run\RunContext;
use Verse\Run\RunCore;
use Verse\Run\RuntimeLog;
use Verse\Run\HttpMvcTests\Sample\Controller\TestController;
use Verse\Run\Spec\HttpResponseSpec;
use Verse\Run\Util\HttpEnvContext;

class SampleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * This is a test
     */
    public function testHttpEnvRequestProcessing () 
    {
        $core = new RunCore();
        $core->addComponent(new CreateDependencyContainer());
        /* Create Run context */
        
        $core->setContext(new RunContext());
        $core->setRuntime(new RuntimeLog());
        
        /* Create provider */
        $httpEnv = new HttpEnvContext();
        $httpEnv->setScope(HttpEnvContext::HTTP_SERVER, 'REQUEST_URI', '/sample-test-controller');
        
        $provider = new RegularHttpRequestProvider();
        $provider->setHttpEnv($httpEnv);
        
        $core->setProvider($provider);
        
        /* Create processor */
        $processor = new SimpleRestProcessor();
        
        $requestRouter = new BasicMvcRequestRouter();
        $requestRouter->setRootNamespace(__NAMESPACE__);
        
        $processor->setRequestRouter($requestRouter);
        $core->setProcessor($processor);
        
        
        /* Create data channel */
        $channel = new MemoryStoreChannel();
        $core->setDataChannel($channel);
        
        $core->configure();
        $core->prepare();
        $core->run();
        
        $resultMessage = $channel->getMessage();

        $this->assertEquals($resultMessage->getCode(), HttpResponseSpec::HTTP_CODE_OK, $resultMessage->getBody());
        $this->assertEquals($resultMessage->getBody(), TestController::RESPONSE_GET);
    }
}