<?php

declare(strict_types=1);

namespace App\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Компилятор стратегий обработки заявок на шутку
 */
class JokeRequestStrategyCompilerPass implements CompilerPassInterface
{
    /**
     * Инициализирует конфигурацию приложения
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $commandHandler = $container->findDefinition('App\Strategy\JokeRequest\RequestHandler');

        $strategyServiceIds = array_keys($container->findTaggedServiceIds('app.strategy.joke_request'));

        foreach ($strategyServiceIds as $serviceId) {
            $commandHandler->addMethodCall(
                'addStrategy',
                [new Reference($serviceId)]
            );
        }
    }
}
