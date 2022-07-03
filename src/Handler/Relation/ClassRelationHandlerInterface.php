<?php

namespace UMLGenerationBundle\Handler\Relation;

use ReflectionClass;
use UMLGenerationBundle\Model\Relation;

interface ClassRelationHandlerInterface
{
    public function canHandle(ReflectionClass $reflectionClass): bool;

    /**
     * @param Relation[] $relations
     */
    public function handle(ReflectionClass $reflectionClass, array &$relations): void;
}
