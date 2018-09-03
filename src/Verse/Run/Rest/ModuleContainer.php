<?php


namespace Verse\Run\Rest;

use Mu\Interfaces\ContainerInterface;

class ModuleContainer implements ContainerInterface
{
    private $modules = [];
    
    public function bootstrap($module, $required = true)
    {
        if (!isset($this->modules[$module])) {
            if ($required) {
                throw new \Exception('Module '.$module.' not supported');    
            } else {
                return null;
            }
        }
        
        if (is_callable($this->modules[$module])) {
            $this->modules[$module] = $this->modules[$module]();
        }
        
        return $this->modules[$module];
    }
    
    public function setModule($moduleName, $module) 
    {
        return $this->modules[$moduleName] = $module;
    }
}