<?php


namespace Verse\Run\Util;


use Verse\Modular\ModularContextProto;

class HttpEnvContext extends ModularContextProto
{
    /* HTTP CONSUMING */
    const HTTP_GET     = 'http_get';
    const HTTP_POST    = 'http_post';
    const HTTP_POST_BODY = 'http_post_body';
    const HTTP_COOKIE  = 'http_cookie';
    const HTTP_SERVER  = 'http_server';
    const HTTP_HEADERS = 'http_headers';
    
    /* Cookies hack */
    const COOKIE_ENCODE_DELIMITER = ':';
    const COOKIE_ENCODED_JSON     = 'je';
}