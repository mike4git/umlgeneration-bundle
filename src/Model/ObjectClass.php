<?php
declare(strict_types=1);

namespace UMLGenerationBundle\Model;

class ObjectClass
{
    private string $className;
    private string $classId;
    private string $stereotype;
    /** @var Attribute[] */
    private array $attributes = [];

    public function getClassName(): string
    {
        return $this->className;
    }

    public function setClassName(string $className): ObjectClass
    {
        $this->className = $className;

        return $this;
    }

    public function getClassId(): string
    {
        return $this->classId;
    }

    public function setClassId(string $classId): ObjectClass
    {
        $this->classId = $classId;

        return $this;
    }

    public function getStereotype(): string
    {
        return $this->stereotype;
    }

    public function setStereotype(string $stereotype): ObjectClass
    {
        $this->stereotype = $stereotype;

        return $this;
    }

    /**
     * @return Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function addAttribute(Attribute $attribute): ObjectClass
    {
        $this->attributes[] = $attribute;

        return $this;
    }

    public function setBaseClass(): ObjectClass
    {
        return $this;
    }
}
