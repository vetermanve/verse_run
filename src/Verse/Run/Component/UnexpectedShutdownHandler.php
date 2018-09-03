<?php


namespace Run\Component;


use Run\ChannelMessage\ChannelMsg;
use Run\RunContext;
use Run\Spec\HttpRequestMetaSpec;
use Run\Spec\HttpResponseSpec;
use Run\Util\UnexpectedEndHandler;

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
        
        if ($this->context->getEnv(RunContext::ENV_DEBUG) || $lastRequest->getMetaItem(HttpRequestMetaSpec::REQUEST_HEADERS, 'x-real-debug') == 'awesome') {
            $msg->body['error_details'] = $error;
        }
        
        $msg->setChannelState($lastRequest->getChannelState());
        $msg->setCode(HttpResponseSpec::HTTP_CODE_ERROR);
        $msg->setDestination($lastRequest->getReply());
        
        $this->core->getDataChannel()->send($msg);
    }
}