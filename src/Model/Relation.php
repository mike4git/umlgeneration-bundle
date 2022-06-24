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

    /**
     * @return int|null
     */
    public function getMinimum(): ?int
    {
        return $this->minimum;
    }

    /**
     * @param int|null $minimum
     * @return Relation
     */
    public function setMinimum(?int $minimum): Relation
    {
        $this->minimum = $minimum;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMaximum(): ?int
    {
        return $this->maximum;
    }

    /**
     * @param int|null $maximum
     * @return Relation
     */
    public function setMaximum(?int $maximum): Relation
    {
        $this->maximum = $maximum;
        return $this;
    }

    /**
     * @return string
     */
    public function getSourceType(): string
    {
        return $this->sourceType;
    }

    /**
     * @param string $sourceType
     * @return Relation
     */
    public function setSourceType(string $sourceType): Relation
    {
        $this->sourceType = $sourceType;
        return $this;
    }

    /**
     * @return string
     */
    public function getTargetType(): string
    {
        return $this->targetType;
    }

    /**
     * @param string $targetType
     * @return Relation
     */
    public function setTargetType(string $targetType): Relation
    {
        $this->targetType = $targetType;
        return $this;
    }

    /**
     * @return bool
     */
    public function isBidirectional(): bool
    {
        return $this->bidirectional;
    }

    /**
     * @param bool $bidirectional
     * @return Relation
     */
    public function setBidirectional(bool $bidirectional): Relation
    {
        $this->bidirectional = $bidirectional;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAggregation(): bool
    {
        return $this->aggregation;
    }

    /**
     * @param bool $aggregation
     * @return Relation
     */
    public function setAggregation(bool $aggregation): Relation
    {
        $this->aggregation = $aggregation;
        return $this;
    }

    /**
     * @return string
     */
    public function getSourceRolename(): string
    {
        return $this->sourceRolename;
    }

    /**
     * @param string $sourceRolename
     * @return Relation
     */
    public function setSourceRolename(string $sourceRolename): Relation
    {
        $this->sourceRolename = $sourceRolename;
        return $this;
    }

    /**
     * @return string
     */
    public function getTargetRolename(): string
    {
        return $this->targetRolename;
    }

    /**
     * @param string $targetRolename
     * @return Relation
     */
    public function setTargetRolename(string $targetRolename): Relation
    {
        $this->targetRolename = $targetRolename;
        return $this;
    }

}
