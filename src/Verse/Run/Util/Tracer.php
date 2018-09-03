<?php


namespace Run\Util;


class Tracer
{
    /**
     * @var string
     */
    private $cwd;
    
    /**
     * @var callable
     */
    private $errorRegisterCallback;
    
    /**
     * Tracer constructor.
     */
    public function __construct()
    {
        $this->cwd = getcwd();
    }
    
    public function getTrace($deep = 10, $skipLevels = 1)
    {
        $backtrace = debug_backtrace();
        
        $i = 0;
        $res = [];
        
        $deep += $skipLevels; 
        
        foreach ($backtrace as &$record) {
            $i++;
            
            if($i <= $skipLevels) {
                continue;
            }
            
            if ($i > $deep ) {
                break;
            }
    
            $argsDesc = '';
            foreach ($record['args'] as $k => &$arg) {
                if (is_object($arg)) {
                    $argsDesc .=', '.get_class($arg);     
                } elseif (is_string($arg)) {
                    $argsDesc .=', '.$arg;
                } elseif (is_array($arg)) {
                    $argsDesc .=', Array('.count($arg).')';
                } elseif (is_callable($arg)) {
                    $argsDesc .= ', Closure';
                } else {
                    $argsDesc .= '?';
                }
            }
            
            $argsDesc  = trim($argsDesc, ', ');
            
            $record['file'] = isset($record['file']) ? str_replace($this->cwd, '', $record['file']): 'PHP_CORE';
            $record['line'] = isset($record['line']) ? ':'.$record['line'] : '';
            $res[] = $record['file'] .  $record['line'] . 
                (!empty($record['class']) 
                    ? ('->' . $record['class'] . '::' . $record['function'].'()') 
                    : ('->' . $record['function'].'('.$argsDesc.')')
                );
        }
        
        return $res;
    }
    
    
    public function getErrorCodeName($code) {
        switch($code)
        {
            case E_ERROR: // 1 // 
                return 'E_ERROR';
            case E_WARNING: // 2 // 
                return 'E_WARNING';
            case E_PARSE: // 4 // 
                return 'E_PARSE';
            case E_NOTICE: // 8 // 
                return 'E_NOTICE';
            case E_CORE_ERROR: // 16 // 
                return 'E_CORE_ERROR';
            case E_CORE_WARNING: // 32 // 
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR: // 64 // 
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING: // 128 // 
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR: // 256 // 
                return 'E_USER_ERROR';
            case E_USER_WARNING: // 512 // 
                return 'E_USER_WARNING';
            case E_USER_NOTICE: // 1024 // 
                return 'E_USER_NOTICE';
            case E_STRICT: // 2048 // 
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR: // 4096 // 
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED: // 8192 // 
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED: // 16384 // 
                return 'E_USER_DEPRECATED';
            default :
                return 'UNKNOWN_ERROR #'.$code;
        }
    }
    
    /**
     * @return mixed
     */
    public function getCwd()
    {
        return $this->cwd;
    }
    
    public function catchErrors($errorTypes, $errorCallback)
    {
        if (!is_callable($errorCallback)) {
            return false;
        }
        
        set_error_handler(array($this,'handleError'), $errorTypes);
        $this->errorRegisterCallback = $errorCallback;
        
        return true;
    }
    
    public function handleError ($code, $message, $file, $line)
    {
        if (error_reporting() === 0) { // ignored by @
            return ;
        }
        
        $context = [
            'type' => $this->getErrorCodeName($code),
            'file'       => str_replace($this->cwd, '', $file).':'.$line,
            'trace'      => $this->getTrace(15, 2),
            'cwd'        => $this->cwd,
        ];
        
        call_user_func($this->errorRegisterCallback, $message, $context);
    }
}