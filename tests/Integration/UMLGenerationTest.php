<?php
declare(strict_types=1);

namespace UMLGenerationBundle\Tests\Integration;

use PHPUnit\Framework\TestCase;
use UMLGenerationBundle\Formatter\AttributeFormatter;
use UMLGenerationBundle\Formatter\ClassFormatter;
use UMLGenerationBundle\Formatter\RelationsFormatter;
use UMLGenerationBundle\Handler\Relation\ClassExtendsHandler;
use UMLGenerationBundle\Handler\Relation\ManyToManyRelationHandler;
use UMLGenerationBundle\Handler\Relation\ManyToOneRelationHandler;
use UMLGenerationBundle\Model\Attribute;
use UMLGenerationBundle\Model\ObjectClass;
use UMLGenerationBundle\Model\Relation;
use UMLGenerationBundle\Service\Class2UMLService;
use UMLGenerationBundle\Service\PrinterService;
use UMLGenerationBundle\Tests\Data\BaseTestClass;
use UMLGenerationBundle\Tests\Data\SubTestClass;

final class UMLGenerationTest extends TestCase
{
    /**
     * @test
     */
    public function createDotFile(): void
    {
        $class2UMLService = new Class2UMLService(
            [
                new ManyToOneRelationHandler(),
                new ManyToManyRelationHandler(),
            ],
            new ClassExtendsHandler(),
        );

        $class2UMLService->generateClassBox(ObjectClass::class);
        $class2UMLService->generateClassBox(Attribute::class);
        $class2UMLService->generateClassBox(Relation::class);

        $class2UMLService->generateClassBox(BaseTestClass::class);
        $class2UMLService->generateClassBox(SubTestClass::class);

        $printerService = new PrinterService(
            new ClassFormatter(new AttributeFormatter()),
            new RelationsFormatter(),
        );

        $print = $printerService->print($class2UMLService->getClasses(), $class2UMLService->getRelations());

        file_put_contents(__DIR__ . '/../Data/result.dot', $print);
        self::assertNotEmpty($print);
    }
}
