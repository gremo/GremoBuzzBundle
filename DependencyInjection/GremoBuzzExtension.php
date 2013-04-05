<?php

/*
 * This file is part of the GremoBuzzBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\BuzzBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class GremoBuzzExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        // Set the class parameter based on the client
        $container->setParameter(
            'gremo_buzz.client.class',
            $container->getParameter("gremo_buzz.client.{$config['client']}.class")
        );

        // Get the client definition
        $client = $container->getDefinition('gremo_buzz.client');

        // Dynamically add a method call to the chosen client
        foreach ($config['options'] as $key => $val) {
            $client->addMethodCall('set'.implode(array_map('ucfirst', explode('_', $key))), array($val));
        }
    }
}
