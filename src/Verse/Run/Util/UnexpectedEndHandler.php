<?php


namespace Verse\Run\Util;


class UnexpectedEndHandler
{
    static private $isRegistered = false;
    
    /**
     * @var callable[]
     */
    static private $callbacks;
    
    static private $skipErrors = [
        E_STRICT       => 1,
        E_DEPRECATED   => 1,
        E_NOTICE       => 1,
        E_USER_NOTICE  => 1,
        E_WARNING      => 1,
        E_USER_WARNING => 1,
        E_USER_DEPRECATED => 1,
    ];
    
    public static function addCallback ($callable) 
    {
        if (!is_callable($callable)) {
            trigger_error('Callback not callable', E_USER_WARNING);
            return false;
        }
        
        if(!self::$isRegistered) {
            register_shutdown_function(__CLASS__.'::runCallbacks');
            self::$isRegistered = true;
        }
        
        self::$callbacks[] = $callable;
        
        return true;
    }
    
    
    public static function runCallbacks () 
    {
        $error = error_get_last();
    
        if (!$error || isset(self::$skipErrors[$error['type']])) {
            return ;
        }
        
        foreach (self::$callbacks as $callback) {
            try {
                call_user_func($callback, $error);
            } catch (\Exception $e) {
                trigger_error('Exception on rescue: '.$e->getMessage(), E_USER_WARNING);
            }
        }
    }
}