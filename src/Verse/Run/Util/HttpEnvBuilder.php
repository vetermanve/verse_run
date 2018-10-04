<?php


namespace Verse\Run\Util;


class HttpEnvBuilder
{
    /**
     * @return \Verse\Run\Util\HttpEnvContext
     */
    public static function buildContext() : HttpEnvContext
    {
        $env = new HttpEnvContext();
        $env->fill([
            HttpEnvContext::HTTP_COOKIE    => &$_COOKIE,
            HttpEnvContext::HTTP_GET       => &$_GET,
            HttpEnvContext::HTTP_POST      => &$_POST,
            HttpEnvContext::HTTP_POST_BODY => trim(file_get_contents("php://input")),
            HttpEnvContext::HTTP_SERVER    => &$_SERVER,
            HttpEnvContext::HTTP_HEADERS   => getallheaders(),
        ]);
        
        return $env;
    }
}