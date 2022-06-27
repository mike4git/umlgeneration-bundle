<?php

declare(strict_types=1);

namespace UMLGenerationBundle\Handler\FieldDefinition;

use Pimcore\Model\DataObject\ClassDefinition\Data;

class ReverseObjectRelationHandler implements FieldDefinitionHandlerInterface
{
    public function canHandle(Data $fieldDefinition): bool
    {
       return $fieldDefinition instanceof Data\ReverseObjectRelation;
    }

    public function handle(Data $fieldDefinition, array &$relations): void
    {
        // TODO: Implement handle() method.
    }
}