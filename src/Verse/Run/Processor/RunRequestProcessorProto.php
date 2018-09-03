<?php


namespace Verse\Run\Processor;


use Verse\Run\ChannelMessage\ChannelMsg;
use Verse\Run\Execution\Rest\MsgModificator\MsgModificatorProto;
use Verse\Run\RunModuleProto;
use Verse\Run\RunRequest;
use Verse\Run\Util\SessionBuilder;

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