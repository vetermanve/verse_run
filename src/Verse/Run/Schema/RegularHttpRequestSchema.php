<?php


namespace Verse\Run\Schema;


use Verse\Run\Channel\JsonHttpResponseChannel;
use Verse\Run\Component\CreateDependencyContainer;
use Verse\Run\Component\UnexpectedShutdownHandler;
use Verse\Run\Processor\SimpleRestProcessor;
use Verse\Run\Provider\RegularHttpRequestProvider;
use Verse\Run\Util\HttpEnvContext;

class RegularHttpRequestSchema extends PreconfiguredSchemaProto
{
    /**
     * @var HttpEnvContext
     */
    private $httpEnv;
    
    public function configure()
    {
        $provider = new RegularHttpRequestProvider();
        $provider->setHttpEnv($this->httpEnv);
    
        $this->core->addComponent(new UnexpectedShutdownHandler());
        $this->core->addComponent(new CreateDependencyContainer());
        
        $this->_addCustomComponents();
        
        $this->core->setProvider($provider);
        $this->core->setProcessor($this->processor ?? new SimpleRestProcessor());
        $this->core->setDataChannel(new JsonHttpResponseChannel());
    }
    
    /**
     * @param HttpEnvContext $httpEnv
     */
    public function setHttpEnv($httpEnv)
    {
        $this->httpEnv = $httpEnv;
    }
}