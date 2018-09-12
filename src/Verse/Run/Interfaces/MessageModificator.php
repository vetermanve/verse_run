<?php


namespace Verse\Run\Interfaces;

use Verse\Run\ChannelMessage\ChannelMsg;
use Verse\Run\RunRequest;

interface MessageModificator 
{
    public function process (RunRequest $request, ChannelMsg $message); 
}