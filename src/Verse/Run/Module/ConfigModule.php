<?php


namespace Run\Module;


use Mu\Interfaces\ConfigInterface;
use Run\RunContext;
use Run\RunModuleProto;

class ConfigModule extends RunModuleProto implements ConfigInterface
{
    private $data;
    
    /**
     * ConfigModule constructor.
     *
     * @param $data
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }
    
    /**
     * Возвращает одно занчение
     *
     * @param string $key
     * @param string $section
     * @param mixed  $defaultValue
     *
     * @return mixed
     */
    public function get($key, $section = null, $defaultValue = null)
    {
        if ($section !== null) {
            return isset($this->data[$section][$key]) ? $this->data[$section][$key] : $defaultValue;
        }
    
        return isset($this->data[$key]) ? $this->data[$key] : $defaultValue;
    }
    
    /**
     * Возвращает полный массив настроек
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }
    
    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
    
    public function loadDataFromContext ($key) 
    {
        $this->data = &$this->context->getLink($key, []);
    }
    
    /**
     * Получить целиком секцию
     *
     * @param string $section
     *
     * @return array
     */
    public function getSection($section)
    {
        return isset($this->data[$section]) ? $this->data[$section] : [];
    }
}