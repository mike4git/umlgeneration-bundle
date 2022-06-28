<?php

declare(strict_types=1);

namespace UMLGenerationBundle\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use UMLGenerationBundle\Repository\ClassDefinitionsRepositoryInterface;
use UMLGenerationBundle\Service\ClassDefinition2UMLService;
use UMLGenerationBundle\Service\PrinterService;

class UMLGenerationCommand extends AbstractCommand
{
    private const COMMAND_NAME = 'uml:generate';
    private ClassDefinitionsRepositoryInterface $classDefinitionsRepository;
    private ClassDefinition2UMLService $classDefinition2UMLService;
    private PrinterService $printerService;

    public function __construct(
        ClassDefinitionsRepositoryInterface $classDefinitionsRepository,
        ClassDefinition2UMLService $classDefinition2UMLService,
        PrinterService $printerService,
        string $name = null
    ) {
        $this->printerService = $printerService;
        $this->classDefinition2UMLService = $classDefinition2UMLService;
        $this->classDefinitionsRepository = $classDefinitionsRepository;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('Creates a dot file which can converted to an image by graphviz.')
            ->setHelp('exports all class definitions and generate a dot file.'
                . \PHP_EOL . 'This dot file can be converted into several formats by graphviz', );
        $this
            ->addOption(
                'outputfile',
                'o',
                InputOption::VALUE_REQUIRED,
                'name of the outputfile (without suffix .dot) which will be generated'
                . PHP_EOL . 'by default  \<definitions\> will be used.',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outputfile = $input->getOption('outputfile');
        if (!$outputfile) {
            $outputfile = './definitions';
        }

        $definitions = $this->classDefinitionsRepository->findDefinitions();

        foreach ($definitions as $definition) {
            $this->classDefinition2UMLService->generateClassBox($definition);
            $this->classDefinition2UMLService->generateRelations($definition);
        }

        file_put_contents(
            $outputfile . '.dot',
            $this->printerService->print(
                $this->classDefinition2UMLService->getClasses(),
                $this->classDefinition2UMLService->getRelations(),
            ),
        );

        return 0;
    }
}
