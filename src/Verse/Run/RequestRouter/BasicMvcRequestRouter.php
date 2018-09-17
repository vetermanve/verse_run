<?php

namespace Verse\Run\RequestRouter;

use Verse\Run\Interfaces\RequestRouterInterface;
use Verse\Run\RunRequest;

class BasicMvcRequestRouter implements RequestRouterInterface
{
    private $_rootNamespace = 'App';

    public function getClassByRequest(RunRequest $request) : string 
    {
        $module = $request->getResourcePart(0);
        if ($module) {
            $moduleParts = explode('-', $module);
            $moduleName = ucfirst(array_shift($moduleParts));
            if ($moduleParts) {
                array_walk($moduleParts, function (&$val) {
                    $val = ucfirst($val);
                });
                $controllerName = implode('', $moduleParts);
            } else {
                $controllerName = $moduleName;
            }
        } else {
            $moduleName = $controllerName = 'Landing';
        }
        
        return '\\'.$this->_rootNamespace.'\\'.$moduleName.'\\Controller\\'.$controllerName;
    }

    /**
     * @return string
     */
    public function getRootNamespace() : string
    {
        return $this->_rootNamespace;
    }

    /**
     * @param string $namespacePrefix
     */
    public function setRootNamespace(string $namespacePrefix)
    {
        $this->_rootNamespace = $namespacePrefix;
    }
}