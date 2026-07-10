<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Component\Runtime\Runner\Symfony;

use BeyondSEODeps\Symfony\Component\HttpFoundation\Request;
use BeyondSEODeps\Symfony\Component\HttpKernel\HttpKernelInterface;
use BeyondSEODeps\Symfony\Component\HttpKernel\TerminableInterface;
use BeyondSEODeps\Symfony\Component\Runtime\RunnerInterface;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class HttpKernelRunner implements RunnerInterface
{
    private $kernel;
    private $request;

    public function __construct(HttpKernelInterface $kernel, Request $request)
    {
        $this->kernel = $kernel;
        $this->request = $request;
    }

    public function run(): int
    {
        $response = $this->kernel->handle($this->request);
        $response->send();

        if ($this->kernel instanceof TerminableInterface) {
            $this->kernel->terminate($this->request, $response);
        }

        return 0;
    }
}
