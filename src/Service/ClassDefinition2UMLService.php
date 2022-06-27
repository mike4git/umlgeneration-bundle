<?php
declare(strict_types=1);

namespace UMLGenerationBundle\Service;

use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data\Localizedfields;
use UMLGenerationBundle\Handler\Relation\FieldDefinitionHandlerInterface;
use UMLGenerationBundle\Handler\Relation\PropertyRelationHandlerInterface;
use UMLGenerationBundle\Model\Attribute;
use UMLGenerationBundle\Model\ObjectClass;
use UMLGenerationBundle\Model\Relation;

class ClassDefinition2UMLService
{
    private const UNKNOWN = 'unknown';
    /** @var Relation[] */
    private array $relations;

    /** @var ObjectClass[] */
    private array $classes;

    /** @var FieldDefinitionHandlerInterface[] */
    private array $fieldDefinitionHandler;

    public function __construct()
    {
        $this->fieldDefinitionHandler = [];
        $this->relations = [];
        $this->classes = [];
    }

    public function generateClassBox(ClassDefinition $classDefinition): void
    {
        $class = new ObjectClass();
        $class->setClassName($classDefinition->getName() ?? self::UNKNOWN)
            ->setClassId($classDefinition->getId() ?? self::UNKNOWN)
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
                        ->setType($definition->getPhpdocReturnType() ?? self::UNKNOWN)
                        ->setModifier('protected')
                        ->setAdditionalInfo('localized');
                    $class->addAttribute($attribute);
                }
            } else {
                $attribute = new Attribute();
                $attribute->setName($key)
                    ->setModifier('protected')
                    ->setType($fieldDefinition->getPhpdocReturnType() ?? self::UNKNOWN);
                $class->addAttribute($attribute);
            }
        }

        $this->classes[] = $class;
    }

    public function generateRelations(ClassDefinition $classDefinition): void
    {
        foreach ($classDefinition->getFieldDefinitions() as $fieldDefinition) {
            foreach ($this->fieldDefinitionHandler as $handler) {
                if ($handler->canHandle($fieldDefinition)) {
                    $handler->handle($fieldDefinition, $this->relations);
                    break;
                }
            }
            if ($fieldDefinition->isRelationType()) {
                $relation = new Relation();
                $relation->setAggregation(true);

                if ($fieldDefinition instanceof ClassDefinition\Data\ReverseObjectRelation) {
                    $this->addReverseRelation($fieldDefinition, $relation, $classDefinition);
                } elseif ($this->isManyToOneObjectRelation($fieldDefinition)) {
                    $relation->setMaximum(1);
                    $this->addRelation($fieldDefinition, $relation, $classDefinition);
                } elseif ($this->isNonReverseManyToManyObjectRelation($fieldDefinition)
                ) {
                    /** @var ClassDefinition\Data\ManyToManyRelation|ClassDefinition\Data\ManyToManyObjectRelation $manyToManyRelation */
                    $manyToManyRelation = $fieldDefinition;
                    if ($manyToManyRelation->getMaxItems() > 0) {
                        $relation->setMaximum($manyToManyRelation->getMaxItems());
                    }
                    $this->addRelation($manyToManyRelation, $relation, $classDefinition);
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

    private function addRelation(mixed $fieldDefinition, Relation $relation, ClassDefinition $classDefinition): void
    {
        // TODO Check cases where $fieldDefinition->getClasses() has more than one item
        /** @var string $class */
        $class = $fieldDefinition->getClasses()[0]['classes'];
        $relation->setSourceType($classDefinition->getName() ?? self::UNKNOWN)
            ->setTargetType($class)
            ->setSourceRolename($fieldDefinition->getTitle())
            ->setMinimum($fieldDefinition->getMandatory() ? 1 : 0);

        $relationsKey = sprintf('%s.%s - %s', $relation->getSourceType(), $fieldDefinition->getName(), $relation->getTargetType());

        // if relation already exists it must be bidirectional
        if (\array_key_exists($relationsKey, $this->relations)) {
            $relation->setBidirectional(true);
        }
        $this->relations[$relationsKey] = $relation;
    }

    private function addReverseRelation(ClassDefinition\Data\ReverseObjectRelation $fieldDefinition, Relation $relation, ClassDefinition $classDefinition): void
    {
        $relation
            ->setSourceType($fieldDefinition->getOwnerClassName() ?? self::UNKNOWN)
            ->setSourceRolename($fieldDefinition->getOwnerFieldName())
            ->setTargetType($classDefinition->getName() ?? self::UNKNOWN);

        $relationsKey = sprintf('%s.%s - %s', $relation->getSourceType(), $fieldDefinition->getOwnerFieldName(), $relation->getTargetType());

        // if relation exists already merge it otherwise
        if (\array_key_exists($relationsKey, $this->relations)) {
            $relationToMerge = $this->relations[$relationsKey];
            $relationToMerge->setBidirectional(true);
        } else {
            // add new relation
            $this->relations[$relationsKey] = $relation;
        }
    }

    private function isManyToOneObjectRelation(ClassDefinition\Data $fieldDefinition): bool
    {
        return $fieldDefinition instanceof ClassDefinition\Data\ManyToOneRelation
            && $fieldDefinition->getObjectsAllowed();
    }

    private function isNonReverseManyToManyObjectRelation(ClassDefinition\Data $fieldDefinition): bool
    {
        return ($fieldDefinition instanceof ClassDefinition\Data\ManyToManyRelation ||
                $fieldDefinition instanceof ClassDefinition\Data\ManyToManyObjectRelation)
            && !($fieldDefinition instanceof ClassDefinition\Data\ReverseObjectRelation)
            && $fieldDefinition->getObjectsAllowed();
    }
}
