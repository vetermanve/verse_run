<?php


namespace Run\Util;


class TerminateHandler
{
    private static $instance;
    
    const NAME     = 'name';
    const CALLBACK = 'call';
    const PREVENT_EXIT = 'prevent_exit';
    
    private static $signals = [
        SIGTERM => 'SIGTERM',
        SIGINT  => 'SIGINT',
        SIGHUP  => 'SIGHUP',
    ];
    
    private $registeredCallbacks = [];
    
    private $init = false;
    
    private function __construct()
    {
    }
    
    /**
     * @return TerminateHandler
     */
    public static function i()
    {
        if (!self::$instance) {
            self::$instance = new self();
            self::$instance->_init();
        }
        
        return self::$instance;
    }
    
    private function _init()
    {
        if (function_exists('pcntl_signal')) {
//            foreach (self::$signals as $signal => $title) {
//                pcntl_signal($signal, [$this, 'catchTerminateSignal']);
//            }
            
            $this->init = true;
        }
    }
    
    public function registerShutdown($name, $callback, $preventExit = false)
    {
        $this->registeredCallbacks[$name] = [
            self::NAME => $name,
            self::CALLBACK => $callback,
            self::PREVENT_EXIT => $preventExit
        ];
    }
    
    public function catchTerminateSignal($sigNumber)
    {
        $exitOnEnd = true;
        
        foreach ($this->registeredCallbacks as $name => $callbackInfo) {
            try {
                echo "Executing callback: " . $name."\n";
                $callbackInfo[self::CALLBACK]();
                $callbackInfo[self::PREVENT_EXIT] && $exitOnEnd = false;
            } catch (\Exception $e) {
                $msg = 'TerminateHandler on signal '
                    . self::$signals[$sigNumber]
                    . ' hds exception '
                    . $e->getMessage()
                    . ' on "'
                    . $name
                    . '" callback';
                
                error_log($msg, E_USER_WARNING);
            }
        }
        
        if (!$exitOnEnd) {
            exit(0);
        }
    }
    
    /**
     * @return boolean
     */
    public function isInit()
    {
        return $this->init;
    }
}