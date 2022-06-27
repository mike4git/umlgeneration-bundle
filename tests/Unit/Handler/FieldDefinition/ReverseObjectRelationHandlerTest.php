<?php

declare(strict_types=1);

namespace UMLGenerationBundle\Tests\Unit\Handler\FieldDefinition;

use PHPUnit\Framework\TestCase;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\Data\ReverseObjectRelation;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use UMLGenerationBundle\Handler\FieldDefinition\ReverseObjectRelationHandler;
use UMLGenerationBundle\Model\Relation;
use UMLGenerationBundle\Tests\Unit\Handler\AssertionHelper;

class ReverseObjectRelationHandlerTest extends TestCase
{
    use ProphecyTrait;

    private ReverseObjectRelationHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new ReverseObjectRelationHandler();
    }

    /**
     * @test
     */
    public function canHandleShouldReturnTrueOnReverseObjectRelation(): void
    {
        $fieldDefinition = $this->prophesize(ReverseObjectRelation::class);

        self::assertTrue($this->handler->canHandle($fieldDefinition->reveal()));
    }

    /**
     * @test
     */
    public function canHandleShouldReturnFalseOnNonReverseObjectRelation(): void
    {
        $fieldDefinition = $this->prophesize(Data::class);

        self::assertFalse($this->handler->canHandle($fieldDefinition->reveal()));
    }

    /**
     * @test
     */
    public function handleShouldAddRelation(): void
    {
        /** @var ReverseObjectRelation|ObjectProphecy $fieldDefinition */
        $fieldDefinition = $this->prophesize(ReverseObjectRelation::class);
        $fieldDefinition->getOwnerClassName()->willReturn('TargetType');
        $fieldDefinition->getOwnerFieldName()->willReturn('targetField');

        $classDefinition = $this->prophesize(ClassDefinition::class);
        $classDefinition->getName()->willReturn('SourceType');
        $relations = [];

        $this->handler->handle($classDefinition->reveal(), $fieldDefinition->reveal(), $relations);

        self::assertCount(1, $relations);
        self::assertInstanceOf(Relation::class, $relations['TargetType.targetField - SourceType']);

        AssertionHelper::assertRelations(
            $this,
            $relations['TargetType.targetField - SourceType'],
            'TargetType',
            'SourceType',
            'targetField',
            null,
            0,
            null,
        );
    }

    /**
     * @test
     */
    public function handleShouldMergeExistingRelation(): void
    {
        /** @var ReverseObjectRelation|ObjectProphecy $fieldDefinition */
        $fieldDefinition = $this->prophesize(ReverseObjectRelation::class);
        $fieldDefinition->getOwnerClassName()->willReturn('TargetType');
        $fieldDefinition->getOwnerFieldName()->willReturn('targetField');

        $classDefinition = $this->prophesize(ClassDefinition::class);
        $classDefinition->getName()->willReturn('SourceType');

        $relation = new Relation();
        $relations['TargetType.targetField - SourceType'] = $relation;

        $this->handler->handle($classDefinition->reveal(), $fieldDefinition->reveal(), $relations);

        self::assertCount(1, $relations);
        self::assertTrue($relation->isBidirectional());
    }
}
