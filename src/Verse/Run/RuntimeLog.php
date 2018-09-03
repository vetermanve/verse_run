<?php


namespace Run;


use Monolog\Logger;
use Run\Util\Tracer;

class RuntimeLog extends Logger
{
    const LOGGER_NAME_CONTEXT_KEY = 'loggerName';
    
    const LOG_LEVEL_RUNTIME = 100;
    
    protected $context = [];
    
    /**
     * @var Tracer
     */
    private $tracer;
    
    public function __construct($name = 'RunCore', $handlers = array(), $processors = array())
    {
        parent::__construct($name, $handlers, $processors);
    }
    
    public function runtime ($message, array $context = array()) 
    {
        $this->addRecord(self::LOG_LEVEL_RUNTIME, $message, $context);
    }
    
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return bool
     */
    public function addRecord(int $level, string $message, array $context = array()) : bool
    {
        $context += $this->context;
        
        $msg = '';
        if ($context) {
            $msg = json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        
        $levelName = is_numeric($level) ? self::getLevelName($level) : $level;

        $msg = date('[Y-m-d H:i:s] '). $this->getName() .'.'. $levelName.': '. $message ." ".trim($msg);
        
        static $stdout;
        !$stdout && $stdout = fopen('php://stdout', 'w');
        
        fwrite($stdout, $msg."\n");
    
        if ($this->handlers) {
            parent::addRecord($level, $message, $context);
        }
        
        return true;
    }
    
    public function catchErrors () 
    {
        $this->tracer = new Tracer();
        $this->tracer->catchErrors(E_ALL ^ E_STRICT ^ E_DEPRECATED, [$this, 'addWarning']);
    }
    
    /**
     * Заморозка параметра в контексте
     *
     * @param string $param
     * @param mixed $value
     */
    public function freeze($param, $value)
    {
        $this->context[$param] = $value;
    }
    
    /**
     * Разморозка параметра в контексте
     *
     * @param string $param
     *
     * @return void
     */
    public function unfreeze($param)
    {
        unset($this->context[$param]);
    }
    
    
    public function getFromContext ($key, $default = null)
    {
        return isset($this->context[$key]) ? $this->context[$key] : $default;
    }
    
    /**
     * Returns logger name
     *
     * @return string
     */
    public function getLoggerName()
    {
        return $this->getFromContext(self::LOGGER_NAME_CONTEXT_KEY, ''); 
    }
    
    /**
     * Sets logger name
     *
     * @param string $loggerName
     */
    public function setLoggerName($loggerName)
    {
        return $this->freeze(self::LOG_LEVEL_RUNTIME, $loggerName);
    }
}