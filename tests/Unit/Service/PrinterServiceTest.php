<?php

namespace UMLGenerationBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use UMLGenerationBundle\Formatter\ClassFormatter;
use UMLGenerationBundle\Formatter\RelationsFormatter;
use UMLGenerationBundle\Model\ObjectClass;
use UMLGenerationBundle\Model\Relation;
use UMLGenerationBundle\Service\PrinterService;

class PrinterServiceTest extends TestCase
{
    use ProphecyTrait;

    private PrinterService $printerService;

    /** @var ClassFormatter|ObjectProphecy */
    private $classFormatter;
    /** @var RelationsFormatter|ObjectProphecy */
    private $relationsFormatter;

    protected function setUp(): void
    {
        $this->classFormatter = $this->prophesize(ClassFormatter::class);
        $this->relationsFormatter = $this->prophesize(RelationsFormatter::class);

        $this->printerService = new PrinterService(
            $this->classFormatter->reveal(),
            $this->relationsFormatter->reveal(),
        );
    }

    /**
     * @test
     */
    public function print_regular_case(): void
    {
        $classDefinition = $this->prophesize(ObjectClass::class);
        $relation = $this->prophesize(Relation::class);

        $this->classFormatter->format($classDefinition->reveal())->willReturn('class');
        $this->relationsFormatter->format([$relation->reveal()])->willReturn('relations');

        self::assertEquals(
            <<<OUTPUT
            digraph {
            class
            relations
            }
            OUTPUT,
            $this->printerService->print([$classDefinition->reveal()], [$relation->reveal()]),
        );
    }

    /**
     * @test
     */
    public function print_empty_arrays(): void
    {
        $this->relationsFormatter->format([])->willReturn('');
        self::assertEquals(
            <<<OUTPUT
            digraph {


            }
            OUTPUT
            ,
            $this->printerService->print([], []),
        );
    }
}
