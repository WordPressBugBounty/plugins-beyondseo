<?php

declare(strict_types=1);
// src/Security/AccessDeniedHandler.php
namespace BeyondSEODeps\DDD\Symfony\Commands\Base\DoctrineModels;

use BeyondSEODeps\DDD\Domain\Common\Entities\Accounts\Account;
use BeyondSEODeps\DDD\Domain\Common\Services\EntityModelGeneratorService;
use BeyondSEODeps\DDD\Infrastructure\Services\DDDService;
use BeyondSEODeps\Symfony\Component\Console\Attribute\AsCommand;
use BeyondSEODeps\Symfony\Component\Console\Command\Command;
use BeyondSEODeps\Symfony\Component\Console\Input\InputInterface;
use BeyondSEODeps\Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:generate-doctrine-models-for-entities',
    description: 'Creates Doctrine models based on entities',
    hidden: false
)]
class CreateDoctrineModels extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        putenv('SYMFONY_DEPRECATIONS_HELPER=disabled');
        $output->writeln([
            'Doctrine Model Generator (generates Doctrine Models based on Entities)',
            '============',
        ]);
        //echo AppService::instance()->getContainerServiceClassNameForClass(\BeyondSEODeps\DDD\Symfony\Commands\Common\Crons\CronsExecute::class);
        //die();
        $entityModelGeneratorService = new EntityModelGeneratorService();
        $classes = $entityModelGeneratorService->getAllEntityClasses();
        foreach ($classes as $classWithNamespace){
            $output->writeln("Generating Doctrine model for {$classWithNamespace->name}");
            $entityModelGeneratorService->generateDoctrineModelForEntityClass($classWithNamespace->getNameWithNamespace(),true);
        }
        return Command::SUCCESS;
    }

}