<?php


namespace Verse\Run;


abstract class RunModuleProto
{
    /**
     * Все отчеты по происходящему идут сюда
     *
     * @var RuntimeLog
     */
    protected $runtime;
    
    /**
     * Ядро процессинга запросов
     *
     * @var RunCore
     */
    protected $core;
    
    /**
     * Конфигурация запуска
     *
     * @var RunContext
     */
    protected $context;
    
    /**
     * @return RunCore
     */
    public function getCore()
    {
        return $this->core;
    }
    
    /**
     * @param RunCore $core
     */
    public function setCore($core)
    {
        $this->core = $core;
    }
    
    /**
     * @return RuntimeLog
     */
    public function getRuntime()
    {
        return $this->runtime;
    }
    
    /**
     * @param RuntimeLog $runtime
     */
    public function setRuntime($runtime)
    {
        $this->runtime = $runtime;
    }
    
    /**
     * @return RunContext
     */
    public function getContext()
    {
        return $this->context;
    }
    
    /**
     * @param RunContext $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }
    
    public function follow(RunModuleProto $module)
    {
        $this->context = $module->getContext();
        $this->runtime = $module->getRuntime();
        $this->core    = $module->getCore();
    }
}