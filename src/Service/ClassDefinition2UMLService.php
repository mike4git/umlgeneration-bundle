<?php
declare(strict_types=1);

namespace UMLGenerationBundle\Service;

use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data\Localizedfields;
use UMLGenerationBundle\Handler\FieldDefinition\FieldDefinitionHandlerInterface;
use UMLGenerationBundle\Model\Attribute;
use UMLGenerationBundle\Model\ObjectClass;
use UMLGenerationBundle\Model\Relation;

class ClassDefinition2UMLService
{
    public const UNKNOWN = 'unknown';
    /** @var Relation[] */
    private array $relations;

    /** @var ObjectClass[] */
    private array $classes;

    /** @var FieldDefinitionHandlerInterface[] */
    private array $fieldDefinitionHandlers;

    /**
     * @param FieldDefinitionHandlerInterface[] $fieldDefinitionHandlers
     */
    public function __construct(
        array $fieldDefinitionHandlers,
    ) {
        $this->relations = [];
        $this->classes = [];
        $this->fieldDefinitionHandlers = $fieldDefinitionHandlers;
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
            foreach ($this->fieldDefinitionHandlers as $handler) {
                if ($handler->canHandle($fieldDefinition)) {
                    $handler->handle($classDefinition, $fieldDefinition, $this->relations);
                    break;
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
}
