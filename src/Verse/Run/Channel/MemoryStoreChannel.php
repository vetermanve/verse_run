<?php


namespace Verse\Run\Channel;


use Verse\Run\ChannelMessage\ChannelMsg;

class MemoryStoreChannel extends DataChannelProto
{
    /**
     * @var ChannelMsg
     */
    private $message;
    
    /**
     * Подготовка к отправке данных
     *
     * @return mixed
     */
    public function prepare()
    {
        
    }
    
    /**
     * Непосредственно отпрвка данных
     *
     * @param $msg
     *
     * @return null
     */
    public function send(ChannelMsg $msg)
    {
        $this->message = $msg;
    }
    
    /**
     * @return ChannelMsg
     */
    public function getMessage()
    {
        return $this->message;
    }
    
}