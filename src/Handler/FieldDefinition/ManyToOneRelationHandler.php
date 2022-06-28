<?php declare(strict_types=1);

namespace UMLGenerationBundle\Handler\FieldDefinition;

use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use UMLGenerationBundle\Model\Relation;
use UMLGenerationBundle\Service\ClassDefinition2UMLService;

class ManyToOneRelationHandler implements FieldDefinitionHandlerInterface
{
    public function canHandle(Data $fieldDefinition): bool
    {
        return $fieldDefinition instanceof ClassDefinition\Data\ManyToOneRelation
            && $fieldDefinition->getObjectsAllowed();
    }

    /**
     * @param Data\ManyToOneRelation $fieldDefinition
     * @param Relation[] $relations
     */
    public function handle(ClassDefinition $classDefinition, Data $fieldDefinition, array &$relations): void
    {
        foreach ($fieldDefinition->getClasses() as $objectClassArray) {
            $relation = new Relation();
            $relation->setMaximum(1);
            $class = $objectClassArray['classes'];
            $relation->setSourceType($classDefinition->getName() ?? ClassDefinition2UMLService::UNKNOWN)
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
