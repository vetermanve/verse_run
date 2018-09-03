<?php


namespace Run\Rest\Exception;


class Redirect extends \Exception
{
    private $url;
    
    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }
    
    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }
}