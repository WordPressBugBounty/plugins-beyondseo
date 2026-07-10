<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Bundle\FrameworkBundle\Command;

use BeyondSEODeps\Symfony\Component\Console\Attribute\AsCommand;
use BeyondSEODeps\Symfony\Component\Console\Command\Command;
use BeyondSEODeps\Symfony\Component\Console\Completion\CompletionInput;
use BeyondSEODeps\Symfony\Component\Console\Completion\CompletionSuggestions;
use BeyondSEODeps\Symfony\Component\Console\Exception\InvalidArgumentException;
use BeyondSEODeps\Symfony\Component\Console\Input\InputArgument;
use BeyondSEODeps\Symfony\Component\Console\Input\InputInterface;
use BeyondSEODeps\Symfony\Component\Console\Input\InputOption;
use BeyondSEODeps\Symfony\Component\Console\Output\OutputInterface;
use BeyondSEODeps\Symfony\Component\Workflow\Definition;
use BeyondSEODeps\Symfony\Component\Workflow\Dumper\GraphvizDumper;
use BeyondSEODeps\Symfony\Component\Workflow\Dumper\MermaidDumper;
use BeyondSEODeps\Symfony\Component\Workflow\Dumper\PlantUmlDumper;
use BeyondSEODeps\Symfony\Component\Workflow\Dumper\StateMachineGraphvizDumper;
use BeyondSEODeps\Symfony\Component\Workflow\Marking;

/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 *
 * @final
 */
#[AsCommand(name: 'workflow:dump', description: 'Dump a workflow')]
class WorkflowDumpCommand extends Command
{
    /**
     * string is the service id.
     *
     * @var array<string, Definition>
     */
    private array $workflows = [];

    private const DUMP_FORMAT_OPTIONS = [
        'puml',
        'mermaid',
        'dot',
    ];

    public function __construct(array $workflows)
    {
        parent::__construct();

        $this->workflows = $workflows;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDefinition([
                new InputArgument('name', InputArgument::REQUIRED, 'A workflow name'),
                new InputArgument('marking', InputArgument::IS_ARRAY, 'A marking (a list of places)'),
                new InputOption('label', 'l', InputOption::VALUE_REQUIRED, 'Label a graph'),
                new InputOption('dump-format', null, InputOption::VALUE_REQUIRED, 'The dump format ['.implode('|', self::DUMP_FORMAT_OPTIONS).']', 'dot'),
            ])
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> command dumps the graphical representation of a
workflow in different formats

<info>DOT</info>:  %command.full_name% <workflow name> | dot -Tpng > workflow.png
<info>PUML</info>: %command.full_name% <workflow name> --dump-format=puml | java -jar plantuml.jar -p > workflow.png
<info>MERMAID</info>: %command.full_name% <workflow name> --dump-format=mermaid | mmdc -o workflow.svg
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $workflowName = $input->getArgument('name');

        $workflow = null;

        if (isset($this->workflows['workflow.'.$workflowName])) {
            $workflow = $this->workflows['workflow.'.$workflowName];
            $type = 'workflow';
        } elseif (isset($this->workflows['state_machine.'.$workflowName])) {
            $workflow = $this->workflows['state_machine.'.$workflowName];
            $type = 'state_machine';
        }

        if (null === $workflow) {
            throw new InvalidArgumentException(sprintf('No service found for "workflow.%1$s" nor "state_machine.%1$s".', $workflowName));
        }

        switch ($input->getOption('dump-format')) {
            case 'puml':
                $transitionType = 'workflow' === $type ? PlantUmlDumper::WORKFLOW_TRANSITION : PlantUmlDumper::STATEMACHINE_TRANSITION;
                $dumper = new PlantUmlDumper($transitionType);
                break;

            case 'mermaid':
                $transitionType = 'workflow' === $type ? MermaidDumper::TRANSITION_TYPE_WORKFLOW : MermaidDumper::TRANSITION_TYPE_STATEMACHINE;
                $dumper = new MermaidDumper($transitionType);
                break;

            case 'dot':
            default:
                $dumper = ('workflow' === $type) ? new GraphvizDumper() : new StateMachineGraphvizDumper();
        }

        $marking = new Marking();

        foreach ($input->getArgument('marking') as $place) {
            $marking->mark($place);
        }

        $options = [
            'name' => $workflowName,
            'nofooter' => true,
            'graph' => [
                'label' => $input->getOption('label'),
            ],
        ];
        $output->writeln($dumper->dump($workflow, $marking, $options));

        return 0;
    }

    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        if ($input->mustSuggestArgumentValuesFor('name')) {
            $suggestions->suggestValues(array_keys($this->workflows));
        }

        if ($input->mustSuggestOptionValuesFor('dump-format')) {
            $suggestions->suggestValues(self::DUMP_FORMAT_OPTIONS);
        }
    }
}
