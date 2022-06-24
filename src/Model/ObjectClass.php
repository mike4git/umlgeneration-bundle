<?php

namespace UMLGenerationBundle\Model;

class ObjectClass
{
    private string $className;
    private string $classId;
    private string $stereotype;
    /** @var Attribute[] */
    private array $attributes = [];

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param string $className
     * @return ObjectClass
     */
    public function setClassName(string $className): ObjectClass
    {
        $this->className = $className;
        return $this;
    }

    /**
     * @return string
     */
    public function getClassId(): string
    {
        return $this->classId;
    }

    /**
     * @param string $classId
     * @return ObjectClass
     */
    public function setClassId(string $classId): ObjectClass
    {
        $this->classId = $classId;
        return $this;
    }

    /**
     * @return string
     */
    public function getStereotype(): string
    {
        return $this->stereotype;
    }

    /**
     * @param string $stereotype
     * @return ObjectClass
     */
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

    /**
     * @param Attribute $attribute
     * @return ObjectClass
     */
    public function addAttribute(Attribute $attribute): ObjectClass
    {
        $this->attributes[] = $attribute;
        return $this;
    }

}
