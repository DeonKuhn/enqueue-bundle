<?php

namespace Enqueue\Bundle;

use Enqueue\AmqpBunny\AmqpContext as AmqpBunnyContext;
use Enqueue\AmqpBunny\Symfony\AmqpBunnyTransportFactory;
use Enqueue\AmqpBunny\Symfony\RabbitMqAmqpBunnyTransportFactory;
use Enqueue\AmqpExt\AmqpContext;
use Enqueue\AmqpExt\Symfony\AmqpTransportFactory;
use Enqueue\AmqpExt\Symfony\RabbitMqAmqpTransportFactory;
use Enqueue\AmqpLib\AmqpContext as AmqpLibContext;
use Enqueue\AmqpLib\Symfony\AmqpLibTransportFactory;
use Enqueue\AmqpLib\Symfony\RabbitMqAmqpLibTransportFactory;
use Enqueue\AsyncEventDispatcher\DependencyInjection\AsyncEventsPass;
use Enqueue\AsyncEventDispatcher\DependencyInjection\AsyncTransformersPass;
use Enqueue\Bundle\DependencyInjection\Compiler\BuildClientExtensionsPass;
use Enqueue\Bundle\DependencyInjection\Compiler\BuildClientRoutingPass;
use Enqueue\Bundle\DependencyInjection\Compiler\BuildConsumptionExtensionsPass;
use Enqueue\Bundle\DependencyInjection\Compiler\BuildExclusiveCommandsExtensionPass;
use Enqueue\Bundle\DependencyInjection\Compiler\BuildProcessorRegistryPass;
use Enqueue\Bundle\DependencyInjection\Compiler\BuildQueueMetaRegistryPass;
use Enqueue\Bundle\DependencyInjection\Compiler\BuildTopicMetaSubscribersPass;
use Enqueue\Bundle\DependencyInjection\EnqueueExtension;
use Enqueue\Dbal\DbalContext;
use Enqueue\Dbal\Symfony\DbalTransportFactory;
use Enqueue\Fs\FsContext;
use Enqueue\Fs\Symfony\FsTransportFactory;
use Enqueue\Redis\RedisContext;
use Enqueue\Redis\Symfony\RedisTransportFactory;
use Enqueue\Sqs\SqsContext;
use Enqueue\Sqs\Symfony\SqsTransportFactory;
use Enqueue\Stomp\StompContext;
use Enqueue\Stomp\Symfony\RabbitMqStompTransportFactory;
use Enqueue\Stomp\Symfony\StompTransportFactory;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EnqueueBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new BuildConsumptionExtensionsPass());
        $container->addCompilerPass(new BuildClientRoutingPass());
        $container->addCompilerPass(new BuildProcessorRegistryPass());
        $container->addCompilerPass(new BuildTopicMetaSubscribersPass());
        $container->addCompilerPass(new BuildQueueMetaRegistryPass());
        $container->addCompilerPass(new BuildClientExtensionsPass());
        $container->addCompilerPass(new BuildExclusiveCommandsExtensionPass());

        /** @var EnqueueExtension $extension */
        $extension = $container->getExtension('enqueue');

        if (class_exists(StompContext::class)) {
            $extension->addTransportFactory(new StompTransportFactory());
            $extension->addTransportFactory(new RabbitMqStompTransportFactory());
        }

        if (class_exists(AmqpContext::class)) {
            $extension->addTransportFactory(new AmqpTransportFactory());
            $extension->addTransportFactory(new RabbitMqAmqpTransportFactory());
        }

        if (class_exists(AmqpLibContext::class)) {
            $extension->addTransportFactory(new AmqpLibTransportFactory());
            $extension->addTransportFactory(new RabbitMqAmqpLibTransportFactory());
        }

        if (class_exists(FsContext::class)) {
            $extension->addTransportFactory(new FsTransportFactory());
        }

        if (class_exists(RedisContext::class)) {
            $extension->addTransportFactory(new RedisTransportFactory());
        }

        if (class_exists(DbalContext::class)) {
            $extension->addTransportFactory(new DbalTransportFactory());
        }

        if (class_exists(SqsContext::class)) {
            $extension->addTransportFactory(new SqsTransportFactory());
        }

        if (class_exists(AmqpBunnyContext::class)) {
            $extension->addTransportFactory(new AmqpBunnyTransportFactory());
            $extension->addTransportFactory(new RabbitMqAmqpBunnyTransportFactory());
        }

        $container->addCompilerPass(new AsyncEventsPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 100);
        $container->addCompilerPass(new AsyncTransformersPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 100);
    }
}
