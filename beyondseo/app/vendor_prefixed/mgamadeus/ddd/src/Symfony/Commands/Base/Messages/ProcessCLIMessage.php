<?php

declare (strict_types=1);

namespace BeyondSEODeps\DDD\Symfony\Commands\Base\Messages;

use BeyondSEODeps\DDD\Domain\Base\Entities\MessageHandlers\AppMessage;
use BeyondSEODeps\DDD\Domain\Base\Entities\MessageHandlers\AppMessageHandler;
use BeyondSEODeps\Symfony\Component\Console\Attribute\AsCommand;
use BeyondSEODeps\Symfony\Component\Console\Command\Command;
use BeyondSEODeps\Symfony\Component\Console\Input\InputArgument;
use BeyondSEODeps\Symfony\Component\Console\Input\InputInterface;
use BeyondSEODeps\Symfony\Component\Console\Input\InputOption;
use BeyondSEODeps\Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:process-cli-message',
    description: 'Processes a Message encoded as CLI parameter and executes the corresponding MessageHandler',
    hidden: false
)]
class ProcessCLIMessage extends Command
{
    protected function configure()
    {
        $this->addArgument('message', InputArgument::REQUIRED, 'The encoded message or temp file name.');
        $this->addOption('useTempFile', null, InputOption::VALUE_NONE, 'Use temp file for transport.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $useTempFile = $input->getOption('useTempFile');

        if ($useTempFile) {
            $message = AppMessage::loadFromTempDir($input->getArgument('message'));
        } else {
            $message = AppMessage::decodeFromCommandline($input->getArgument('message'));
        }

        if (!$message) {
            $output->writeln("<error>Failed to decode message.</error>");
            return Command::FAILURE;
        }

        $handlerClass = $message::$messageHandler;
        if (!is_a($handlerClass, AppMessageHandler::class, true)) {
            $output->writeln("<error>Invalid handler class: {$handlerClass}.</error>");
            return Command::FAILURE;
        }

        $handler = new $handlerClass();
        $handler->__invoke($message);

        return Command::SUCCESS;
    }
}