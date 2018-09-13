<?php


namespace Verse\Run\Spec;


class HttpResponseSpec
{
    const STATUS_ERROR = 1;
    const STATUS_OK    = 0;
    
    const HTTP_CODE_OK = 200;
    
    const HTTP_CODE_REDIRECT = 302;
    
    const HTTP_CODE_BAD_REQUEST = 400;
    const HTTP_CODE_UNAUTHORIZED= 401;
    const HTTP_CODE_NOT_FOUND   = 404;
    const HTTP_CODE_UNSUPPORTED = 406;
    const HTTP_CODE_CONFLICT    = 409;
    
    const HTTP_CODE_ERROR = 500;
    
    const META_HTTP_HEADERS = 'headers';
    const META_HTTP_CODE    = 'code';
    const META_EXECUTION_TIME = 'ex_time';
    
    const META_HTTP_HEADER_REQUEST_ID  = 'X-Request-Id';
    const META_HTTP_HEADER_STATUS_CODE = 'X-Status-Code';
    const META_HTTP_HEADER_ALLOW_ORIGIN = 'Access-Control-Allow-Origin';
    const META_HTTP_HEADER_LOCATION = 'Location';
    const META_HTTP_HEADER_EXECUTION_TIME  = 'X-Time';
    const META_HTTP_HEADER_CONTENT_TYPE  = 'Content-Type';

    const META_HTT_HEADER_EXPIRES = 'Expires';
    const META_HTT_HEADER_PRAGMA  = 'Pragma';
    const META_HTT_HEADER_CACHE_CONTROL = 'Cache-Control';
    const META_HTT_HEADER_ACCESS_CONTROL_ALLOW_CREDENTIALS = 'Access-Control-Allow-Credentials';
    const META_HTT_HEADER_ACCESS_CONTROL_EXPOSE_HEADERS = 'Access-Control-Expose-Headers';
    const META_HTT_HEADER_ACCESS_CONTROL_ALLOW_METHODS = 'Access-Control-Allow-Methods';
    const META_HTT_HEADER_ACCESS_CONTROL_ALLOW_HEADERS = 'Access-Control-Allow-Headers';
    
    const MESSAGE    = 'msg';
    const STATUS     = 'status';
    const REQUEST_ID = 'request_id';
    const TIME       = 'time';
    const DATA       = 'data';
    const USER_ID    = 'user_id';
    
    const CONTENT_JSON = 'application/json; charset=UTF-8';
    const CONTENT_HTML = 'text/html; charset=UTF-8';
    const CONTENT_TEXT = 'text/plain; charset=UTF-8';
    
    public static $absoluteHeaders = [
        self::META_HTT_HEADER_ACCESS_CONTROL_ALLOW_HEADERS     => 'Origin, Accept, X-Suppress-HTTP-Code, Content-Type, X-Rest-App, Authorization',
        self::META_HTT_HEADER_ACCESS_CONTROL_ALLOW_METHODS     => 'POST, GET, OPTIONS, DELETE, HEAD, PUT, COUNT',
        self::META_HTT_HEADER_ACCESS_CONTROL_EXPOSE_HEADERS    => 'X-Status-Code, Content-Type, X-Application',
        self::META_HTT_HEADER_ACCESS_CONTROL_ALLOW_CREDENTIALS => 'true',
        self::META_HTTP_HEADER_CONTENT_TYPE                    => self::CONTENT_JSON,
        self::META_HTT_HEADER_CACHE_CONTROL                    => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
        self::META_HTT_HEADER_PRAGMA                           => 'no-cache',
        self::META_HTT_HEADER_EXPIRES                          => '0',
    ];
}