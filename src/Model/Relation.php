<?php

namespace UMLGenerationBundle\Model;

class Relation
{
    private string $sourceType;
    private string $targetType;
    private bool $bidirectional = false;
    private bool $aggregation;
    private string $sourceRolename = '';
    private string $targetRolename = '';
    private ?int $minimum = null;
    private ?int $maximum = null;

    public function getMinimum(): ?int
    {
        return $this->minimum;
    }

    public function setMinimum(?int $minimum): Relation
    {
        $this->minimum = $minimum;

        return $this;
    }

    public function getMaximum(): ?int
    {
        return $this->maximum;
    }

    public function setMaximum(?int $maximum): Relation
    {
        $this->maximum = $maximum;

        return $this;
    }

    public function getSourceType(): string
    {
        return $this->sourceType;
    }

    public function setSourceType(string $sourceType): Relation
    {
        $this->sourceType = $sourceType;

        return $this;
    }

    public function getTargetType(): string
    {
        return $this->targetType;
    }

    public function setTargetType(string $targetType): Relation
    {
        $this->targetType = $targetType;

        return $this;
    }

    public function isBidirectional(): bool
    {
        return $this->bidirectional;
    }

    public function setBidirectional(bool $bidirectional): Relation
    {
        $this->bidirectional = $bidirectional;

        return $this;
    }

    public function isAggregation(): bool
    {
        return $this->aggregation;
    }

    public function setAggregation(bool $aggregation): Relation
    {
        $this->aggregation = $aggregation;

        return $this;
    }

    public function getSourceRolename(): string
    {
        return $this->sourceRolename;
    }

    public function setSourceRolename(string $sourceRolename): Relation
    {
        $this->sourceRolename = $sourceRolename;

        return $this;
    }

    public function getTargetRolename(): string
    {
        return $this->targetRolename;
    }

    public function setTargetRolename(string $targetRolename): Relation
    {
        $this->targetRolename = $targetRolename;

        return $this;
    }
}
