<?php

/*
 * This file is part of the GremoBuzzBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\BuzzBundle\Tests\DependencyInjection;

use Gremo\BuzzBundle\DependencyInjection\GremoBuzzExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GremoBuzzExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Gremo\BuzzBundle\DependencyInjection\GremoBuzzExtension
     */
    private $extension;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private $container;

    public function setUp()
    {
        $this->extension = new GremoBuzzExtension();
        $this->container = new ContainerBuilder();
    }

    /**
     * @dataProvider getValidClients
     */
    public function testLoadSetsTheClientClassParameter($client)
    {
        if(in_array(strtolower($client), array('curl', 'multi_curl')) && !extension_loaded('curl')) {
            $this->setExpectedException('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException');
        }

        $this->extension->load(array(array('client' => $client)), $this->container);

        $this->assertTrue($this->container->hasParameter('gremo_buzz.client.class'));
    }

    /**
     * @dataProvider getOptionsWithValuesAndMethods
     */
    public function testLoadIfOptionsAreProvidedAddsMethodCall($name, $value, $method)
    {
        $this->extension->load(array(array('options' => array($name => $value))), $this->container);

        $calls = $this->container->getDefinition('gremo_buzz.client')->getMethodCalls();
        $this->assertArrayHasKey(0, $calls);
        $this->assertInternalType('array', $calls[0]);
        $this->assertArrayHasKey(0, $calls[0]);
        $this->assertEquals($method, $calls[0][0]);
        $this->assertArrayHasKey(1, $calls[0]);
        $this->assertInternalType('array', $calls[0][1]);
        $this->assertArrayHasKey(0, $calls[0][1]);
        $this->assertEquals($value, $calls[0][1][0]);
    }

    public function getValidClients()
    {
        return array(
            array('cURL'),
            array('native'),
            array('multi_curl')
        );
    }

    public function getOptionsWithValuesAndMethods()
    {
        return array(
            array('ignore_errors', true, 'setIgnoreErrors'),
            array('max_redirects', 3, 'setMaxRedirects'),
            array('timeout', 10, 'setTimeout'),
            array('verify_peer', true, 'setVerifyPeer'),
        );
    }
}
