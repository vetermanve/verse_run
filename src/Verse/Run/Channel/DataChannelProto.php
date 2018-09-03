<?php


namespace Verse\Run\Channel;


use Verse\Run\ChannelMessage\ChannelMsg;
use Verse\Run\RunModuleProto;

abstract class DataChannelProto extends RunModuleProto
{
    /**
     * Подготовка к отправке данных
     * 
     * @return mixed
     */
    abstract public function prepare();
    
    /**
     * Непосредственно отпрвка данных
     * 
     * @param $msg
     *
     * @return null
     */
    abstract public function send(ChannelMsg $msg);
}