<?php
declare(strict_types=1);

namespace UMLGenerationBundle\Tests\Integration;

use PHPUnit\Framework\TestCase;
use UMLGenerationBundle\Formatter\AttributeFormatter;
use UMLGenerationBundle\Formatter\ClassFormatter;
use UMLGenerationBundle\Formatter\RelationsFormatter;
use UMLGenerationBundle\Model\Attribute;
use UMLGenerationBundle\Model\ObjectClass;
use UMLGenerationBundle\Model\Relation;
use UMLGenerationBundle\Service\Class2UMLService;
use UMLGenerationBundle\Service\PrinterService;

final class UMLGenerationTest extends TestCase
{
    /**
     * @test
     */
    public function createDotFile(): void
    {
        $class2UMLService = new Class2UMLService();

        $class2UMLService->generateClassBox(ObjectClass::class);
        $class2UMLService->generateClassBox(Attribute::class);
        $class2UMLService->generateClassBox(Relation::class);

        $printerService = new PrinterService(
            new ClassFormatter(new AttributeFormatter()),
            new RelationsFormatter(),
        );

        $print = $printerService->print($class2UMLService->getClasses(), []);

        file_put_contents('michi.dot', $print);
        self::assertNotEmpty($print);
    }
}
