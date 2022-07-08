<?php

namespace UMLGenerationBundle\Tests\Data;

class TestClassForRelations
{
    private TestClass $parent;
    private ?TestClass $nullableParent;

    /** @var TestClass[] */
    private array $children;
}
