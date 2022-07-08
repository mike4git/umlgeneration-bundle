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
        $result = false;
        $declaredPropertyType = $property->getType();
        if ($declaredPropertyType instanceof \ReflectionNamedType
            && ($declaredPropertyType->getName() === 'array')) {
            /** @var class-string<mixed> $docTypeAsString */
            $docTypeAsString = substr($this->determineType($property), 0, -\strlen('[]'));

            if (!\in_array($docTypeAsString, ['string', 'int', 'bool'], true)) {
                // check further conditions
                return true;
            }
        }

        return $result;
    }

    public function handle(ReflectionProperty $property, ReflectionClass $reflection, array &$relations): void
    {
        /** @var class-string<mixed> $docTypeAsString */
        $docTypeAsString = substr($this->determineType($property), 0, -\strlen('[]'));

        if (!\in_array($docTypeAsString, ['string', 'int', 'bool'], true)) {
            try {
                $reflectionClass = new \ReflectionClass($docTypeAsString);
            } catch (\ReflectionException) { //@phpstan-ignore-line
                try {
                    $reflectionClass = new \ReflectionClass('UMLGenerationBundle\\Tests\\Data\\' . $docTypeAsString);
                } catch (\ReflectionException $e) {
                    $reflectionClass = null;
                }
            }

            if ($reflectionClass) { //@phpstan-ignore-line
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
