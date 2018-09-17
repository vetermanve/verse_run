<?php

namespace Verse\Run\Processor;

use Verse\Run\RunRequest;

class SimplePageProcessor extends SimpleRestProcessor
{
    protected function _getRequestMethod(RunRequest $request)
    {
        return $request->getResourcePart(1) ?: 'index';
    }
}