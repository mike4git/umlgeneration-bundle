<?php
declare(strict_types=1);

namespace UMLGenerationBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data\Localizedfields;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use UMLGenerationBundle\Handler\FieldDefinition\ManyToManyRelationHandler;
use UMLGenerationBundle\Handler\FieldDefinition\ReverseObjectRelationHandler;
use UMLGenerationBundle\Service\ClassDefinition2UMLService;
use UMLGenerationBundle\Tests\Unit\Helper\AssertionHelper;

class ClassDefinition2UMLServiceTest extends TestCase
{
    use ProphecyTrait;

    private ClassDefinition2UMLService $service;

    /** @var ClassDefinition|ObjectProphecy */
    private $classDefinition;

    protected function setUp(): void
    {
        $this->service = new ClassDefinition2UMLService(
            [
                new ManyToManyRelationHandler(),
                new ManyToManyRelationHandler(),
                new ReverseObjectRelationHandler(),
            ],
        );

        $this->classDefinition = $this->prophesize(ClassDefinition::class);
        $this->classDefinition->getName()->willReturn('MyType');
        $this->classDefinition->getId()->willReturn('type_id');
    }

    /**
     * @test
     */
    public function generateClassBox_classdefinition_without_fields(): void
    {
        $this->classDefinition->getFieldDefinitions()->willReturn([]);

        $this->service->generateClassBox($this->classDefinition->reveal());

        $classes = $this->service->getClasses();
        AssertionHelper::assertClasses($this, $classes, ['MyType'], ['type_id'], ['DataObject'], [0]);
    }

    /**
     * @test
     */
    public function generateClassBox_classdefinition_with_multiple_non_localized_fields(): void
    {
        $fieldDefinition1 = $this->createFieldDefinitionMock('Field 1', 'string|null');
        $fieldDefinition2 = $this->createFieldDefinitionMock('Field 2', 'int|null');

        $this->classDefinition->getFieldDefinitions()->willReturn([
            'field1' => $fieldDefinition1->reveal(),
            'field2' => $fieldDefinition2->reveal(),
        ]);

        $this->service->generateClassBox($this->classDefinition->reveal());

        $objectClasses = $this->service->getClasses();
        AssertionHelper::assertClasses(
            $this,
            $objectClasses,
            ['MyType'],
            ['type_id'],
            ['DataObject'],
            [2],
        );

        AssertionHelper::assertAttributes(
            $this,
            $objectClasses[0]->getAttributes(),
            ['field1', 'field2'],
            ['string|null', 'int|null'],
            ['protected', 'protected'],
            ['', ''],
        );
    }

    /**
     * @test
     */
    public function generateClassBox_classdefinition_with_manytoone_relation_field(): void
    {
        /** @var ClassDefinition\Data\ManyToOneRelation|ObjectProphecy $fieldDefinition */
        $fieldDefinition = $this->prophesize(ClassDefinition\Data\ManyToOneRelation::class);
        $fieldDefinition->getName()->willReturn('myTarget');
        $fieldDefinition->isRelationType()->willReturn(true);
        $fieldDefinition->getObjectsAllowed()->willReturn(true);
        $fieldDefinition->getTitle()->willReturn('the Others');
        $fieldDefinition->getPhpdocReturnType()->willReturn('\Pimcore\Model\DataObject\TargetType|null');
        $fieldDefinition->getClasses()->willReturn([
            ['classes' => 'TargetType'],
        ]);
        $fieldDefinition->getMandatory()->willReturn(false);

        $this->classDefinition->getFieldDefinitions()->willReturn([
            'field1' => $fieldDefinition->reveal(),
        ]);

        $this->service->generateClassBox($this->classDefinition->reveal());

        $objectClasses = $this->service->getClasses();
        AssertionHelper::assertClasses(
            $this,
            $objectClasses,
            ['MyType'],
            ['type_id'],
            ['DataObject'],
            [1],
        );

        AssertionHelper::assertAttributes(
            $this,
            $objectClasses[0]->getAttributes(),
            ['field1'],
            ['\Pimcore\Model\DataObject\TargetType|null'],
            ['protected'],
            [''],
        );

        $this->service->generateRelations($this->classDefinition->reveal());
        AssertionHelper::assertRelations(
            $this,
            $this->service->getRelations(),
            ['MyType'],
            ['TargetType'],
            ['the Others'],
            [''],
            ['0'],
            ['1'],
        );
    }

