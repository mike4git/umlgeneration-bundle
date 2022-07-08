<?php declare(strict_types=1);

namespace UMLGenerationBundle\Handler\Relation;

use UMLGenerationBundle\Model\Relation;

final class ClassExtendsHandler implements ClassRelationHandlerInterface
{
    public function canHandle(\ReflectionClass $reflectionClass): bool
    {
        return (bool)$reflectionClass->getParentClass();
    }

    /**
     * @param Relation[] $relations
     */
    public function handle(\ReflectionClass $reflectionClass, array &$relations): void
    {
        /** @var \ReflectionClass $parentClass */
        $parentClass = $reflectionClass->getParentClass();
        $expectedRelation = new Relation();
        $expectedRelation->setSourceType($reflectionClass->getShortName())
            ->setTargetType($parentClass->getShortName())
            ->setAggregation(false)
            ->setBidirectional(true);

        $relations[] = $expectedRelation;
    }
}
