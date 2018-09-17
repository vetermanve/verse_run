<?php


namespace Verse\Run\Interfaces;


interface HttpRequestDataWrapperInterface
{
    /**
     * @return string|null
     */
    public function getUserAgent();
    
    public function getUserAgentType();
    
    /**
     * @return string|null
     */
    public function getOrigin();
    
    /**
     * Получение IP адреса с которого пришел запрос
     * @return string|null
     */
    public function getClientIp();
    
    
    public function getUrl();
    
    /**
     * @return array
     */
    public function getParams();
    
    /**
     * @return int
     */
    public function getId();
    
    /**
     * @return int[]
     */
    public function getIds();
    
    /**
     * @return string
     */
    public function getMethod();
    
    /**
     * @return string
     */
    public function getResource();
    
    /**
     * @return string
     */
    public function getBody();
    
    
    public function getHeader($name);
    
    /**
     * @return string
     */
    public function getLocale() : string ;
    
    /**
     * Получение всех параметров запроса
     * Фильтрация растпространяется на параметры, получаемые этим методом
     * @return array
     */
    public function getRequestParams() : array;
    
    public function getParamsByKeys ($paramsKeys);
    
    /**
     * @return string
     */
    public function getRequestUuid() : string;
    
    public function getParam ($key, $default = null);
    
    /**
     * Получить состояние
     * 
     * @param string $name
     * @param null $default
     *
     * @return mixed
     */
    public function getState (string $name, $default = null);
    
    /**
     * Записать состояние
     * 
     * @param string $name
     * @param        $value
     * @param null   $ttl
     *
     * @return mixed
     */
    public function setState (string $name, $value, $ttl = null);
}