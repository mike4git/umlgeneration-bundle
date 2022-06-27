<?php

namespace UMLGenerationBundle\Handler\Relation;

use ReflectionClass;
use ReflectionProperty;
use UMLGenerationBundle\Model\Relation;
use UMLGenerationBundle\Service\Class2UMLService;

class ManyToManyRelationHandler implements PropertyRelationHandlerInterface
{
    public function canHandle(ReflectionProperty $property): bool
    {
        return $property->getType() instanceof \ReflectionNamedType
            && ($property->getType()->getName() === 'array');
    }

    public function handle(ReflectionProperty $property, ReflectionClass $reflection, array &$relations): void
    {
        $docTypeAsString = substr($this->determineType($property), 0, -\strlen('[]'));

        if (!\in_array($docTypeAsString, ['string', 'int', 'bool'], true)) {
            try {
                $reflectionClass = new \ReflectionClass($docTypeAsString);
            } catch (\ReflectionException) {
                try {
                    $reflectionClass = new \ReflectionClass('UMLGenerationBundle\\Tests\\Unit\\Service\\' . $docTypeAsString);
                } catch (\ReflectionException $e) {
                    $reflectionClass = null;
                }
            }

            if ($reflectionClass) {
                // add Relation
                $relation = new Relation();
                $minimum = 0;
                $relation->setSourceType($reflection->getShortName())
                    ->setTargetType($reflectionClass->getShortName())
                    ->setTargetRolename($property->getName())
                    ->setBidirectional(false)
                    ->setAggregation(true)
                    ->setMinimum($minimum)
                    ->setMaximum(null);
                $relations[] = $relation;
            }
        }
    }

    private function determineType(\ReflectionProperty $property): string
    {
        if ($property->hasType()) {
            if ($property->getType() instanceof \ReflectionNamedType) {
                $declaredType = $property->getType()->getName();
                if ($declaredType === 'array') {
                    $matches = [];
                    if ($property->getDocComment() && preg_match("/@var[\s]*(\S*)\[\]/", $property->getDocComment(), $matches)) {
                        return $matches[1] . '[]';
                    }

                    return $declaredType;
                }

                return $declaredType;
            }
            if ($property->getType() instanceof \ReflectionUnionType) {
                return implode('|', $property->getType()->getTypes());
            }
        }

        return Class2UMLService::UNTYPED;
    }
}
