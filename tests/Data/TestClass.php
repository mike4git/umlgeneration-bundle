<?php

namespace UMLGenerationBundle\Tests\Data;

class TestClass
{
    public float $attribute3;
    protected int $attribute2;
    protected int|string|null $unionTypedAttribute;
    private string $attribute1; // @phpstan-ignore-line
    private static $classAttribute;  // @phpstan-ignore-line
    /** @var string[] */
    private array $arrayAttribute;  // @phpstan-ignore-line
    private array $arrayWithoutDocAttribute;  // @phpstan-ignore-line
    private $attributeWithoutType;  // @phpstan-ignore-line
}
