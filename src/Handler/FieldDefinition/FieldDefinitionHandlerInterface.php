<?php

namespace UMLGenerationBundle\Handler\Relation;

use Pimcore\Model\DataObject\ClassDefinition\Data;

interface FieldDefinitionHandlerInterface
{
    public function canHandle(Data $fieldDefinition): bool;

    public function handle(Data $fieldDefinition, array &$relations): void;
}
