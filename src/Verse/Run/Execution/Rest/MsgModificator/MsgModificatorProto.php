<?php


namespace Run\Execution\Rest\MsgModificator;


use Run\ChannelMessage\ChannelMsg;
use Run\RunRequest;

abstract class MsgModificatorProto 
{
    abstract public function process (RunRequest $request, ChannelMsg $message); 
}