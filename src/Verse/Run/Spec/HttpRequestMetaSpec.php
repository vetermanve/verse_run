<?php


namespace Run\Spec;


class HttpRequestMetaSpec
{
    /* channel space related */
    const CLIENT_TYPE        = 'client_type';
    const CLIENT_VERSION     = 'client_version';
    const CLIENT_AGENT       = 'user_agent';
    const CLIENT_LOCALE      = 'user_locale';
    const CLIENT_IP          = 'user_ip';
    
    const CHANNEL_SESSION_ID = 'PHPSESSID';
    
    /* client  */
    const PROVIDER_TYPE = 'provider_type';
    
    /* channel modification related */
    const SET_CHANNEL_STATE = 'set_to_state';
    
    /* request related */
    const REQUEST_SOURCE     = 'req_source';
    const REQUEST_METHOD     = 'req_method';
    const REQUEST_VERSION    = 'req_version';
    const REQUEST_HEADERS    = 'req_headers';
    
    const EXECUTION_START = 'ex_start';
}