<?php
declare(strict_types=1);

namespace UMLGenerationBundle\Service;

use phpDocumentor\Reflection\Types\ClassString;
use UMLGenerationBundle\Model\Attribute;
use UMLGenerationBundle\Model\ObjectClass;

class Class2UMLService
{
    public const UNTYPED = 'untyped';
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
                ->setType($this->determineType($property))
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

    private function determineType(\ReflectionProperty $property): string
    {
        if ($property->hasType()) {
            if ($property->getType() instanceof \ReflectionNamedType) {
                $declaredType = $property->getType()->getName();
                if ($declaredType === 'array') {
                    $matches = [];
                    if ($property->getDocComment() && preg_match("/@var[\s]*(\S*)/", $property->getDocComment(), $matches)) {
                        return $matches[1];
                    }

                    return $declaredType;
                }

                return $declaredType;
            }
            if ($property->getType() instanceof \ReflectionUnionType) {
                return implode('|', $property->getType()->getTypes());
            }
        }

        return self::UNTYPED;
    }
}
