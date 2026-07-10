<?php

declare(strict_types=1);

namespace BeyondSEODeps\ProxyManager\ProxyGenerator\RemoteObject\PropertyGenerator;

use BeyondSEODeps\Laminas\Code\Generator\Exception\InvalidArgumentException;
use BeyondSEODeps\Laminas\Code\Generator\PropertyGenerator;
use BeyondSEODeps\ProxyManager\Factory\RemoteObject\AdapterInterface;
use BeyondSEODeps\ProxyManager\Generator\Util\IdentifierSuffixer;

/**
 * Property that contains the remote object adapter
 */
class AdapterProperty extends PropertyGenerator
{
    /**
     * Constructor
     *
     * @throws InvalidArgumentException
     */
    public function __construct()
    {
        parent::__construct(IdentifierSuffixer::getIdentifier('adapter'));

        $this->setVisibility(self::VISIBILITY_PRIVATE);
        $this->setDocBlock('@var \\' . AdapterInterface::class . ' Remote web service adapter');
    }
}
