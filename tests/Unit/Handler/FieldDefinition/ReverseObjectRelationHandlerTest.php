<?php

declare(strict_types=1);

namespace UMLGenerationBundle\Tests\Unit\Handler\FieldDefinition;

use PHPUnit\Framework\TestCase;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\Data\ReverseObjectRelation;
use Prophecy\PhpUnit\ProphecyTrait;
use UMLGenerationBundle\Handler\FieldDefinition\ReverseObjectRelationHandler;

class ReverseObjectRelationHandlerTest extends TestCase
{
    use ProphecyTrait;

    private ReverseObjectRelationHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new ReverseObjectRelationHandler();
    }

    /**
     * @test
     */
    public function canHandleShouldReturnTrueOnReverseObjectRelation(): void
    {
        $fieldDefinition = $this->prophesize(ReverseObjectRelation::class);

        self::assertTrue($this->handler->canHandle($fieldDefinition->reveal()));
    }

    /**
     * @test
     */
    public function canHandleShouldReturnFalseOnNonReverseObjectRelation(): void
    {
        $fieldDefinition = $this->prophesize(Data::class);

        self::assertFalse($this->handler->canHandle($fieldDefinition->reveal()));
    }

    /**
     * @test
     */
    public function handleShouldAddRelation(): void
    {
        $fieldDefinition = $this->prophesize(ReverseObjectRelation::class);
        $relations = [];


        $this->handler->handle($fieldDefinition->reveal(), $relations);

        self::assertCount(1, $relations);
     }
}