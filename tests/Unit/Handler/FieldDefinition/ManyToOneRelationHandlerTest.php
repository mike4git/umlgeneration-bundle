<?php

namespace UMLGenerationBundle\Tests\Unit\Handler\FieldDefinition;

use PHPUnit\Framework\TestCase;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\Data\ManyToOneRelation;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use UMLGenerationBundle\Handler\FieldDefinition\ManyToOneRelationHandler;
use UMLGenerationBundle\Model\Relation;
use UMLGenerationBundle\Tests\Unit\Handler\AssertionHelper;

class ManyToOneRelationHandlerTest extends TestCase
{
    use ProphecyTrait;

    private ManyToOneRelationHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new ManyToOneRelationHandler();
    }

    /**
     * @test
     */
    public function canHandleShouldReturnTrueOnManyToOneRelation(): void
    {
        /** @var ManyToOneRelation|ObjectProphecy $fieldDefinition */
        $fieldDefinition = $this->prophesize(ManyToOneRelation::class);
        $fieldDefinition->getObjectsAllowed()->willReturn(true);

        self::assertTrue($this->handler->canHandle($fieldDefinition->reveal()));
    }

    /**
     * @test
     */
    public function canHandleShouldReturnFalseOnNonManyToOneRelation(): void
    {
        $fieldDefinition = $this->prophesize(Data::class);

        self::assertFalse($this->handler->canHandle($fieldDefinition->reveal()));
    }

    public function testHandle(): void
    {
        /** @var ManyToOneRelation|ObjectProphecy $fieldDefinition */
        $fieldDefinition = $this->prophesize(ManyToOneRelation::class);
        $fieldDefinition->getClasses()->willReturn(
            [
                ['classes' => 'TargetType'],
            ],
        );
        $fieldDefinition->getName()->willReturn('my Targets');
        $fieldDefinition->getTitle()->willReturn('sourceRole');
        $fieldDefinition->getMandatory()->willReturn(true);

        /** @var ClassDefinition|ObjectProphecy $classDefinition */
        $classDefinition = $this->prophesize(ClassDefinition::class);
        $classDefinition->getName()->willReturn('SourceType');

        /** @var Relation[] $relations */
        $relations = [];

        $this->handler->handle($classDefinition->reveal(), $fieldDefinition->reveal(), $relations);

        self::assertCount(1, $relations);
        AssertionHelper::assertRelations(
            $this,
            $relations['SourceType.my Targets - TargetType'],
            'SourceType',
            'TargetType',
            'sourceRole',
            null,
            1,
            1,
        );
    }

    public function testHandleMultipleAllowedTypes(): void
    {
        /** @var ManyToOneRelation|ObjectProphecy $fieldDefinition */
        $fieldDefinition = $this->prophesize(ManyToOneRelation::class);
        $fieldDefinition->getClasses()->willReturn(
            [
                ['classes' => 'TargetType1'],
                ['classes' => 'TargetType2'],
                ['classes' => 'TargetType3'],
            ],
        );
        $fieldDefinition->getName()->willReturn('my Targets');
        $fieldDefinition->getTitle()->willReturn('sourceRole');
        $fieldDefinition->getMandatory()->willReturn(true);

        /** @var ClassDefinition|ObjectProphecy $classDefinition */
        $classDefinition = $this->prophesize(ClassDefinition::class);
        $classDefinition->getName()->willReturn('SourceType');

        /** @var Relation[] $relations */
        $relations = [];

        $this->handler->handle($classDefinition->reveal(), $fieldDefinition->reveal(), $relations);

        self::assertCount(3, $relations);
        AssertionHelper::assertRelations(
            $this,
            $relations['SourceType.my Targets - TargetType1'],
            'SourceType',
            'TargetType1',
            'sourceRole',
            null,
            1,
            1,
        );
        AssertionHelper::assertRelations(
            $this,
            $relations['SourceType.my Targets - TargetType2'],
            'SourceType',
            'TargetType2',
            'sourceRole',
            null,
            1,
            1,
        );
        AssertionHelper::assertRelations(
            $this,
            $relations['SourceType.my Targets - TargetType3'],
            'SourceType',
            'TargetType3',
            'sourceRole',
            null,
            1,
            1,
        );
    }
}
