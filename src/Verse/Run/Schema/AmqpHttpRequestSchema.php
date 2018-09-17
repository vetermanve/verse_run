<?php


namespace Verse\Run\Schema;

use Verse\Run\Channel\AmqpReplyChannel;
use Verse\Run\Component\CreateDependencyContainer;
use Verse\Run\Component\UnexpectedShutdownHandler;
use Verse\Run\Processor\SimpleRestProcessor;
use Verse\Run\Provider\AmqpHttpRequestProvider;

class AmqpHttpRequestSchema extends PreconfiguredSchemaProto
{
    
    public function configure()
    {
        $this->core->addComponent(new UnexpectedShutdownHandler());
        $this->core->addComponent(new CreateDependencyContainer());
        
        $this->_addCustomComponents();
            
        $this->core->setProvider(new AmqpHttpRequestProvider());
        $this->core->setProcessor($this->processor ?? new SimpleRestProcessor());
        $this->core->setDataChannel(new AmqpReplyChannel());
    }
}