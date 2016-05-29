<?php

/*
 * This file is part of the buzz-bundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\BuzzBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;

class AddListenersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('gremo_buzz')) {
            return;
        }

        // Build listeners queue from tagged services
        $listeners = new \SplPriorityQueue();

        foreach ($container->findTaggedServiceIds('gremo_buzz.listener') as $id => $attrs) {
            $class = $container->getDefinition($id)->getClass();
            $class = $container->getParameterBag()->resolveValue($class);

            $reflector = new \ReflectionClass($class);
            $interface = 'Buzz\Listener\ListenerInterface';

            // Check if the service implements the above interface
            if (!$reflector->isSubclassOf($interface)) {
                throw new InvalidArgumentException(sprintf("Service '%s' must implement '%s'.", $id, $interface));
            }

            // Add a reference to the listeners queue providing a default priority
            $listeners->insert(new Reference($id), isset($attrs[0]['priority']) ? $attrs[0]['priority'] : 0);
        }

        if (!empty($listeners)) {
            $browser = $container->getDefinition('gremo_buzz');

            // Add listeners starting from those with higher priority
            foreach ($listeners as $listener) {
                $browser->addMethodCall('addListener', array($listener));
            }
        }
    }
}
