<?php

namespace UMLGenerationBundle\Service;

use UMLGenerationBundle\Model\Attribute;
use UMLGenerationBundle\Model\ObjectClass;
use UMLGenerationBundle\Model\Relation;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data\Localizedfields;

class ClassDefinition2UMLService
{
    /** @var Relation[] */
    private array $relations;

    /** @var ObjectClass[] */
    private array $classes;

    public function __construct()
    {
        $this->relations = [];
        $this->classes = [];
    }

    public function generateClassBox(ClassDefinition $classDefinition): void
    {
        $class = new ObjectClass();
        $class->setClassName($classDefinition->getName())
            ->setClassId($classDefinition->getId())
            ->setStereotype('DataObject');

        $fieldDefinitions = $classDefinition->getFieldDefinitions();

        /**
         * @var string $key
         * @var ClassDefinition\Data $fieldDefinition
         */
        foreach ($fieldDefinitions as $key => $fieldDefinition) {
            if ($key === 'localizedfields') {
                /** @var Localizedfields $locFieldDef */
                $locFieldDef = $fieldDefinition;
                /** @var ClassDefinition\Data $definition */
                foreach ($locFieldDef->getChildren() as $definition) {
                    $attribute = new Attribute();
                    $attribute->setName($definition->getName())
                        ->setType($definition->getPhpdocReturnType())
                        ->setModifier('protected')
                        ->setAdditionalInfo('localized');
                    $class->addAttribute($attribute);
                }
            } else {
                $attribute = new Attribute();
                $attribute->setName($key)
                    ->setModifier('protected')
                    ->setType($fieldDefinition->getPhpdocReturnType());
                $class->addAttribute($attribute);
            }
        }

        $this->classes[] = $class;
    }

    public function generateRelations(ClassDefinition $classDefinition): void
    {
        foreach ($classDefinition->getFieldDefinitions() as $fieldDefinition) {
            if ($fieldDefinition->isRelationType()) {
                $relation = new Relation();
                $relation->setAggregation(true);

                if ($this->isReverseObjectRelation($fieldDefinition)) {
                    $this->addReverseRelation($fieldDefinition, $relation, $classDefinition);
                } else if ($this->isManyToOneObjectRelation($fieldDefinition)) {
                    $relation->setMaximum(1);
                    $this->addRelation($fieldDefinition, $relation, $classDefinition);
                } else if ($this->isNonReverseManyToManyObjectRelation($fieldDefinition)
                ) {
                    if ($fieldDefinition->getMaxItems() > 0) {
                        $relation->setMaximum($fieldDefinition->getMaxItems());
                    }
                    $this->addRelation($fieldDefinition, $relation, $classDefinition);
                }
            }
        }
    }

    /**
     * @return Relation[]
     */
    public function getRelations(): array
    {
        return $this->relations;
    }

    /**
     * @return ObjectClass[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * @param mixed $fieldDefinition
     * @param Relation $relation
     * @param ClassDefinition $classDefinition
     * @return void
     */
    private function addRelation(mixed $fieldDefinition, Relation $relation, ClassDefinition $classDefinition): void
    {
        // TODO Check cases where $fieldDefinition->getClasses() has more than one item
        /** @var string $class */
        $class = $fieldDefinition->getClasses()[0]['classes'];
        $relation->setSourceType($classDefinition->getName())
            ->setTargetType($class)
            ->setSourceRolename($fieldDefinition->getTitle())
            ->setMinimum($fieldDefinition->getMandatory() ? 1 : 0);

        $relationsKey = sprintf("%s.%s - %s", $relation->getSourceType(), $fieldDefinition->getName(), $relation->getTargetType());

        // if relation already exists it must be bidirectional
        if (array_key_exists($relationsKey, $this->relations)) {
            $relation->setBidirectional(true);
        }
        $this->relations[$relationsKey] = $relation;
    }

    /**
     * @param ClassDefinition\Data\ReverseObjectRelation $fieldDefinition
     * @param Relation $relation
     * @param ClassDefinition $classDefinition
     * @return void
     */
    private function addReverseRelation(ClassDefinition\Data\ReverseObjectRelation $fieldDefinition, Relation $relation, ClassDefinition $classDefinition): void
    {
        $relation->setSourceType($fieldDefinition->getOwnerClassName())
            ->setSourceRolename($fieldDefinition->getOwnerFieldName())
            ->setTargetType($classDefinition->getName());

        $relationsKey = sprintf("%s.%s - %s", $relation->getSourceType(), $fieldDefinition->getOwnerFieldName(), $relation->getTargetType());

        // if relation exists already merge it otherwise
        if (array_key_exists($relationsKey, $this->relations)) {
            $relationToMerge = $this->relations[$relationsKey];
            $relationToMerge->setBidirectional(true);
        } else {
            // add new relation
            $this->relations[$relationsKey] = $relation;
        }

    }

    /**
     * @param ClassDefinition\Data $fieldDefinition
     * @return bool
     */
    private function isManyToOneObjectRelation(ClassDefinition\Data $fieldDefinition): bool
    {
        return $fieldDefinition instanceof ClassDefinition\Data\ManyToOneRelation
            && $fieldDefinition->getObjectsAllowed();
    }

    /**
     * @param ClassDefinition\Data $fieldDefinition
     * @return bool
     */
    private function isNonReverseManyToManyObjectRelation(ClassDefinition\Data $fieldDefinition): bool
    {
        return ($fieldDefinition instanceof ClassDefinition\Data\ManyToManyRelation ||
                $fieldDefinition instanceof ClassDefinition\Data\ManyToManyObjectRelation)
            && !($fieldDefinition instanceof ClassDefinition\Data\ReverseObjectRelation)
            && $fieldDefinition->getObjectsAllowed();
    }

    /**
     * @param ClassDefinition\Data $fieldDefinition
     * @return bool
     */
    private function isReverseObjectRelation(ClassDefinition\Data $fieldDefinition): bool
    {
        return $fieldDefinition instanceof ClassDefinition\Data\ReverseObjectRelation;
    }
}
