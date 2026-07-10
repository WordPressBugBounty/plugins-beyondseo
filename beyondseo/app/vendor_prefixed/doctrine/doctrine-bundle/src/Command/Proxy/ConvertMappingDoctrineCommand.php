<?php

namespace BeyondSEODeps\Doctrine\Bundle\DoctrineBundle\Command\Proxy;

use BeyondSEODeps\Doctrine\ORM\Tools\Console\Command\ConvertMappingCommand;
use BeyondSEODeps\Doctrine\ORM\Tools\Export\Driver\AbstractExporter;
use BeyondSEODeps\Doctrine\ORM\Tools\Export\Driver\XmlExporter;
use BeyondSEODeps\Doctrine\ORM\Tools\Export\Driver\YamlExporter;
use BeyondSEODeps\Symfony\Component\Console\Input\InputOption;

use function assert;

/**
 * Convert Doctrine ORM metadata mapping information between the various supported
 * formats.
 *
 * @deprecated use BeyondSEODeps\Doctrine\ORM\Tools\Console\Command\ConvertMappingCommand instead
 *
 * @psalm-suppress UndefinedClass ORM < 3
 */
class ConvertMappingDoctrineCommand extends ConvertMappingCommand
{
    use OrmProxyCommand;

    /** @return void */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('doctrine:mapping:convert');

        if ($this->getDefinition()->hasOption('em')) {
            return;
        }

        $this->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command');
    }

    /**
     * @param string $toType
     * @param string $destPath
     *
     * @return AbstractExporter
     */
    protected function getExporter($toType, $destPath)
    {
        $exporter = parent::getExporter($toType, $destPath);
        assert($exporter instanceof AbstractExporter);
        if ($exporter instanceof XmlExporter) {
            $exporter->setExtension('.orm.xml');
        } elseif ($exporter instanceof YamlExporter) {
            $exporter->setExtension('.orm.yml');
        }

        return $exporter;
    }
}
