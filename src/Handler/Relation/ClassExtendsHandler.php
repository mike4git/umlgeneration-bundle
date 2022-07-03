<?php declare(strict_types=1);

namespace UMLGenerationBundle\Handler\Relation;

use UMLGenerationBundle\Model\Relation;

final class ClassExtendsHandler implements ClassRelationHandlerInterface
{
    public function canHandle(\ReflectionClass $reflection): bool
    {
        return (bool)$reflection->getParentClass();
    }

    public function handle(\ReflectionClass $reflection, array &$relations): void
    {
        $expectedRelation = new Relation();
        $expectedRelation->setSourceType($reflection->getShortName())
            ->setTargetType($reflection->getParentClass()->getShortName())
            ->setAggregation(false)
            ->setBidirectional(true);

        $relations[] = $expectedRelation;
    }
}
