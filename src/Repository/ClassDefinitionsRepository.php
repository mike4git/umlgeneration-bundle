<?php

namespace UMLGenerationBundle\Repository;

use Pimcore\Model\DataObject\ClassDefinition;

class ClassDefinitionsRepository implements ClassDefinitionsRepositoryInterface
{
    public function findDefinitions(): array
    {
        return (new ClassDefinition\Listing())->load();
    }
}
