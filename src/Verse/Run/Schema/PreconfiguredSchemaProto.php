<?php


namespace Verse\Run\Schema;


use Verse\Run\Component\RunComponentProto;
use Verse\Run\Processor\RunRequestProcessorProto;

abstract class PreconfiguredSchemaProto extends RunSchemaProto
{

    /**
     * Set-up components
     *
     * @var RunComponentProto[]
     */
    protected $components = [];

    /**
     * @var RunRequestProcessorProto
     */
    protected $processor;

    /**
     * Add component to set-up pipelines
     *
     * @param RunComponentProto $component
     */
    public function addComponent (RunComponentProto $component)
    {
        $this->components[] = $component;
    }
    
    protected function _addCustomComponents() {
        foreach ($this->components as $component) {
            $this->core->addComponent($component);
        }
    }

    /**
     * @param RunRequestProcessorProto $processor
     */
    public function setProcessor(RunRequestProcessorProto $processor)
    {
        $this->processor = $processor;
    }
}