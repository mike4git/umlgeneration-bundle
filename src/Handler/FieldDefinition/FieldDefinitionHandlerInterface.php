<?php

declare(strict_types=1);

namespace UMLGenerationBundle\Handler\FieldDefinition;

use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use UMLGenerationBundle\Model\Relation;

interface FieldDefinitionHandlerInterface
{
    public function canHandle(Data $fieldDefinition): bool;

    /**
     * @param Relation[] $relations
     */
    public function handle(ClassDefinition $classDefinition, Data $fieldDefinition, array &$relations): void;
}
