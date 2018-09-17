<?php


namespace Verse\Run;


use Verse\Run\Channel\DataChannelProto;
use Verse\Run\Component\RunComponentProto;
use Verse\Run\Processor\RunRequestProcessorProto;
use Verse\Run\Provider\RequestProviderProto;
use Verse\Run\Schema\RunSchemaProto;

class RunCore extends RunModuleProto
{
    /**
     * Что будем процессить
     * 
     * @var RequestProviderProto
     */
    private $provider;
    
    /**
     * Кто будет процессить
     * 
     * @var RunRequestProcessorProto
     */
    private $processor;
    
    /**
     * Куда будем складывать результат
     * 
     * @var DataChannelProto
     */
    private $dataChannel;
    
    /**
     * Схема запуска
     * 
     * @var RunSchemaProto
     */
    private $schema;
    
    /**
     * Последний реквест который отправлялся на процессинг
     * 
     * @var RunRequest
     */
    private $lastRequest;
    
    /**
     * Компоненты
     * 
     * @var RunComponentProto[]
     */
    private $components = [];
    
    /**
     * RunCore constructor.
     */
    public function __construct()
    {
    }
    
    public function configure () 
    {
        if ($this->schema) {
            $this->schema->follow($this);
            $this->schema->configure();    
        }
    }
    
    public function prepare () 
    {
        $this->runComponents();
        
        $this->provider && $this->prepareProvider();
        $this->processor && $this->prepareProcessor();
        $this->dataChannel && $this->prepareDataChannel();
    }
    
    public function runComponents () 
    {
        foreach ($this->components as $component) {
            $component->follow($this);
            $component->run();
        }
    }
    
    public function prepareProcessor () 
    {
        $this->processor->follow($this);
        $this->processor->prepare();
    }
    
    public function prepareProvider () 
    {
        $this->provider->follow($this);
        $this->provider->prepare();        
    }
    
    public function prepareDataChannel () 
    {
        $this->dataChannel->follow($this);
        $this->dataChannel->prepare();
    }
    
    public function run ()
    {
        $this->provider->run();
    }
    
    /**
     * @return RequestProviderProto
     */
    public function getProvider()
    {
        return $this->provider;
    }
    
    /**
     * @param RequestProviderProto $provider
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }
    
    /**
     * @return RunRequestProcessorProto
     */
    public function getProcessor()
    {
        return $this->processor;
    }
    
    /**
     * @param RunRequestProcessorProto $processor
     */
    public function setProcessor($processor)
    {
        $this->processor = $processor;
    }
    
    /**
     * @return DataChannelProto
     */
    public function getDataChannel()
    {
        return $this->dataChannel;
    }
    
    /**
     * @param DataChannelProto $dataChannel
     */
    public function setDataChannel($dataChannel)
    {
        $this->dataChannel = $dataChannel;
    }
    
    public function process(RunRequest $request)
    {   
        $this->lastRequest = $request;
        $this->processor->process($request);
    }
    
    /**
     * @param RunSchemaProto $schema
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
    }
    
    public function addComponent (RunComponentProto $component) 
    {
        $this->components[] = $component;
    }
    
    public function getCore()
    {
        return $this;
    }
    
    /**
     * @return RunRequest
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }
}