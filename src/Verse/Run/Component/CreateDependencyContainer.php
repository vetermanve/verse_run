<?php


namespace Verse\Run\Component;


use Verse\Di\Env;
use Verse\Di\SimpleContainer;
use Verse\Modular\ModularContextProto;
use Verse\Run\RunContext;

class CreateDependencyContainer extends RunComponentProto
{

    public function run()
    {
        $context = $this->context;

        if ($context->getPath([RunContext::GLOBAL_CONFIG, 'error', 'debug'])) {
            $this->context->setEnv(RunContext::ENV_DEBUG, true);
        }

        $container = new SimpleContainer();

        $container->setModule('logger', $this->runtime);

        $container->setModule('config', function () use ($context) {
            $data = $context->get(RunContext::GLOBAL_CONFIG, []);
            $context = new ModularContextProto();
            $context->fill($data);
            return $context;
        });
        
        Env::setContainer($container);
    }
}