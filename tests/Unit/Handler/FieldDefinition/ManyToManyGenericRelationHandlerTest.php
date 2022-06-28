<?php declare(strict_types=1);

namespace UMLGenerationBundle\Tests\Unit\Handler\FieldDefinition;

use PHPUnit\Framework\TestCase;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyRelation;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use UMLGenerationBundle\Handler\FieldDefinition\ManyToManyGenericRelationHandler;
use UMLGenerationBundle\Model\Relation;
use UMLGenerationBundle\Tests\Unit\Helper\AssertionHelper;

class ManyToManyGenericRelationHandlerTest extends TestCase
{
    use ProphecyTrait;

    private ManyToManyGenericRelationHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new ManyToManyGenericRelationHandler();
    }

    /**
     * @test
     */
    public function canHandleShouldReturnFalseOnTypedManyToManyRelation(): void
    {
        /** @var ManyToManyRelation|ObjectProphecy $fieldDefinition */
        $fieldDefinition = $this->prophesize(ManyToManyRelation::class);
        $fieldDefinition->getObjectsAllowed()->willReturn(true);
        $fieldDefinition->getClasses()->willReturn([
            [
                'classes' => 'AnySpecifiedTargetType',
            ],
        ]);

        self::assertFalse($this->handler->canHandle($fieldDefinition->reveal()));
    }

    /**
     * @test
     */
    public function canHandleShouldReturnTrueOnGenericManyToManyObjectRelation(): void
    {
        /** @var Data\ManyToManyObjectRelation|ObjectProphecy $fieldDefinition */
        $fieldDefinition = $this->prophesize(Data\ManyToManyObjectRelation::class);
        $fieldDefinition->getObjectsAllowed()->willReturn(true);
        $fieldDefinition->getClasses()->willReturn([]);

        self::assertTrue($this->handler->canHandle($fieldDefinition->reveal()));
    }

    /**
     * @test
     */
    public function canHandleShouldReturnFalseOnGenericManyToManyObjectRelation(): void
    {
        /** @var Data\ManyToManyObjectRelation|ObjectProphecy $fieldDefinition */
        $fieldDefinition = $this->prophesize(Data\ManyToManyObjectRelation::class);
        $fieldDefinition->getObjectsAllowed()->willReturn(true);
        $fieldDefinition->getClasses()->willReturn([['classes' => 'AnyType']]);

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
        /** @var ManyToManyRelation|ObjectProphecy $fieldDefinition */
        $fieldDefinition = $this->prophesize(ManyToManyRelation::class);
        $fieldDefinition->getClasses()->willReturn([]);
        $fieldDefinition->getName()->willReturn('my Targets');
        $fieldDefinition->getTitle()->willReturn('sourceRole');
        $fieldDefinition->getMandatory()->willReturn(true);
        $fieldDefinition->getMaxItems()->willReturn(7);

        /** @var ClassDefinition|ObjectProphecy $classDefinition */
        $classDefinition = $this->prophesize(ClassDefinition::class);
        $classDefinition->getName()->willReturn('SourceType');

        /** @var Relation[] $relations */
        $relations = [];

        $this->handler->handle($classDefinition->reveal(), $fieldDefinition->reveal(), $relations);

        self::assertCount(1, $relations);
        AssertionHelper::assertRelation(
            $this,
            $relations['SourceType.my Targets - Pimcore\Model\DataObject'],
            'SourceType',
            'Pimcore\Model\DataObject',
            'sourceRole',
            null,
            1,
            7,
        );
    }
}
