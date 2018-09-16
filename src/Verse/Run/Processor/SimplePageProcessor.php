<?php

namespace App\Base\Run;

use Verse\Run\Processor\SimpleRestProcessor;
use Verse\Run\RunRequest;

class SimplePageProcessor extends SimpleRestProcessor
{
    protected function _getRequestMethod(RunRequest $request)
    {
        return $request->getResourcePart(1) ?: 'index';
    }
}