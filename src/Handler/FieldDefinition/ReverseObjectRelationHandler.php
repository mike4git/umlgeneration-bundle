<?php

declare(strict_types=1);

namespace UMLGenerationBundle\Handler\FieldDefinition;

use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use UMLGenerationBundle\Model\Relation;

class ReverseObjectRelationHandler implements FieldDefinitionHandlerInterface
{
    public function canHandle(Data $fieldDefinition): bool
    {
        return $fieldDefinition instanceof Data\ReverseObjectRelation;
    }

    /**
     * @param Data\ReverseObjectRelation $fieldDefinition
     * @param Relation[] $relations
     */
    public function handle(ClassDefinition $classDefinition, Data $fieldDefinition, array &$relations): void
    {
        if ($classDefinition->getName() !== null && $fieldDefinition->getOwnerClassName() !== null) {
            $relation = (new Relation())
                ->setSourceType($fieldDefinition->getOwnerClassName())
                ->setSourceRolename($fieldDefinition->getOwnerFieldName())
                ->setTargetType($classDefinition->getName());

            $relationsKey = sprintf('%s.%s - %s', $relation->getSourceType(), $fieldDefinition->getOwnerFieldName(), $relation->getTargetType());

            // if relation exists already merge it otherwise
            if (\array_key_exists($relationsKey, $relations)) {
                $relationToMerge = $relations[$relationsKey];
                $relationToMerge->setBidirectional(true);
            } else {
                $relations[$relationsKey] = $relation;
            }
        }
    }
}
