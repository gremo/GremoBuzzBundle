<?php

/*
 * This file is part of the buzz-bundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\BuzzBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Gremo\BuzzBundle\DependencyInjection\Compiler\AddListenersPass;

class GremoBuzzBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddListenersPass());
    }
}