    /**
     * @return iterable<string, array<mixed>>
     */
    public function combinationsOfFieldDefs(): iterable
    {
        yield 'Pflichtfeld und maximal 3' => [true, 3, '(1..3)'];
        yield 'Kein Pflichtfeld und maximal 3' => [false, 3, '(0..3)'];
        yield 'Pflichtfeld und unbeschränkt' => [true, null, '(1..n)'];
        yield 'Kein Pflichtfeld und unbeschränkt' => [false, null, '(0..n)'];
    }

    /**
     * @test
     * @dataProvider combinationsOfFieldDefs
     *
     * @param $mandatory
     */
    public function generateClassBox_classdefinition_with_manytomany_relation_field(bool $mandatory, ?int $maxItems, string $cardinality): void
    {
        /** @var ClassDefinition\Data\ManyToOneRelation|ObjectProphecy $fieldDefinition */
        $fieldDefinition = $this->prophesize(ClassDefinition\Data\ManyToManyRelation::class);
        $fieldDefinition->getName()->willReturn('myTargets');
        $fieldDefinition->isRelationType()->willReturn(true);
        $fieldDefinition->getObjectsAllowed()->willReturn(true);
        $fieldDefinition->getTitle()->willReturn('the Others');
        $fieldDefinition->getPhpdocReturnType()->willReturn('\Pimcore\Model\DataObject\TargetType[]');
        $fieldDefinition->getClasses()->willReturn([
            ['classes' => 'TargetType'],
        ]);
        $fieldDefinition->getMandatory()->willReturn($mandatory);
        $fieldDefinition->getMaxItems()->willReturn($maxItems);

        $this->classDefinition->getFieldDefinitions()->willReturn([
            'field1' => $fieldDefinition->reveal(),
        ]);

        $this->service->generateClassBox($this->classDefinition->reveal());

        $objectClasses = $this->service->getClasses();
        AssertionHelper::assertClasses(
            $this,
            $objectClasses,
            ['MyType'],
            ['type_id'],
            ['DataObject'],
            [1],
        );

        AssertionHelper::assertAttributes(
            $this,
            $objectClasses[0]->getAttributes(),
            ['field1'],
            ['\Pimcore\Model\DataObject\TargetType[]'],
            ['protected'],
            [''],
        );

        $this->service->generateRelations($this->classDefinition->reveal());
        AssertionHelper::assertRelations(
            $this,
            $this->service->getRelations(),
            ['MyType'],
            ['TargetType'],
            ['the Others'],
            [''],
            [$mandatory ? 1 : 0],
            [$maxItems],
        );
    }

    /**
     * @test
     */
    public function generateClassBox_classdefinition_with_localized_fields(): void
    {
        $fieldDefinition = $this->prophesize(Localizedfields::class);
        $fieldDefinition1 = $this->createFieldDefinitionMock('Field 1', 'string|null');
        $fieldDefinition2 = $this->createFieldDefinitionMock('Field 2', 'int|null');
        $fieldDefinition->getChildren()->willReturn([$fieldDefinition1->reveal(), $fieldDefinition2->reveal()]);

        $this->classDefinition->getFieldDefinitions()->willReturn([
            'localizedfields' => $fieldDefinition->reveal(),
        ]);

        $this->service->generateClassBox($this->classDefinition->reveal());

        $objectClasses = $this->service->getClasses();
        AssertionHelper::assertClasses(
            $this,
            $objectClasses,
            ['MyType'],
            ['type_id'],
            ['DataObject'],
            [2],
        );

        AssertionHelper::assertAttributes(
            $this,
            $objectClasses[0]->getAttributes(),
            ['Field 1', 'Field 2'],
            ['string|null', 'int|null'],
            ['protected', 'protected'],
            ['localized', 'localized'],
        );
    }

    private function createFieldDefinitionMock(string $name, string $type): ObjectProphecy
    {
        $fieldDefinition = $this->prophesize(ClassDefinition\Data::class);
        $fieldDefinition->getName()->willReturn($name);
        $fieldDefinition->getPhpdocReturnType()->willReturn($type);

        return $fieldDefinition;
    }
}
