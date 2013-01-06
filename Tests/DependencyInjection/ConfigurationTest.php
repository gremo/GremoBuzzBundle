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

use Gremo\BuzzBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Config\Definition\Processor
     */
    private $processor;

    /**
     * @var \Gremo\BuzzBundle\DependencyInjection\Configuration
     */
    private $configuration;

    public function setUp()
    {
        $this->configuration = new Configuration();
        $this->processor = new Processor();
    }

    public function testProcessIfConfigIsEmpty()
    {
        $config = $this->processor->processConfiguration($this->configuration, array());

        $this->assertArrayHasKey('client', $config);
        $this->assertEquals('native', $config['client']);

        $this->assertArrayHasKey('options', $config);
        $this->assertEquals(array(), $config['options']);
    }

    /**
     * @dataProvider getValidClients
     */
    public function testProcessIfClientIsValid($client)
    {
        if(in_array(strtolower($client), array('curl', 'multi_curl')) && !extension_loaded('curl')) {
            $this->setExpectedException('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException');
        }

        $configs = array(array('client' => $client));
        $this->processor->processConfiguration($this->configuration, $configs);
    }

    /**
     * @dataProvider getInvalidClients
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testProcessIfClientIsValidThrowsException($client)
    {
        $configs = array(array('client' => $client));
        $this->processor->processConfiguration($this->configuration, $configs);
    }

    /**
     * @dataProvider getValidValuesAsInteger
     */
    public function testProcessIfMaxRedirectsIsValid($maxRedirects)
    {
        $configs = array(array('options' => array('max_redirects' => $maxRedirects)));
        $this->processor->processConfiguration($this->configuration, $configs);
    }

    /**
     * @dataProvider getInvalidValuesAsInteger
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testProcessIfMaxRedirectsIsInvalidThrowsException($maxRedirects)
    {
        $configs = array(array('options' => array('max_redirects' => $maxRedirects)));
        $this->processor->processConfiguration($this->configuration, $configs);
    }

    /**
     * @dataProvider getValidValuesAsInteger
     */
    public function testProcessIfTimeoutIsValid($timeout)
    {
        $configs = array(array('options' => array('timeout' => $timeout)));
        $this->processor->processConfiguration($this->configuration, $configs);
    }

    /**
     * @dataProvider getInvalidValuesAsInteger
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testProcessIfTimeoutIsInvalidThrowsException($timeout)
    {
        $configs = array(array('options' => array('timeout' => $timeout)));
        $this->processor->processConfiguration($this->configuration, $configs);
    }

    public function getValidClients()
    {
        return array(
            array('curl'),
            array('cURL'),
            array('native'),
            array('multi_curl')
        );
    }

    public function getInvalidClients()
    {
        return array(
            array('foo'),
            array('bar')
        );
    }

    public function getInvalidValuesAsInteger()
    {
        return array(
            array(null),
            array(true),
            array(false),
            array(''),
            array('1.'),
            array(12.2),
            array(-10),
            array('-5'),
        );
    }

    public function getValidValuesAsInteger()
    {
        return array(
            array(0),
            array(12),
            array('0'),
            array('3')
        );
    }
}
