<?php


namespace Run\Util;


use Mu\Dispatcher;
use Mu\Exception\Validator\NoValidRequest;
use Mu\Registry;
use Mu\Validate\Request;
use Json\ValidationException;
use Json\Validator;
use Run\RunRequest;
use Run\Spec\HttpRequestMetaSpec;

class SchemaChecker
{
    private $resourceConfig = [];
    
    private $resourceMethodSchema = [];
    
    
    /**
     * @var Validator
     */
    private $validator;
    
    /**
     * SchemaChecker constructor.
     *
     */
    public function __construct()
    {
        $this->validator = new Validator(null, null, true);
    }
    
    public function getResourceMethodSchema($resource, $requestMethod)
    {
        $resource         = strtolower($resource);
        $requestMethod    = strtolower($requestMethod);
        $resourceMethodId = $resource . ':' . $requestMethod;
        
        if (isset($this->resourceMethodSchema[$resourceMethodId])) {
            
            Registry::put('config_resource', $this->resourceConfig[$resource] ?: new \stdClass());
            Registry::put('config_resource_request', $this->resourceMethodSchema[$resourceMethodId] ?: new \stdClass());
            
            return $this->resourceMethodSchema[$resourceMethodId];
        }
        
        if (!isset($this->resourceConfig[$resource])) {
            $this->resourceConfig[$resource] = $this->getResourceConfig($resource);
        }
        
        $resourceConfig = $this->resourceConfig[$resource];
        Registry::put('config_resource', $resourceConfig);
        
        $requestSchema = false;
        if ($resourceConfig && isset($resourceConfig->schema->$requestMethod)) {
            $requestSchema = $resourceConfig->schema->$requestMethod;
        }
        
        $this->resourceMethodSchema[$resourceMethodId] = $requestSchema;
        Registry::put('config_resource_request', $requestSchema);
        
        return $this->resourceMethodSchema[$resourceMethodId];
    }
    
    private function getResourceConfig($resource)
    {
        $resource = strtolower($resource);
        
        if (isset($this->resourceConfig[$resource])) {
            return $this->resourceConfig[$resource];
        }
        
        $this->resourceConfig[$resource] = false;
        
        $resourceConfFileName = 'conf/resources/' . $resource . '.json';
        
        if (file_exists($resourceConfFileName) && is_readable($resourceConfFileName)) {
            if ($resourceConfig = json_decode(file_get_contents($resourceConfFileName))) {
                $this->resourceConfig[$resource] = $resourceConfig;
            }
        }
        
        return $this->resourceConfig[$resource];
    }
    
    public function check(RunRequest $request)
    {
        $resource = $request->getResource();
        $params   = is_array($request->data) ? $request->params + $request->data : $request->params;
        
        $method        = $request->getMeta(HttpRequestMetaSpec::REQUEST_METHOD);
        $requestSchema = $this->getResourceMethodSchema($resource, $method);
        
        if ($requestSchema) {
            $error = $fieldName = $errorCode = null;
            
            try {
                $params = $params ? json_decode(json_encode($params)) : new \stdClass() ; // @TODO убрать этот костыль для схемы
                $this->validator->validate($params, null, $requestSchema);
            } catch (ValidationException $e) {
                $error     = $e->getMessage();
                $fieldName = $e->getFieldName();
                $errorCode = $e->getCode();
            } catch (\Exception $e) {
                $error     = $e->getMessage();
                $errorCode = $e->getCode();
            }
            
            if ($error || $errorCode) {
                throw new NoValidRequest($error, $errorCode, null, $fieldName);    
            }
        }
        
        return true;
    }
    
}