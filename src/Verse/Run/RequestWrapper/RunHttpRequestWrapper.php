<?php


namespace Verse\Run\RequestWrapper;


use Verse\Run\Interfaces\HttpRequestDataWrapperInterface;
use Verse\Run\RunRequest;
use Verse\Run\Spec\HttpRequestMetaSpec;
use Verse\Run\Util\ChannelState;

class RunHttpRequestWrapper implements HttpRequestDataWrapperInterface
{
    /**
     * @var RunRequest;
     */
    private $request;
    
    /**
     * @var ChannelState
     */
    private $state;
    
    private $allParams;
    
    /**
     * @return string|null
     */
    public function getUserAgent()
    {
        return $this->request->getMeta(HttpRequestMetaSpec::CLIENT_AGENT, 'unknown');
    }
    
    public function getUserAgentType()
    {
        return $this->request->getMeta(HttpRequestMetaSpec::CLIENT_TYPE, 'unknown');
    }
    
    /**
     * @return string|null
     */
    public function getOrigin()
    {
        return $this->request->getMeta(HttpRequestMetaSpec::REQUEST_SOURCE);
    }
    
    /**
     * Получение IP адреса с которого пришел запрос
     * 
     * @return string|null
     */
    public function getClientIp()
    {
        return $this->getHeader('x-forwarded-for') ?: '000.000.000.000';
    }
    
    public function getUrl()
    {
        return $this->request->getResource();
    }
    
    /**
     * @return array
     */
    public function getParams()
    {
        return $this->allParams;
    }
    
    /**
     *
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    public function getParam($key, $default = null)
    {
        if (isset($this->allParams[$key])) {
            return $this->allParams[$key];
        }
        
        return $default;
    }
    
    /**
     * @return int
     */
    public function getId()
    {
        return $this->getParam('id');
    }
    
    /**
     * @return int[]
     */
    public function getIds()
    {
        return $this->getParam('ids');
    }
    
    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->request->getMeta(HttpRequestMetaSpec::REQUEST_METHOD);
    }
    
    /**
     * @return string
     */
    public function getResource()
    {
        return $this->request->getResource();
    }
    
    /**
     * @return string
     */
    public function getBody()
    {
        return $this->request->body;
    }
    
    /**
     * @return string
     */
    public function getLocale() : string 
    {
        return $this->request->getMeta(HttpRequestMetaSpec::CLIENT_LOCALE);
    }
    
    /**
     * Получение всех параметров запроса
     * Фильтрация растпространяется на параметры, получаемые этим методом
     * @return array
     */
    public function getRequestParams() : array 
    {
        return $this->allParams;
    }
    
    /**
     * @return string
     */
    public function getRequestUuid() :string 
    {
        return $this->request->getUid();
    }
    
    /**
     * @param RunRequest $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
        $this->allParams = (array)$this->request->params + (array)$this->request->data;
        $this->state = $request->getChannelState();
    }
    
    public function getHeader($name)
    {
        return $this->request->getMetaItem(HttpRequestMetaSpec::REQUEST_HEADERS, strtolower($name));
    }
    
    public function getParamsByKeys($paramsKeys)
    {
        $result = [];
        
        foreach ($paramsKeys as $key) {
            if (isset($this->allParams[$key])) {
                $result[$key] = $this->allParams[$key];
            }
        }
        
        return $result;
    }
    
    /**
     * Получить состояние клиента
     *
     * @param string $name
     * @param null   $default
     *
     * @return mixed
     */
    public function getState(string $name, $default = null)
    {
        return $this->state->get($name, $default);
    }
    
    /**
     * Записать состояние клиента
     *
     * @param string $name
     * @param        $value
     * @param null   $ttl
     *
     * @return mixed
     */
    public function setState(string $name, $value, $ttl = null)
    {
        $this->state->set($name, $value, $ttl);
    }
}