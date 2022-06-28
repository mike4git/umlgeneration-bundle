<?php

namespace UMLGenerationBundle\Tests\Unit\Handler\FieldDefinition;

use PHPUnit\Framework\TestCase;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use UMLGenerationBundle\Handler\FieldDefinition\AdvancedManyToManyObjectRelationHandler;
use UMLGenerationBundle\Model\Relation;
use UMLGenerationBundle\Tests\Unit\Helper\AssertionHelper;

class AdvancedManyToManyObjectRelationHandlerTest extends TestCase
{
    use ProphecyTrait;

    private AdvancedManyToManyObjectRelationHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new AdvancedManyToManyObjectRelationHandler();
    }

    /**
     * @test
     */
    public function canHandleShouldReturnTrueOnAdvancedManyToManyObjectRelation(): void
    {
        /** @var Data\AdvancedManyToManyObjectRelation|ObjectProphecy $fieldDefinition */
        $fieldDefinition = $this->prophesize(Data\AdvancedManyToManyObjectRelation::class);
        $fieldDefinition->getAllowedClassId()->willReturn('TargetType');

        self::assertTrue($this->handler->canHandle($fieldDefinition->reveal()));
    }

    /**
     * @test
     */
    public function canHandleShouldReturnFalseOnOtherRelations(): void
    {
        /** @var Data|ObjectProphecy $fieldDefinition */
        $fieldDefinition = $this->prophesize(Data::class);

        self::assertFalse($this->handler->canHandle($fieldDefinition->reveal()));
    }

    /**
     * @test
     */
    public function canHandleShouldReturnFalseOnNonManyToManyRelation(): void
    {
        $fieldDefinition = $this->prophesize(Data\ReverseObjectRelation::class);

        self::assertFalse($this->handler->canHandle($fieldDefinition->reveal()));
    }

    public function testHandle(): void
    {
        /** @var Data\AdvancedManyToManyObjectRelation|ObjectProphecy $fieldDefinition */
        $fieldDefinition = $this->prophesize(Data\AdvancedManyToManyObjectRelation::class);
        $fieldDefinition->getAllowedClassId()->willReturn('TargetType');
        $fieldDefinition->getName()->willReturn('my Targets');
        $fieldDefinition->getTitle()->willReturn('sourceRole');
        $fieldDefinition->getMaxItems()->willReturn(7);

        /** @var ClassDefinition|ObjectProphecy $classDefinition */
        $classDefinition = $this->prophesize(ClassDefinition::class);
        $classDefinition->getName()->willReturn('SourceType');

        /** @var Relation[] $relations */
        $relations = [];

        $this->handler->handle($classDefinition->reveal(), $fieldDefinition->reveal(), $relations);

        self::assertCount(2, $relations);
        AssertionHelper::assertRelation(
            $this,
            $relations['SourceType.my Targets - SourceType2TargetType'],
            'SourceType',
            'SourceType2TargetType',
            'sourceRole',
            null,
            0,
            7,
        );
        AssertionHelper::assertRelation(
            $this,
            $relations['SourceType2TargetType - TargetType'],
            'SourceType2TargetType',
            'TargetType',
            '',
            null,
            0,
            null,
        );
    }
}
