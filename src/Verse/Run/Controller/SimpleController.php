<?php


namespace Verse\Run\Controller;


class SimpleController extends BaseControllerProto
{

    public function run()
    {
        return $this->{$this->method}();
    }

    public function validateMethod() : bool 
    {
        return method_exists($this, $this->method);
    }
}