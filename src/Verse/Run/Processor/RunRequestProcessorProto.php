<?php


namespace Verse\Run\Processor;


use Verse\Run\ChannelMessage\ChannelMsg;
use Verse\Run\Interfaces\MessageModificator;
use Verse\Run\Interfaces\RequestRouterInterface;
use Verse\Run\RunModuleProto;
use Verse\Run\RunRequest;

abstract class RunRequestProcessorProto extends RunModuleProto
{
    /**
     * @var MessageModificator[]
     */
    protected $msgModificators = [];

    /**
     * @var RequestRouterInterface
     */
    protected $requestRouter;

    abstract public function prepare();
    abstract public function process(RunRequest $request);
    
    public function sendResponse (ChannelMsg $response, RunRequest $request) 
    {
        if ($this->msgModificators) {
            foreach ($this->msgModificators as &$modificator) {
                $modificator->process($request, $response);
            }
        }
        
        return $this->core->getDataChannel()->send($response);
    }
    
    public function addMsgModificator (MessageModificator $modificator) 
    {
        $this->msgModificators[] = $modificator;
    }

    /**
     * @return RequestRouterInterface
     */
    public function getRequestRouter(): RequestRouterInterface
    {
        return $this->requestRouter;
    }

    /**
     * @param RequestRouterInterface $requestRouter
     */
    public function setRequestRouter(RequestRouterInterface $requestRouter)
    {
        $this->requestRouter = $requestRouter;
    }
}