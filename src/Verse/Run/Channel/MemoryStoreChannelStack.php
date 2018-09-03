<?php


namespace Run\Channel;


use Run\ChannelMessage\ChannelMsg;

class MemoryStoreChannelStack extends DataChannelProto
{
    /**
     * @var ChannelMsg
     */
    private $messages = [];
    
    /**
     * Подготовка к отправке данных
     *
     * @return mixed
     */
    public function prepare()
    {
        // TODO: Implement prepare() method.
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
        $this->messages[$msg->getUid()] = $msg;
    }
    
    /**
     * @return ChannelMsg
     */
    public function getMessageByUid($uid)
    {
        return $this->messages[$uid] ?? null;
    }
    
}