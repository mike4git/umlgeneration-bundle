<?php
declare(strict_types=1);

namespace UMLGenerationBundle\Service;

use UMLGenerationBundle\Formatter\ClassFormatter;
use UMLGenerationBundle\Formatter\RelationsFormatter;
use UMLGenerationBundle\Model\ObjectClass;
use UMLGenerationBundle\Model\Relation;

class PrinterService
{
    private ClassFormatter $classFormatter;
    private RelationsFormatter $relationsFormatter;

    public function __construct(
        ClassFormatter $classFormatter,
        RelationsFormatter $relationsFormatter,
    ) {
        $this->relationsFormatter = $relationsFormatter;
        $this->classFormatter = $classFormatter;
    }

    /**
     * @param ObjectClass[] $classes
     * @param Relation[] $relations
     *
     * @return string
     */
    public function print(array $classes, array $relations)
    {
        $output = <<<OUTPUT
        digraph {
        %s
        %s
        }
        OUTPUT;

        return sprintf($output, $this->printClasses($classes), $this->printRelations($relations));
    }

    /**
     * @param ObjectClass[] $classes
     */
    private function printClasses(array $classes): string
    {
        $result = [];
        foreach ($classes as $class) {
            $result[] = $this->classFormatter->format($class);
        }

        return implode(PHP_EOL, $result);
    }

    /**
     * @param Relation[] $relations
     */
    private function printRelations(array $relations): string
    {
        return $this->relationsFormatter->format($relations);
    }
}
