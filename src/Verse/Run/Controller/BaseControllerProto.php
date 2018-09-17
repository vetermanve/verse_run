<?php


namespace Verse\Run\Controller;


use Verse\Run\Interfaces\HttpRequestDataWrapperInterface;

abstract class BaseControllerProto
{
    /**
     * @var HttpRequestDataWrapperInterface
     */
    protected $requestWrapper;
    
    /**
     * Requested method
     * 
     * @var string
     */
    protected $method = 'index';
    
    abstract public function run();
    
    abstract public function validateMethod() : bool;
    
    final public function p($name, $default = null)
    {
        return $this->requestWrapper->getParam($name, $default);
    }
    
    public function getState ($name, $default = null)
    {
        return $this->requestWrapper->getState($name, $default);
    }
    
    public function setState ($name, $value, $ttl = null)
    {
        $this->requestWrapper->setState($name, $value, $ttl);
    }
    
    /**
     * @param HttpRequestDataWrapperInterface $requestWrapper
     */
    public function setRequestWrapper(HttpRequestDataWrapperInterface $requestWrapper)
    {
        $this->requestWrapper = $requestWrapper;
    }
    
    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }
    
    /**
     * @param string $method
     */
    public function setMethod(string $method)
    {
        $this->method = $method;
    }
}