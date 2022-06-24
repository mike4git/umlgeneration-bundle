<?php
declare(strict_types=1);

namespace UMLGenerationBundle\Service;

use phpDocumentor\Reflection\Types\ClassString;
use UMLGenerationBundle\Model\Attribute;
use UMLGenerationBundle\Model\ObjectClass;

class Class2UMLService
{
    /** @var ObjectClass[] */
    private array $classes = [];

    /** @var array|string[] */
    private array $mapModifiersToText = [
        \ReflectionProperty::IS_PRIVATE => 'private',
        \ReflectionProperty::IS_PROTECTED => 'protected',
        \ReflectionProperty::IS_PUBLIC => 'public',
        \ReflectionProperty::IS_STATIC => 'static',
    ];

    public function generateClassBox(string $class): void
    {
        /** @var ClassString $class */
        $reflection = new \ReflectionClass($class);
        $classBox = new ObjectClass();
        $classBox->setClassName($reflection->getShortName());
        $classBox->setClassId($reflection->getName());
        $classBox->setStereotype('');

        $properties = $reflection->getProperties(
            \ReflectionProperty::IS_PUBLIC
            | \ReflectionProperty::IS_PROTECTED
            | \ReflectionProperty::IS_PRIVATE,
        );

        foreach ($properties as $property) {
            $boxAttribute = new Attribute();
            $boxAttribute->setName($property->getName())
                ->setType($property->getType()?->getName() ?? '')
                ->setStatic($property->isStatic())
                ->setModifier($this->mapModifiersToText[$property->getModifiers() % 8]);
            $classBox->addAttribute($boxAttribute);
        }
        $this->classes[] = $classBox;
    }

    /**
     * @return ObjectClass[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }
}
