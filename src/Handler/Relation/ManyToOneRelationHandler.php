<?php

namespace UMLGenerationBundle\Handler\Relation;

use ReflectionClass;
use ReflectionProperty;
use UMLGenerationBundle\Model\Relation;

class ManyToOneRelationHandler implements PropertyRelationHandlerInterface
{
    public function canHandle(ReflectionProperty $property): bool
    {
        return $property->getType() instanceof \ReflectionNamedType
            && !$property->getType()->isBuiltin();
    }

    public function handle(ReflectionProperty $property, ReflectionClass $reflection, array &$relations): void
    {
        $reflectionClass = new \ReflectionClass($property->getType()->getName());
        // add Relation
        $relation = new Relation();
        $minimum = $property->getType()->allowsNull() ? 0 : 1;
        $relation->setSourceType($reflection->getShortName())
            ->setTargetType($reflectionClass->getShortName())
            ->setTargetRolename($property->getName())
            ->setBidirectional(false)
            ->setAggregation(true)
            ->setMinimum($minimum)
            ->setMaximum(1);
        $relations[] = $relation;
    }
}
