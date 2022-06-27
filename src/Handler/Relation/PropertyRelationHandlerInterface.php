<?php

namespace UMLGenerationBundle\Handler\Relation;

use ReflectionClass;
use ReflectionProperty;
use UMLGenerationBundle\Model\Relation;

interface PropertyRelationHandlerInterface
{
    public function canHandle(ReflectionProperty $property): bool;

    /**
     * @param Relation[] $relations
     */
    public function handle(ReflectionProperty $property, ReflectionClass $reflection, array &$relations): void;
}
