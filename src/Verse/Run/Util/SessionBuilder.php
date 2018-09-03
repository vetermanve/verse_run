<?php


namespace Run\Util;


use Auth\Session\LocalSession;
use Auth\Session\RedisSession;
use Mu\Env;
use Mu\Exception\Auth\NoPrivileges;
use Mu\Exception\Auth\OAuth2InvalidGrant;
use Mu\Exception\Auth\OAuth2NoPrivileges;
use Mu\Interfaces\SessionInterface;
use Mu\OAuth\OAuth2Storage\iContoServices;
use Mu\Service\Auth\AuthService;
use OAuth2\OAuth2;
use OAuth2\OAuth2ServerException;
use Run\RunModuleProto;
use Run\RunRequest;

class SessionBuilder extends RunModuleProto
{
    const OLD_HTTP_SESSION_KEY = 'PHPSESSID';
    
    const PARAM_ACCESS_TOKEN = 'access_token';
    
    /**
     * @var SessionInterface
     */
    private $replaceSession;
    
    /**
     * @param RunRequest $request
     *
     * @return SessionInterface
     */
    public function getSession (RunRequest $request) 
    {
        if ($this->replaceSession) {
            $session = $this->replaceSession;
            unset($this->replaceSession);
            return $session;
        }
        
        $accessToken = $request->getParamOrData(self::PARAM_ACCESS_TOKEN);
        
        if ($accessToken) {
            return $this->_buildOauthSession($accessToken, $request->getResource(), $request->getChannelState());
        }
        
        $session = new RedisSession();
        $session->setChannelState($request->getChannelState());
        
//     
        return $session;
    }
    
    /**
     * @param $token
     * @param $resource
     *
     * @return LocalSession
     * @throws NoPrivileges
     * @throws OAuth2InvalidGrant
     * @throws OAuth2NoPrivileges
     * @throws \Mu\Exception\InternalError
     * @internal param RunRequest $request
     *
     */
    private function _buildOauthSession($token, $resource, ChannelState $channelState) {
        /* @var $authService AuthService */
        $authService = Env::getServiceContainer()->getService('Auth');
        
        $provider = new OAuth2(new iContoServices(), [OAuth2::CONFIG_SUPPORTED_SCOPES => \Mu\OAuth\OAuth2::getAllowedScopes()]);
        
        try {
            $provider->verifyAccessToken($token, $resource);
        
            $accessToken = $authService->getOAuth2AccessTokenInfo($token);
        
            // Занесение инфы о пользователе в сессию для контроллера и плагинов
            if (!empty($accessToken) && !empty($accessToken['user_id'])) {
                $session = new RedisSession();
                $session->setChannelState($channelState);
                $session->setUserId($accessToken['user_id']);
    
                $authInfo = [
                    'type' => 'token',
                    'data' => [
                        'name'    => $accessToken['name'],
                        'app_key' => $accessToken['app_key'],
                        'id'      => $accessToken['id'],
                    ]
                ];
                
                $session->set('auth_by', \json_encode($authInfo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            } else {
                $session = new LocalSession();
            }
    
        }  catch ( OAuth2ServerException $e) {
            // маппинг OAuth исключений на iConto-вские
            switch ($e->getMessage()) {
                case OAuth2::ERROR_INVALID_GRANT:
                    throw new OAuth2InvalidGrant($e->getMsg());
                    break;
                case OAuth2::ERROR_INSUFFICIENT_SCOPE:
                    throw new OAuth2NoPrivileges();
                    break;
                default:
                    throw new NoPrivileges($e->getMsg());
            }
        }
    
        $appKey = $accessToken['app_key'];
        $app    = $authService->getAccessApplicationByAppKey($appKey);
        
        if ( !empty($app) ) {
            $session->setAppInfo($app->asArray());
        }
        
        return $session;
    }
    
    /**
     * @param SessionInterface $replaceSession
     */
    public function setReplaceSession($replaceSession)
    {
        $this->replaceSession = $replaceSession;
    }
}