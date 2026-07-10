<?php

declare(strict_types=1);

namespace BeyondSEODeps\ProxyManager\GeneratorStrategy;

use BeyondSEODeps\Laminas\Code\Generator\ClassGenerator;
use BeyondSEODeps\ProxyManager\Exception\FileNotWritableException;
use BeyondSEODeps\ProxyManager\FileLocator\FileLocatorInterface;
use BeyondSEODeps\Symfony\Component\Filesystem\Exception\IOException;
use BeyondSEODeps\Symfony\Component\Filesystem\Filesystem;

/**
 * Generator strategy that writes the generated classes to disk while generating them
 *
 * {@inheritDoc}
 */
class FileWriterGeneratorStrategy implements GeneratorStrategyInterface
{
    protected $fileLocator;
    private $emptyErrorHandler;

    public function __construct(FileLocatorInterface $fileLocator)
    {
        $this->fileLocator = $fileLocator;
    }

    /**
     * Write generated code to disk and return the class code
     *
     * {@inheritDoc}
     *
     * @throws FileNotWritableException
     */
    public function generate(ClassGenerator $classGenerator): string
    {
        $generatedCode = $classGenerator->generate();
        $className     = (string) $classGenerator->getNamespaceName() . '\\' . $classGenerator->getName();
        $fileName      = $this->fileLocator->getProxyFileName($className);

        try {
            (new Filesystem())->dumpFile($fileName, "<?php\n\n" . $generatedCode);

            return $generatedCode;
        } catch (IOException $e) {
            throw FileNotWritableException::fromPrevious($e);
        }
    }
}
