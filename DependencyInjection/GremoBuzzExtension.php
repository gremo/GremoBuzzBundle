<?php

/*
 * This file is part of the buzz-bundle package.
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
        $clientClass = $container->getParameter("gremo_buzz.client.{$config['client']}.class");
        $container->setParameter('gremo_buzz.client.class', $clientClass);

        // Get the client definition and dynamically add a method calls
        $client = $container->getDefinition('gremo_buzz.client');
        foreach ($config['options'] as $key => $val) {
            $setterMethod = 'set'.implode(array_map('ucfirst', explode('_', $key)));
            if (!is_callable(array($clientClass, $setterMethod))) {
                continue;
            }

            $client->addMethodCall($setterMethod, array($val));
        }
    }
}
