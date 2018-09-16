<?php

namespace Verse\Run\RequestRouter;

use Verse\Run\Interfaces\RequestRouterInterface;
use Verse\Run\RunRequest;

class BasicMvcRequestRouter implements RequestRouterInterface
{
    private $_namespacePrefix = '\\App';

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
        
        return $this->_namespacePrefix.'\\'.$moduleName.'\\Controller\\'.$controllerName;
    }

    /**
     * @return string
     */
    public function getNamespacePrefix() : string
    {
        return $this->_namespacePrefix;
    }

    /**
     * @param string $namespacePrefix
     */
    public function setNamespacePrefix(string $namespacePrefix)
    {
        $this->_namespacePrefix = $namespacePrefix;
    }
}