<?php


namespace Verse\Run\Module;

use Verse\Run\RunModuleProto;

class ServicesModule extends RunModuleProto
{
    /**
     * @var RpcServices
     */
    private $servicesConfig;
    
    private $services;
    
    public function init () 
    {
        $this->servicesConfig = new RpcServices();
        $this->servicesConfig->follow($this);
    }
    
    /**
     * @param string $serviceName
     *
     * @return Service
     * 
     * @throws InternalError
     */
    public function getService($serviceName)
    {
        if (isset($this->services[$serviceName])) {
            return $this->services[$serviceName];
        }
        
        $serviceConfig = $this->servicesConfig->getServiceConfig($serviceName);
        
        if (!$serviceConfig) {
            throw new \LogicException("There is no " . $serviceName . " service.");
        }
        
        return $this->services[$serviceName] = Service::factory($serviceConfig);
    }
    
    /**
     * @param string $serviceName
     * @param object $object
     *
     * @return void
     */
    public function addService($serviceName, $object)
    {
        $this->services[$serviceName] = $object;
    }
}