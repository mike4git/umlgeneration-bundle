<?php

namespace UMLGenerationBundle\Model;

class Attribute
{
    private string $modifier;
    private string $type;
    private string $name;
    private ?string $additionalInfo = null;

    public function getModifier(): string
    {
        return $this->modifier;
    }

    public function setModifier(string $modifier): Attribute
    {
        $this->modifier = $modifier;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Attribute
    {
        $this->type = $type;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

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

    public function setAdditionalInfo(string $additionalInfo): Attribute
    {
        $this->additionalInfo = $additionalInfo;

        return $this;
    }
}
