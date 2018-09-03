<?php


namespace Run\Processor;


use Run\ChannelMessage\ChannelMsg;
use Run\Execution\Rest\MsgModificator\MsgModificatorProto;
use Run\RunModuleProto;
use Run\RunRequest;
use Run\Util\SessionBuilder;

abstract class RunRequestProcessorProto extends RunModuleProto
{
    /**
     * @var MsgModificatorProto[]
     */
    protected $msgModificators = [];
    
    /**
     * @var SessionBuilder
     */
    protected $sessionBuilder;
    
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
    
    public function addMsgModificator (MsgModificatorProto $modificator) 
    {
        $this->msgModificators[] = $modificator;
    }
    
    /**
     * @return SessionBuilder
     */
    public function getSessionBuilder()
    {
        return $this->sessionBuilder;
    }
    
    /**
     * @param SessionBuilder $sessionBuilder
     */
    public function setSessionBuilder($sessionBuilder)
    {
        $this->sessionBuilder = $sessionBuilder;
    }
}