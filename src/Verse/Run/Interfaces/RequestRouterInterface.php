<?php


namespace Verse\Run\Interfaces;


use Verse\Run\RunRequest;

interface RequestRouterInterface
{
    public function getClassByRequest (RunRequest $request); 
}