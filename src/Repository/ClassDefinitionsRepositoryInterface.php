<?php

namespace UMLGenerationBundle\Repository;

use Pimcore\Model\DataObject\ClassDefinition;

interface ClassDefinitionsRepositoryInterface
{
    /**
     * @return ClassDefinition[]
     */
    public function findDefinitions(): array;
}
