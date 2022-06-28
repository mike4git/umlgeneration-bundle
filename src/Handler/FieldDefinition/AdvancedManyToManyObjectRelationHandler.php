<?php

namespace UMLGenerationBundle\Handler\FieldDefinition;

use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use UMLGenerationBundle\Model\Relation;

class AdvancedManyToManyObjectRelationHandler implements FieldDefinitionHandlerInterface
{
    public function canHandle(Data $fieldDefinition): bool
    {
        return $fieldDefinition instanceof Data\AdvancedManyToManyObjectRelation
            && ($fieldDefinition->getAllowedClassId() !== null);
    }

    /**
     * @param Data\AdvancedManyToManyObjectRelation $fieldDefinition
     * @param Relation[] $relations
     */
    public function handle(ClassDefinition $classDefinition, Data $fieldDefinition, array &$relations): void
    {
        if ($classDefinition->getName() !== null) {
            /** @var string $allowedClass */
            $allowedClass = $fieldDefinition->getAllowedClassId() ?? '';
            $mappingType = sprintf('%s2%s', $classDefinition->getName(), $allowedClass);

            // break m:n relation into two 1:n relations
            $relation = new Relation();

            if ($fieldDefinition->getMaxItems() > 0) {
                $relation->setMaximum($fieldDefinition->getMaxItems());
            }

            $relation->setSourceType($classDefinition->getName())
                ->setTargetType($mappingType)
                ->setSourceRolename($fieldDefinition->getTitle())
                ->setMinimum(0);

            $relationsKey = sprintf('%s.%s - %s', $relation->getSourceType(), $fieldDefinition->getName(), $relation->getTargetType());

            $relations[$relationsKey] = $relation;

            $otherRelation = new Relation();

            $otherRelation->setSourceType($mappingType)
                ->setTargetType($allowedClass);

            $otherRelationsKey = sprintf('%s - %s', $otherRelation->getSourceType(), $otherRelation->getTargetType());

            $relations[$otherRelationsKey] = $otherRelation;
        }
    }
}
