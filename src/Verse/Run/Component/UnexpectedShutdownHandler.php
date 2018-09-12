<?php


namespace Verse\Run\Component;


use Verse\Run\ChannelMessage\ChannelMsg;
use Verse\Run\RunContext;
use Verse\Run\Spec\HttpRequestMetaSpec;
use Verse\Run\Spec\HttpResponseSpec;
use Verse\Run\Util\UnexpectedEndHandler;

class UnexpectedShutdownHandler extends RunComponentProto
{
    public function run()
    {
        UnexpectedEndHandler::addCallback([$this, 'rescue']);
    }
    
    public function rescue ($error)
    {
        $lastRequest = $this->core->getLastRequest();
        if (!$lastRequest) {
            $this->runtime->critical('RUN_CRASH ', ['error' => $error]);
            return ;
        }
        
        $this->runtime->critical('RUN_CRASH after request', [
            'request_id' => $lastRequest->getUid(),
            'resource' => $lastRequest->getResource(),
            'data' => $lastRequest->data,
            'params' => $lastRequest->params,
            'error' => $error,
        ]);
        
        $msg = new ChannelMsg();
        $msg->setUid($lastRequest->getUid());
        $msg->body = [
            'error' => 'Internal Error.',
        ];
        
        if ($this->context->getEnv(RunContext::ENV_DEBUG)) {
            $msg->body['error_details'] = $error;
        }
        
        $msg->setChannelState($lastRequest->getChannelState());
        $msg->setCode(HttpResponseSpec::HTTP_CODE_ERROR);
        $msg->setDestination($lastRequest->getReply());
        
        $this->core->getDataChannel()->send($msg);
    }
}