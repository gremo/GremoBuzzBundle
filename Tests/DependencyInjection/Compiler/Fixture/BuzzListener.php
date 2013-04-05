<?php

/*
 * This file is part of the GremoBuzzBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\BuzzBundle\Tests\DependencyInjection\Compiler\Fixture;

use Buzz\Listener\ListenerInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;

class BuzzListener implements ListenerInterface
{
    public function preSend(RequestInterface $request)
    {
    }

    public function postSend(RequestInterface $request, MessageInterface $response)
    {
    }
}
