<?php

namespace UMLGenerationBundle\Handler\FieldDefinition;

use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use UMLGenerationBundle\Model\Relation;

class ManyToManyRelationHandler implements FieldDefinitionHandlerInterface
{
    public function canHandle(Data $fieldDefinition): bool
    {
        return ($fieldDefinition instanceof ClassDefinition\Data\ManyToManyRelation ||
                $fieldDefinition instanceof ClassDefinition\Data\ManyToManyObjectRelation)
            && !($fieldDefinition instanceof ClassDefinition\Data\ReverseObjectRelation)
            && $fieldDefinition->getObjectsAllowed();
    }

    /**
     * @param ClassDefinition\Data\ManyToManyRelation|ClassDefinition\Data\ManyToManyObjectRelation $fieldDefinition
     * @param Relation[] $relations
     */
    public function handle(ClassDefinition $classDefinition, Data $fieldDefinition, array &$relations): void
    {
        if ($classDefinition->getName() !== null) {
            $relation = new Relation();

            if ($fieldDefinition->getMaxItems() > 0) {
                $relation->setMaximum($fieldDefinition->getMaxItems());
            }
            // TODO Check cases where $fieldDefinition->getClasses() has more than one item
            /** @var string $class */
            $class = $fieldDefinition->getClasses()[0]['classes'];
            $relation->setSourceType($classDefinition->getName())
                ->setTargetType($class)
                ->setSourceRolename($fieldDefinition->getTitle())
                ->setMinimum($fieldDefinition->getMandatory() ? 1 : 0);

            $relationsKey = sprintf('%s.%s - %s', $relation->getSourceType(), $fieldDefinition->getName(), $relation->getTargetType());

            // if relation already exists it must be bidirectional
            if (\array_key_exists($relationsKey, $relations)) {
                $relation->setBidirectional(true);
            }
            $relations[$relationsKey] = $relation;
        }
    }
}
