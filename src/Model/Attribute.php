<?php

namespace UMLGenerationBundle\Model;

class Attribute
{
    private string $modifier;
    private string $type;
    private string $name;
    private ?string $additionalInfo = null;

    /**
     * @return string
     */
    public function getModifier(): string
    {
        return $this->modifier;
    }

    /**
     * @param string $modifier
     * @return Attribute
     */
    public function setModifier(string $modifier): Attribute
    {
        $this->modifier = $modifier;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Attribute
     */
    public function setType(string $type): Attribute
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Attribute
     */
    public function setName(string $name): Attribute
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return ?string
     */
    public function getAdditionalInfo(): ?string
    {
        return $this->additionalInfo;
    }

    /**
     * @param string $additionalInfo
     * @return Attribute
     */
    public function setAdditionalInfo(string $additionalInfo): Attribute
    {
        $this->additionalInfo = $additionalInfo;
        return $this;
    }

}
