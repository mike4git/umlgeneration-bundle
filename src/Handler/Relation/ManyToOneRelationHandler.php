<?php

namespace UMLGenerationBundle\Handler\Relation;

use ReflectionClass;
use ReflectionNamedType;
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
        $type = $property->getType();
        if ($type instanceof ReflectionNamedType) {
            $reflectionClass = new \ReflectionClass($type->getName());  //@phpstan-ignore-line
            // add Relation
            $relation = new Relation();
            $minimum = $type->allowsNull() ? 0 : 1;
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
}
