<?php


namespace Run\ChannelMessage;


use Run\Spec\HttpResponseSpec;

class HttpReply extends ChannelMsg
{
    public function setHeader($key, $val)
    {
        $this->meta[HttpResponseSpec::META_HTTP_HEADERS][$key] = $val;
    }
    
    public function setHeaders($headers)
    {
        if (!isset($this->meta[HttpResponseSpec::META_HTTP_HEADERS])) {
            $this->meta[HttpResponseSpec::META_HTTP_HEADERS] = [];
        }
        
        $this->meta[HttpResponseSpec::META_HTTP_HEADERS] = (array)$headers + $this->meta[HttpResponseSpec::META_HTTP_HEADERS];
    }
}