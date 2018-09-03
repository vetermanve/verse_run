<?php


namespace Run\Channel;


use Run\ChannelMessage\ChannelMsg;
use Run\RunModuleProto;

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