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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('gremo_buzz');

        $notInteger = function ($val) {
            return !is_int($val) && !(is_string($val) && ctype_digit($val));
        };

        $notPositive = function ($val) {
            return $val < 0;
        };

        $rootNode
            ->children()
                ->scalarNode('client')
                    ->defaultValue('native')
                    ->beforeNormalization()
                    ->ifString()
                        ->then(
                            function ($v) {
                                return strtolower($v);
                            }
                        )
                    ->end()
                    ->validate()
                    ->ifNotInArray(array('curl', 'multi_curl', 'native'))
                        ->thenInvalid('Unrecognized %s client, should be "curl", "multi_curl" or "native".')
                    ->end()
                    ->validate()
                    ->ifTrue(
                        function ($v) {
                            return in_array($v, array('curl', 'multi_curl')) && !function_exists('curl_init');
                        }
                    )
                    ->thenInvalid('You must enable the "curl" extension to use %s client.')
                    ->end()
                ->end()
                ->arrayNode('options')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('ignore_errors')->end()
                        ->scalarNode('max_redirects')
                            ->validate()
                            ->ifTrue($notInteger)
                                ->thenInvalid('Value for option "max_redirects" must be an integer.')
                            ->end()
                            ->validate()
                            ->ifTrue($notPositive)
                                ->thenInvalid('Value for option "max_redirects" must be greater or equal to zero.')
                            ->end()
                        ->end()
                        ->scalarNode('timeout')
                            ->validate()
                            ->ifTrue($notInteger)
                                ->thenInvalid('Value for option "timeout" must be an integer.')
                            ->end()
                            ->validate()
                            ->ifTrue($notPositive)
                                ->thenInvalid('Value for option "timeout" must be greater or equal to zero.')
                            ->end()
                        ->end()
                        ->booleanNode('verify_peer')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
