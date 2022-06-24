<?php
declare(strict_types=1);

namespace UMLGenerationBundle\Model;

class Attribute
{
    private string $modifier;
    private string $type;
    private string $name;
    private bool $static;
    private string $defaultValue = '';

    private ?string $additionalInfo = null;

    public function getDefaultValue(): string
    {
        return $this->defaultValue;
    }

    public function setDefaultValue(string $defaultValue): Attribute
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

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

    public function setStatic(bool $static): Attribute
    {
        $this->static = $static;

        return $this;
    }

    public function isStatic(): bool
    {
        return $this->static;
    }
}
