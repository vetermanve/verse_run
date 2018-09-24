<?php

namespace Verse\Run\Processor;

use Verse\Run\RunRequest;
use Verse\Run\Spec\HttpResponseSpec;

class SimplePageProcessor extends SimpleRestProcessor
{
    public function prepare()
    {
        parent::prepare(); 
        
        $this->defaultContentType = HttpResponseSpec::CONTENT_HTML;
    }

    protected function _getRequestMethod(RunRequest $request)
    {
        return $request->getResourcePart(1) ?: 'index';
    }
}