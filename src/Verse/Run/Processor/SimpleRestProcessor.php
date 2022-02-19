<?php


namespace Verse\Run\Processor;


use Verse\Run\ChannelMessage\ChannelMsg;
use Verse\Run\ChannelMessage\HttpReply;
use Verse\Run\Controller\BaseControllerProto;
use Verse\Run\RequestRouter\BasicMvcRequestRouter;
use Verse\Run\RequestWrapper\RunHttpRequestWrapper;
use Verse\Run\RunRequest;
use Verse\Run\Spec\HttpRequestMetaSpec;
use Verse\Run\Spec\HttpResponseSpec;

class SimpleRestProcessor extends RunRequestProcessorProto
{
    protected $defaultContentType = HttpResponseSpec::CONTENT_JSON;

    public function prepare()
    {
        if (!$this->requestRouter) {
            $this->requestRouter = new BasicMvcRequestRouter();    
        }
    }
    
    protected function _getRequestMethod (RunRequest $request)
    {
        return strtolower($request->getMeta(HttpRequestMetaSpec::REQUEST_METHOD, 'get'));
    }
    
    public function _buildResponseObject (RunRequest $request) : HttpReply 
    {
        $response = new HttpReply();
        $response->setUid($request->getUid());
        $response->setDestination($request->getReply());
        $response->setChannelState($request->getChannelState());
        $response->setHeaders(HttpResponseSpec::$absoluteHeaders);
        $response->setHeader(HttpResponseSpec::META_HTTP_HEADER_CONTENT_TYPE, HttpResponseSpec::CONTENT_TEXT);

        return $response;
    }
    
    public function process(RunRequest $request)
    {
        $request->meta[HttpRequestMetaSpec::EXECUTION_START] = microtime(1);
        
        $response = $this->_buildResponseObject($request);
        $response->setHeader(HttpResponseSpec::META_HTTP_HEADER_CONTENT_TYPE, $this->defaultContentType);
        
        try {
            $controllerClass = $this->requestRouter->getClassByRequest($request);
            
            if (!class_exists($controllerClass)) {
                return $this->abnormalResponse(
                    HttpResponseSpec::HTTP_CODE_NOT_FOUND,
                    'Incorrect resource',
                    $response,
                    $request
                );
            }

            $controller = new $controllerClass; 

            if (!$controller instanceof BaseControllerProto) {
                return $this->abnormalResponse(
                    HttpResponseSpec::HTTP_CODE_NOT_FOUND,
                    'Incorrect resource',
                    $response,
                    $request
                );
            } 
    
            $options = new RunHttpRequestWrapper();
            $options->setRequest($request);
            $controller->setRequestWrapper($options);
    
            $method = $this->_getRequestMethod($request);
            $controller->setMethod($method);
            
            if (!$controller->validateMethod()) {
                return $this->abnormalResponse(
                    HttpResponseSpec::HTTP_CODE_NOT_FOUND,
                    'Incorrect resource',
                    $response,
                    $request
                );
            }
            
            $response->setCode(HttpResponseSpec::HTTP_CODE_OK);
            $response->setBody($controller->run());
            
        } catch (\Throwable $throwable) {
            // possible is an http code
            $code = $throwable->getCode();
            if ($code >= 300 && $code < 600) {
                $response->setCode($code);
                $response->body = [
                    'message' => $throwable->getMessage(),
                    'code' => $code,
                ];
            } else {
                $response->setCode(HttpResponseSpec::HTTP_CODE_ERROR);
                $response->body = 'Internal error : '. $throwable->getMessage().' on '.$throwable->getTraceAsString();
            }
        }
        
        $this->sendResponse($response, $request);
    }
    
    protected function abnormalResponse(int $code, string $text, ChannelMsg $response, RunRequest $request) {
        $response->setCode($code);
        $response->body = $text;
        $this->sendResponse($response, $request);
    }
    
    public function sendResponse(ChannelMsg $response, RunRequest $request)
    {
        $response->setMeta(HttpResponseSpec::META_EXECUTION_TIME, microtime(true) - $request->getMeta(HttpRequestMetaSpec::EXECUTION_START));
        return parent::sendResponse($response, $request);
    }
}