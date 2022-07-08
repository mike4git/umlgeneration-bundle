<?php

namespace UMLGenerationBundle\Tests\Unit\Handler\Relation;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use UMLGenerationBundle\Handler\Relation\ManyToManyRelationHandler;
use UMLGenerationBundle\Tests\Data\TestClass;
use UMLGenerationBundle\Tests\Data\TestClassForRelations;

class ManyToManyRelationHandlerTest extends TestCase
{
    use ProphecyTrait;

    private ManyToManyRelationHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new ManyToManyRelationHandler();
    }

    public function sampleScenarios(): iterable  //@phpstan-ignore-line
    {
        yield 'possible m:n relation between classes' => [TestClassForRelations::class, 'children', true];
        yield 'non-array typed attribute' => [TestClass::class, 'attribute3', false];
        yield 'array of primitive type typed attribute' => [TestClass::class, 'arrayAttribute', false];
    }

    /**
     * @test
     * @dataProvider sampleScenarios
     *
     * @param class-string<mixed> $class
     * @param string $propertyName
     */
    public function testCanHandleReturnsTrueInCaseOfArrayTypedProperty($class, $propertyName, bool $result): void
    {
        $property = new \ReflectionProperty($class, $propertyName);
        self::assertEquals($result, $this->handler->canHandle($property));
    }
}
