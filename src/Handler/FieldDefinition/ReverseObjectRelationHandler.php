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

    public function handle(ClassDefinition $classDefinition, Data $fieldDefinition, array &$relations): void
    {
        $relation = (new Relation())
            ->setSourceType($fieldDefinition->getOwnerClassName())
            ->setSourceRolename($fieldDefinition->getOwnerFieldName())
            ->setTargetType($classDefinition->getName());

        $relationsKey = sprintf('%s.%s - %s', $relation->getSourceType(), $fieldDefinition->getOwnerFieldName(), $relation->getTargetType());

        $relations[$relationsKey] = $relation;
    }
}