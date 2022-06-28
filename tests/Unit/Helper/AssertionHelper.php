<?php

namespace UMLGenerationBundle\Tests\Unit\Helper;

use PHPUnit\Framework\TestCase;
use UMLGenerationBundle\Model\Attribute;
use UMLGenerationBundle\Model\ObjectClass;
use UMLGenerationBundle\Model\Relation;

class AssertionHelper
{
    public static function assertRelation(
        TestCase $test,
        Relation $relation,
        string $relationSourceType,
        string $relationTargetType,
        ?string $relationSourceRolename,
        ?string $relationTargetRolename,
        ?int $relationMinimum,
        ?int $relationMaximum,
    ): void {
        $test->assertEquals($relationSourceType, $relation->getSourceType());
        $test->assertEquals($relationTargetType, $relation->getTargetType());
        $test->assertEquals($relationSourceRolename, $relation->getSourceRolename());
        $test->assertEquals($relationTargetRolename, $relation->getTargetRolename());
        $test->assertEquals($relationMinimum, $relation->getMinimum());
        $test->assertEquals($relationMaximum, $relation->getMaximum());
    }

    /**
     * @param Relation[] $relations
     * @param array<string> $relationSourceTypes
     * @param array<string> $relationTargetTypes
     * @param array<string> $relationSourceRolenames
     * @param array<string> $relationTargetRolenames
     * @param array<?int> $relationMinimums
     * @param array<?int> $relationMaximums
     */
    public static function assertRelations(
        TestCase $test,
        array $relations,
        array $relationSourceTypes,
        array $relationTargetTypes,
        array $relationSourceRolenames,
        array $relationTargetRolenames,
        array $relationMinimums,
        array $relationMaximums,
    ): void {
        foreach (array_values($relations) as $key => $relation) {
            AssertionHelper::assertRelation(
                $test,
                $relation,
                $relationSourceTypes[$key],
                $relationTargetTypes[$key],
                $relationSourceRolenames[$key],
                $relationTargetRolenames[$key],
                $relationMinimums[$key],
                $relationMaximums[$key],
            );
        }
    }

    /**
     * @param ObjectClass[] $classes
     * @param array<string> $classNames
     * @param array<string> $classIds
     * @param array<string> $stereoTypes
     * @param array<int> $numbersOfAttributes
     */
    public static function assertClasses(
        TestCase $test,
        array $classes,
        array $classNames,
        array $classIds,
        array $stereoTypes,
        array $numbersOfAttributes,
    ): void {
        foreach ($classes as $key => $classDefinition) {
            $test->assertEquals($classNames[$key], $classDefinition->getClassName());
            $test->assertEquals($classIds[$key], $classDefinition->getClassId());
            $test->assertEquals($stereoTypes[$key], $classDefinition->getStereotype());
            $test->assertCount($numbersOfAttributes[$key], $classDefinition->getAttributes());
        }
    }

    /**
     * @param Attribute[] $attributes
     * @param array<string> $attributeNames
     * @param array<string> $attributeTypes
     * @param array<string> $attributeModifiers
     * @param array<string> $attributeAdditionalInfos
     */
    public static function assertAttributes(
        TestCase $test,
        array $attributes,
        array $attributeNames,
        array $attributeTypes,
        array $attributeModifiers,
        array $attributeAdditionalInfos,
    ): void {
        foreach ($attributes as $key => $attribute) {
            $test->assertEquals($attributeNames[$key], $attribute->getName());
            $test->assertEquals($attributeTypes[$key], $attribute->getType());
            $test->assertEquals($attributeModifiers[$key], $attribute->getModifier());
            $test->assertEquals($attributeAdditionalInfos[$key], $attribute->getAdditionalInfo());
        }
    }
}
