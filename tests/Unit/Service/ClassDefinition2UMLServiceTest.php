<?php

namespace UMLGenerationBundle\Tests\Unit\Service;

use UMLGenerationBundle\Model\Attribute;
use UMLGenerationBundle\Model\ObjectClass;
use UMLGenerationBundle\Model\Relation;
use UMLGenerationBundle\Service\ClassDefinition2UMLService;
use PHPUnit\Framework\TestCase;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data\Localizedfields;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class ClassDefinition2UMLServiceTest extends TestCase
{
    use ProphecyTrait;

    private ClassDefinition2UMLService $service;

    /** @var ClassDefinition|ObjectProphecy */
    private $classDefinition;

    protected function setUp(): void
    {
        $this->service = new ClassDefinition2UMLService();

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
        $this->assertClasses($classes,['MyType'],['type_id'],['DataObject'], [0]);
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
        $this->assertClasses(
            $objectClasses,
            ['MyType'],
            ['type_id'],
            ['DataObject'],
            [2]
        );

        $this->assertAttributes(
            $objectClasses[0]->getAttributes(),
            ['field1', 'field2'],
            ['string|null', 'int|null'],
            ['protected','protected'],
            ['','']
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
            ['classes' => 'TargetType']
        ]);
        $fieldDefinition->getMandatory()->willReturn(false);

        $this->classDefinition->getFieldDefinitions()->willReturn([
            'field1' => $fieldDefinition->reveal(),
        ]);

        $this->service->generateClassBox($this->classDefinition->reveal());

        $objectClasses = $this->service->getClasses();
        $this->assertClasses(
            $objectClasses,
            ['MyType'],
            ['type_id'],
            ['DataObject'],
            [1]
        );

        $this->assertAttributes(
            $objectClasses[0]->getAttributes(),
            ['field1'],
            ['\Pimcore\Model\DataObject\TargetType|null'],
            ['protected'],
            ['']
        );

        $this->service->generateRelations($this->classDefinition->reveal());
        $this->assertRelations(
            $this->service->getRelations(),
            ['MyType'],
            ['TargetType'],
            ['the Others'],
            [''],
            ['0'],
            ['1']
        );

    }

    public function combinationsOfFieldDefs()
    {
        yield 'Pflichtfeld und maximal 3' => [true, 3, '(1..3)'];
        yield 'Kein Pflichtfeld und maximal 3' => [false, 3, '(0..3)'];
        yield 'Pflichtfeld und unbeschränkt' => [true, null, '(1..n)'];
        yield 'Kein Pflichtfeld und unbeschränkt' => [false, null, '(0..n)'];
    }

    /**
     * @test
     * @dataProvider combinationsOfFieldDefs
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
            ['classes' => 'TargetType']
        ]);
        $fieldDefinition->getMandatory()->willReturn($mandatory);
        $fieldDefinition->getMaxItems()->willReturn($maxItems);

        $this->classDefinition->getFieldDefinitions()->willReturn([
            'field1' => $fieldDefinition->reveal(),
        ]);

        $this->service->generateClassBox($this->classDefinition->reveal());

        $objectClasses = $this->service->getClasses();
        $this->assertClasses(
            $objectClasses,
            ['MyType'],
            ['type_id'],
            ['DataObject'],
            [1]
        );

        $this->assertAttributes(
            $objectClasses[0]->getAttributes(),
            ['field1'],
            ['\Pimcore\Model\DataObject\TargetType[]'],
            ['protected'],
            ['']
        );

        $this->service->generateRelations($this->classDefinition->reveal());
        $this->assertRelations(
            $this->service->getRelations(),
            ['MyType'],
            ['TargetType'],
            ['the Others'],
            [''],
            [$mandatory ? '1': '0'],
            [$maxItems]
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
        $this->assertClasses(
            $objectClasses,
            ['MyType'],
            ['type_id'],
            ['DataObject'],
            [2]
        );

        $this->assertAttributes(
            $objectClasses[0]->getAttributes(),
            ['Field 1', 'Field 2'],
            ['string|null', 'int|null'],
            ['protected','protected'],
            ['localized','localized']
        );
    }

    /**
     * @param $name
     * @param $type
     * @return ObjectProphecy
     */
    private function createFieldDefinitionMock($name, $type): ObjectProphecy
    {
        $fieldDefinition = $this->prophesize(ClassDefinition\Data::class);
        $fieldDefinition->getName()->willReturn($name);
        $fieldDefinition->getPhpdocReturnType()->willReturn($type);
        return $fieldDefinition;
    }

    /**
     * @param ObjectClass[] $classes
     * @param array<string> $classNames
     * @param array<string> $classIds
     * @param array<string> $stereoTypes
     * @param array<int> $numbersOfAttributes
     * @return void
     */
    private function assertClasses(
        array $classes,
        array $classNames,
        array $classIds,
        array $stereoTypes,
        array $numbersOfAttributes
    ): void
    {
        foreach ($classes as $key => $classDefinition) {
            self::assertEquals($classNames[$key], $classDefinition->getClassName());
            self::assertEquals($classIds[$key], $classDefinition->getClassId());
            self::assertEquals($stereoTypes[$key], $classDefinition->getStereotype());
            self::assertCount($numbersOfAttributes[$key], $classDefinition->getAttributes());
        }
    }

    /**
     * @param Attribute[] $attributes
     * @param array<string> $attributeNames
     * @param array<string> $attributeTypes
     * @param array<string> $attributeModifiers
     * @param array<string> $attributeAdditionalInfos
     * @return void
     */
    private function assertAttributes(
        array $attributes,
        array $attributeNames,
        array $attributeTypes,
        array $attributeModifiers,
        array $attributeAdditionalInfos
    ): void
    {
        foreach ($attributes as $key => $attribute) {
            self::assertEquals($attributeNames[$key], $attribute->getName());
            self::assertEquals($attributeTypes[$key], $attribute->getType());
            self::assertEquals($attributeModifiers[$key], $attribute->getModifier());
            self::assertEquals($attributeAdditionalInfos[$key], $attribute->getAdditionalInfo());
        }
    }
    /**
     * @param Relation[] $relations
     * @param array<string> $relationSourceTypes
     * @param array<string> $relationTargetTypes
     * @param array<string> $relationSourceRolenames
     * @param array<string> $relationTargetRolenames
     * @param array<string> $relationMinimums
     * @param array<string> $relationMaximums
     * @return void
     */
    private function assertRelations(
        array $relations,
        array $relationSourceTypes,
        array $relationTargetTypes,
        array $relationSourceRolenames,
        array $relationTargetRolenames,
        array $relationMinimums,
        array $relationMaximums
    ): void
    {
        foreach (array_values($relations) as $key => $relation) {
            self::assertEquals($relationSourceTypes[$key], $relation->getSourceType());
            self::assertEquals($relationTargetTypes[$key], $relation->getTargetType());
            self::assertEquals($relationSourceRolenames[$key], $relation->getSourceRolename());
            self::assertEquals($relationTargetRolenames[$key], $relation->getTargetRolename());
            self::assertEquals($relationMinimums[$key], $relation->getMinimum());
            self::assertEquals($relationMaximums[$key], $relation->getMaximum());
        }
    }
}
