<?php

namespace UMLGenerationBundle\Tests\Unit\Handler\Relation;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use UMLGenerationBundle\Handler\Relation\ClassExtendsHandler;
use UMLGenerationBundle\Model\Relation;

class ClassExtendsHandlerTest extends TestCase
{
    use ProphecyTrait;

    private ClassExtendsHandler $handler;

    /** @var \ReflectionClass|ObjectProphecy */
    private $reflectionMock;

    protected function setUp(): void
    {
        $this->reflectionMock = $this->prophesize(\ReflectionClass::class);

        $this->handler = new ClassExtendsHandler();
    }

    /**
     * @test
     */
    public function testCanHandleReturnsTrueForExistingParentClass(): void
    {
        $baseReflectionMock = $this->prophesize(\ReflectionClass::class);
        $this->reflectionMock->getParentClass()->willReturn($baseReflectionMock->reveal());
        self::assertTrue($this->handler->canHandle($this->reflectionMock->reveal()));
    }

    /**
     * @test
     */
    public function testCanHandleReturnsFalseForNonExistingParentClass(): void
    {
        $this->reflectionMock->getParentClass()->willReturn(false);
        self::assertFalse($this->handler->canHandle($this->reflectionMock->reveal()));
    }

    /**
     * @test
     */
    public function testHandleWithClassInheritance(): void
    {
        $relations = [];

        /** @var \ReflectionClass|ObjectProphecy $baseReflectionMock */
        $baseReflectionMock = $this->prophesize(\ReflectionClass::class);
        $baseReflectionMock->getShortName()->willReturn('BaseClass');

        $this->reflectionMock->getShortName()->willReturn('MyClass');
        $this->reflectionMock->getParentClass()->willReturn($baseReflectionMock->reveal());

        $this->handler->handle($this->reflectionMock->reveal(), $relations);

        $expectedRelation = new Relation();
        $expectedRelation->setSourceType('MyClass');
        $expectedRelation->setTargetType('BaseClass');
        $expectedRelation->setBidirectional(false);
        $expectedRelation->setAggregation(false);
        $expectedRelation->setInheritance(true);

        self::assertCount(1, $relations);
        self::assertEquals($expectedRelation, $relations[0]);
    }
}
