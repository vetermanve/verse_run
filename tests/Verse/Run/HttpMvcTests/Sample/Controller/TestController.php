<?php

namespace Verse\Run\HttpMvcTests\Sample\Controller;

use Verse\Run\Controller\BaseControllerProto;

class TestController extends BaseControllerProto
{
    const RESPONSE_GET = [
        'all' => 'ok',
    ];
    
    public function run()
    {
        return $this->{$this->method}();
    }

    public function validateMethod() : bool 
    {
        return \method_exists($this, $this->method);
    }
    
    public function get()
    {
        return self::RESPONSE_GET;
    }
}