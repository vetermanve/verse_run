<?php


namespace Verse\Run\Event\Object;


use Mu\Interfaces\DescribableInterface;
use Verse\Run\Event\EventConfig;

class RuntimeDispatch extends EventObjectProto
{
    /**
     * @var DescribableInterface
     */
    private $loopOwner;
    
    /**
     * RuntimeDispatch constructor.
     *
     * @param $loopOwner
     */
    public function __construct(DescribableInterface $loopOwner)
    {
        $this->loopOwner = $loopOwner;
    }
    
    public function getId()
    {
        return EventConfig::EVENT_RUNTIME_DISPATCH;
    }
}