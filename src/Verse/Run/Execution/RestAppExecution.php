<?php


namespace Verse\Run\Execution;


use Mu\Interfaces\DispatcherInterface;
use Verse\Run\Rest\RestRequestOptions;
use Verse\Run\RunModuleProto;
use Verse\Run\RunRequest;
use Verse\Run\Spec\HttpRequestMetaSpec;

class RestAppExecution extends RunModuleProto
{
    const MODULE_SINGLE = 'single';
    const MODULE_APP    = 'app';

    /**
     * Вызов из которого все рождается
     * 
     * @var RunRequest
     */
    private $runRequest;

    /**
     * Объект запускаемого контроллера
     * 
     * @var \Mu\Controller
     */
    private $controller;
    
    /**
     * Метод для выполнения
     * 
     * @var string
     */
    private $action;
    
    /**
     * Результат выполнения
     * 
     * @var []|string|bool|null
     */
    private $data;
    
    /**
     * Результирующее состояние 
     *  
     * @var int
     */
    private $status = 0;
    
    /**
     * @var DispatcherInterface
     */
    private $dispatcher;
    
    private $controllerClasses = [];
    
    private $actionName = '';
    
    private $startTime;
    
    public function prepareController()
    {
        /** Находим контроллер */
        $controllerName = null;
        
        foreach ($this->controllerClasses as $class) {
            if (!class_exists($class)) {
                continue;
            }
            
            $controllerName = $class;
            
            break;
        }
        
        if (!is_null($controllerName)) {
            $this->controller = new $controllerName($this->dispatcher);
        }
    }
    
    /**
     * Процессим вызов 
     */
    public function prepareAction()
    {
        if ($this->controller && method_exists($this->controller, $this->actionName)) {
            $this->action = $this->actionName;
        }
    }
    
    public function run()
    {
        $action   = $this->action;
        $response = $this->controller->$action();
        
        /** Проверим зафетчен ли ответ */
        if ($response instanceof \Mu\Rpc\Request) {
            /** @var $response \Mu\Rpc\Request */
            $this->data   = $response->getData();
            $this->status = $response->getStatus();
        } else {
            $this->data = $response;
        }
    }
    
    public function extractRequestClassAndAction()
    {
        /* controller */
        $resource = $this->runRequest->getResource();
        $version  = 'V' . $this->runRequest->getMeta(HttpRequestMetaSpec::REQUEST_VERSION);
        
        /** Собираем возможные местонахождения класса контроллера */
        $parts = explode('-', $resource);
        array_walk($parts, function (&$val) {
            $val = ucfirst($val);
        });
        
        $moduleName    = array_shift($parts);
        $componentName = implode('', $parts);
        
        if ($componentName) {
            $this->controllerClasses[self::MODULE_APP] = '\\App\\' . $moduleName . '\\Controller\\' . $componentName;
        } else {
            $this->controllerClasses[self::MODULE_APP] = '\\App\\' . $moduleName . '\\Controller\\' . $moduleName;
        }
    
        $this->controllerClasses[self::MODULE_SINGLE] = "\\iConto\\Controller\\" . $version . '\\' . $moduleName . $componentName;
    
        /* action */
        
        $requestMethod = $this->runRequest->getMeta(HttpRequestMetaSpec::REQUEST_METHOD);
    
        $this->actionName = $requestMethod;
    }
    
    /**
     * @return mixed
     */
    public function getRunRequest()
    {
        return $this->runRequest;
    }
    
    /**
     * @param mixed $runRequest
     */
    public function setRunRequest($runRequest)
    {
        $this->runRequest = $runRequest;
        
        $this->dispatcher = new RestRequestOptions();
        $this->dispatcher->setRequest($this->runRequest);
    }
    
    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }
    
    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }
    
    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * @param \Mu\Controller $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }
    
    /**
     * @param array $controllerClasses
     */
    public function setControllerClasses($controllerClasses)
    {
        $this->controllerClasses = $controllerClasses;
    }
    
    /**
     * @param string $actionName
     */
    public function setActionName($actionName)
    {
        $this->actionName = $actionName;
    }
    
    /**
     * @return DispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }
    
    public function start()
    {
        $this->startTime = microtime(1);
    }
    
    public function getExecutionTime () 
    {
        return microtime(1) - $this->startTime;
    }
}