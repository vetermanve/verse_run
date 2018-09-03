<?php


namespace Verse\Run\Provider;


use Verse\Run\RunContext;
use Verse\Run\RunRequest;
use Verse\Run\Spec\HttpRequestMetaSpec;
use Verse\Run\Util\HttpEnvContext;
use Verse\Run\Util\RestMethodHelper;
use Uuid\Uuid;

class PhpFpmRequest extends RunProviderProto
{
    
    /**
     * @var HttpEnvContext
     */
    private $httpEnv;
    
    public function prepare()
    {
        
    }
    
    public function run()
    {
        $uid = Uuid::v4();
        
        $this->runtime->freeze('request_id', $uid);
        $this->runtime->freeze('engine', 'run.fpm');
        
        $this->runtime->debug('REST_RAW_REQUEST', $this->httpEnv->getData());
        
        $uri = $this->httpEnv->getScope(HttpEnvContext::HTTP_SERVER, 'REQUEST_URI', '/');
        $uri = str_replace('//', '/', urldecode($uri));
        
        $resource = strpos($uri, '?') ? strstr($uri, '?', true) : $uri;
    
        $request = new RunRequest($uid, $resource, '');
    
        $request->params = $this->httpEnv->get(HttpEnvContext::HTTP_GET, []);
    
        RestMethodHelper::makeStrictParams($request->params);
    
        $headers = [];
    
        foreach ($this->httpEnv->get(HttpEnvContext::HTTP_HEADERS, []) as $header => $value) {
            $headers[strtolower($header)] = $value;
        }
    
        $method = $this->httpEnv->getScope(HttpEnvContext::HTTP_SERVER, 'REQUEST_METHOD', 'GET');

        $locale = null;
        if (function_exists('locale_accept_from_http') && isset($headers['accept-language'])) {
            $locale = locale_accept_from_http($headers['accept-language']);
        }
    
        $request->meta = [
            HttpRequestMetaSpec::REQUEST_METHOD  => $method,
            HttpRequestMetaSpec::REQUEST_HEADERS => $headers,
            HttpRequestMetaSpec::CLIENT_IP       => $this->httpEnv->getScope(HttpEnvContext::HTTP_SERVER, 'REMOTE_ADDR'),
            HttpRequestMetaSpec::CLIENT_AGENT    => isset($headers['user-agent']) ? $headers['user-agent'] : null,
            HttpRequestMetaSpec::CLIENT_LOCALE   => isset($locale) ? $locale : null,
        ];
    
        if ($request->getParamOrData('dddebug') === 4) {
            $this->context->setEnv(RunContext::ENV_DEBUG, 1);
            $this->context->setEnv(RunContext::REQUEST_PROFILING_ENABLED, 1);
        }
    
        $request->body = &$this->httpEnv->getLink(HttpEnvContext::HTTP_POST_BODY, '');
    
        if ($request->body) {
            if (strpos($request->body, '{') === 0) {
                $bodyData = json_decode($request->body, true);
                if (is_array($bodyData)) {
                    $request->data = $bodyData;
                }
            } elseif (strpos($request->body, '=')) {
                parse_str($request->body, $bodyData);
                if (is_array($bodyData)) {
                    $request->data = $bodyData;
                }
            }
        }
    
        $chState = $request->getChannelState();
        $chState->setPacked($this->httpEnv->get(HttpEnvContext::HTTP_COOKIE, []));
        
        $this->core->process($request);
    }
    
    /**
     * @param HttpEnvContext $httpEnv
     */
    public function setHttpEnv($httpEnv)
    {
        $this->httpEnv = $httpEnv;
    }
    
}