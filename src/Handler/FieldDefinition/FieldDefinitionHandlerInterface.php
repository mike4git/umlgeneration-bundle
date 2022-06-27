<?php

declare(strict_types=1);

namespace UMLGenerationBundle\Handler\FieldDefinition;

use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;

interface FieldDefinitionHandlerInterface
{
    public function canHandle(Data $fieldDefinition): bool;

    public function handle(ClassDefinition $classDefinition, Data $fieldDefinition, array &$relations): void;
}
