<?php declare(strict_types=1);

namespace UMLGenerationBundle\Tests\Unit\Handler\FieldDefinition;

use PHPUnit\Framework\TestCase;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyRelation;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use UMLGenerationBundle\Handler\FieldDefinition\ManyToManyRelationHandler;
use UMLGenerationBundle\Model\Relation;
use UMLGenerationBundle\Tests\Unit\Helper\AssertionHelper;

class ManyToManyRelationHandlerTest extends TestCase
{
    use ProphecyTrait;

    private ManyToManyRelationHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new ManyToManyRelationHandler();
    }

    /**
     * @test
     */
    public function canHandleShouldReturnTrueOnManyToManyRelation(): void
    {
        /** @var ManyToManyRelation|ObjectProphecy $fieldDefinition */
        $fieldDefinition = $this->prophesize(ManyToManyRelation::class);
        $fieldDefinition->getObjectsAllowed()->willReturn(true);
        $fieldDefinition->getClasses()->willReturn([
            [
                'classes' => 'AnySpecifiedTargetType',
            ],
        ]);

        self::assertTrue($this->handler->canHandle($fieldDefinition->reveal()));
    }

    /**
     * @test
     */
    public function canHandleShouldReturnTrueOnManyToManyObjectRelation(): void
    {
        /** @var Data\ManyToManyObjectRelation|ObjectProphecy $fieldDefinition */
        $fieldDefinition = $this->prophesize(Data\ManyToManyObjectRelation::class);
        $fieldDefinition->getObjectsAllowed()->willReturn(true);
        $fieldDefinition->getClasses()->willReturn([
            [
                'classes' => 'AnySpecifiedTargetType',
            ],
        ]);

        self::assertTrue($this->handler->canHandle($fieldDefinition->reveal()));
    }

    /**
     * @test
     */
    public function canHandleShouldReturnFalseOnNotTypedManyToManyObjectRelation(): void
    {
        /** @var Data\ManyToManyObjectRelation|ObjectProphecy $fieldDefinition */
        $fieldDefinition = $this->prophesize(Data\ManyToManyObjectRelation::class);
        $fieldDefinition->getObjectsAllowed()->willReturn(true);
        $fieldDefinition->getClasses()->willReturn([]);

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
        $fieldDefinition->getClasses()->willReturn(
            [
                ['classes' => 'TargetType'],
            ],
        );
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
            $relations['SourceType.my Targets - TargetType'],
            'SourceType',
            'TargetType',
            'sourceRole',
            null,
            1,
            7,
        );
    }
}
