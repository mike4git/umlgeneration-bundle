<?php declare(strict_types=1);

namespace UMLGenerationBundle\Tests\Unit\Handler\FieldDefinition;

use PHPUnit\Framework\TestCase;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\Data\ManyToOneRelation;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use UMLGenerationBundle\Handler\FieldDefinition\ManyToOneGenericRelationHandler;
use UMLGenerationBundle\Model\Relation;
use UMLGenerationBundle\Tests\Unit\Helper\AssertionHelper;

class ManyToOneGenericRelationHandlerTest extends TestCase
{
    use ProphecyTrait;

    private ManyToOneGenericRelationHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new ManyToOneGenericRelationHandler();
    }

    /**
     * @test
     */
    public function canHandleShouldReturnTrueOnManyToOneRelation(): void
    {
        /** @var ManyToOneRelation|ObjectProphecy $fieldDefinition */
        $fieldDefinition = $this->prophesize(ManyToOneRelation::class);
        $fieldDefinition->getObjectsAllowed()->willReturn(true);
        $fieldDefinition->getClasses()->willReturn([]);

        self::assertTrue($this->handler->canHandle($fieldDefinition->reveal()));
    }

    /**
     * @test
     */
    public function canHandleShouldReturnFalseOnManyToOneNonGenericRelation(): void
    {
        /** @var ManyToOneRelation|ObjectProphecy $fieldDefinition */
        $fieldDefinition = $this->prophesize(ManyToOneRelation::class);
        $fieldDefinition->getObjectsAllowed()->willReturn(true);
        $fieldDefinition->getClasses()->willReturn([['classes' => 'AnyType']]);

        self::assertFalse($this->handler->canHandle($fieldDefinition->reveal()));
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
        $fieldDefinition->getClasses()->willReturn([]);
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
        AssertionHelper::assertRelation(
            $this,
            $relations['SourceType.my Targets - Pimcore\Model\DataObject'],
            'SourceType',
            'Pimcore\Model\DataObject',
            'sourceRole',
            null,
            1,
            1,
        );
    }
}
