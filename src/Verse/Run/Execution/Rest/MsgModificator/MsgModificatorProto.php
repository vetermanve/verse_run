<?php


namespace Verse\Run\Execution\Rest\MsgModificator;


use Verse\Run\ChannelMessage\ChannelMsg;
use Verse\Run\RunRequest;

abstract class MsgModificatorProto 
{
    abstract public function process (RunRequest $request, ChannelMsg $message); 
}