<?php


namespace Run\Channel;


use Mu\Env;
use Router\Router;
use Run\ChannelMessage\ChannelMsg;
use Run\RunContext;
use Run\Spec\HttpResponseSpec;

class AmqpReplyChannel extends DataChannelProto
{
    const UID   = 'uid';
    const CODE  = 'code';
    const HEAD  = 'head';
    const BODY  = 'body';
    const STATE = 'state';
    const FROM = 'from';
    
    /**
     * @var Router
     */
    private $router;
    
    private $identity;
    private $replyHost;
    private $replyPort;
    
    public function prepare()
    {
        $this->router = Env::getRouter();
        $this->identity = $this->context->get(RunContext::IDENTITY);
        $this->replyHost = $this->context->get(RunContext::AMQP_REQUEST_CLOUD_HOST);
        $this->replyPort = $this->context->get(RunContext::AMQP_REQUEST_CLOUD_PORT);
    }
    
    public function send(ChannelMsg $msg)
    {
        $stateObj = $msg->getChannelState();
        $expires = $stateObj->getExpiresAt();
        $stateData = [];
        
        foreach ($stateObj->pack() as $key => $body) {
            $stateData[$key] = [$body, $expires[$key]];    
        }
        
        $payload = [
            self::UID  => $msg->getUid(),
            self::BODY => $msg->getBody(),
            self::CODE => $msg->getCode(),
            self::HEAD => $msg->getMeta(HttpResponseSpec::META_HTTP_HEADERS, new \stdClass()),
            self::STATE => $stateData ?: new \stdClass(),
            self::FROM => $this->identity ?: 'some_host',
        ];
        
        $this->router->registerQueue($msg->getDestination(), $this->replyHost, $this->replyPort);
        $this->router->publish($payload, $msg->getDestination());
        
        $this->getCore()->getRuntime()->runtime('RUN_AMQP_REPLY_SENT', ['request_id' => $msg->getUid(), 'to' => $msg->getDestination()]);
    }
}