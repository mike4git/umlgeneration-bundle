<?php

namespace UMLGenerationBundle\Tests\Unit\Handler;

use PHPUnit\Framework\TestCase;
use UMLGenerationBundle\Model\Relation;

class AssertionHelper
{
    public static function assertRelations(
        TestCase $test,
        Relation $relation,
        string $relationSourceType,
        string $relationTargetType,
        ?string $relationSourceRolename,
        ?string $relationTargetRolename,
        int $relationMinimum,
        ?int $relationMaximum,
    ): void {
        $test->assertEquals($relationSourceType, $relation->getSourceType());
        $test->assertEquals($relationTargetType, $relation->getTargetType());
        $test->assertEquals($relationSourceRolename, $relation->getSourceRolename());
        $test->assertEquals($relationTargetRolename, $relation->getTargetRolename());
        $test->assertEquals($relationMinimum, $relation->getMinimum());
        $test->assertEquals($relationMaximum, $relation->getMaximum());
    }
}
