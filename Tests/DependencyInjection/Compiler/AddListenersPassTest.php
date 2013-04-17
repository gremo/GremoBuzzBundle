<?php

/*
 * This file is part of the GremoBuzzBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\BuzzBundle\Tests\DependencyInjection\Compiler;

use Gremo\BuzzBundle\DependencyInjection\Compiler\AddListenersPass;
use Symfony\Component\DependencyInjection\Reference;

class AddListenersPassTest extends \PHPUnit_Framework_TestCase
{
    private $pass;

    public function setUp()
    {
        $this->pass = new AddListenersPass();
    }

    public function testProcessExitIfBuzzeServiceIsNotDefined()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $browser   = $this->getMock('Symfony\Component\DependencyInjection\Definition');

        $container->expects($this->atLeastOnce())
            ->method('hasDefinition')
            ->with('gremo_buzz')
            ->will($this->returnValue(false));

        $browser->expects($this->never())
            ->method('addMethodCall');

        /** @noinspection PhpParamsInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        $this->pass->process($container);
    }

    public function testProcessWithoutListenersDoesNothing()
    {
        $browser   = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');

        $container->expects($this->atLeastOnce())
            ->method('hasDefinition')
            ->with('gremo_buzz')
            ->will($this->returnValue(true));

        $container->expects($this->once())
            ->method('findTaggedServiceIds')
            ->will($this->returnValue(array()));

        $browser->expects($this->never())
            ->method('addMethodCall');

        /** @noinspection PhpParamsInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        $this->pass->process($container);
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function testProcessIfListenerIsInvalidThrowsException()
    {
        $listener  = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');

        $container->expects($this->atLeastOnce())
            ->method('hasDefinition')
            ->with('gremo_buzz')
            ->will($this->returnValue(true));

        $container->expects($this->once())
            ->method('findTaggedServiceIds')
            ->will($this->returnValue(array('listener' => array())));

        $container->expects($this->atLeastOnce())
            ->method('getDefinition')
            ->with('listener')
            ->will($this->returnValue($listener));

        $listener->expects($this->once())
            ->method('getClass')
            ->will($this->returnValue('stdClass'));

        /** @noinspection PhpParamsInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        $this->pass->process($container);
    }

    public function testProcessAddListenersHonoringPriority()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $listener  = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $browser   = $this->getMock('Symfony\Component\DependencyInjection\Definition');

        $listeners = array(
            'listener_3' => array(array('priority' => '-1')),
            'listener_2' => array(array('priority' => 0)),
            'listener_0' => array(array('priority' => '6')),
            'listener_1' => array(array('priority' => 5)),
        );

        $container->expects($this->atLeastOnce())
            ->method('hasDefinition')
            ->with('gremo_buzz')
            ->will($this->returnValue(true));

        $container->expects($this->once())
            ->method('findTaggedServiceIds')
            ->will($this->returnValue($listeners));

        $container->expects($this->exactly(count($listeners) + 1))
            ->method('getDefinition')
            ->will(
                $this->returnCallback(
                    function ($srv) use ($listener, $browser) {
                        return $srv == 'gremo_buzz' ? $browser : $listener;
                    }
                )
            );

        $listener->expects($this->atLeastOnce())
            ->method('getClass')
            ->will($this->returnValue('Gremo\BuzzBundle\Tests\DependencyInjection\Compiler\Fixture\BuzzListener'));

        for ($i = 0; $i <= count($listeners) - 1; $i++) {
            $browser->expects($this->at($i))
                ->method('addMethodCall')
                ->with('addListener', array(new Reference("listener_$i")));
        }

        /** @noinspection PhpParamsInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        $this->pass->process($container);
    }
}
