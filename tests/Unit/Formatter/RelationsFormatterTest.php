<?php

namespace UMLGenerationBundle\Tests\Unit\Formatter;

use PHPUnit\Framework\TestCase;
use UMLGenerationBundle\Formatter\RelationsFormatter;
use UMLGenerationBundle\Model\Relation;

class RelationsFormatterTest extends TestCase
{
    private RelationsFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new RelationsFormatter();
    }

    /**
     * @return iterable<string, array<mixed>>
     */
    public function sampleRelations(): iterable
    {
        yield '1:4 - unidirektionale Aggregation' => [
            false, true, 1, 4,
            <<<EXPECTED
            MyType -> TargetType [dir=both arrowtail=odiamond label="my targets (1..4)"];
            EXPECTED,
        ];
        yield '1:n - unidirektionale Aggregation' => [
            false, true, 1, null,
            <<<EXPECTED
            MyType -> TargetType [dir=both arrowtail=odiamond label="my targets (1..n)"];
            EXPECTED,
        ];
        yield '1:1 - unidirektionale Aggregation' => [
            false, true, 1, 1,
            <<<EXPECTED
            MyType -> TargetType [dir=both arrowtail=odiamond label="my targets (1)"];
            EXPECTED,
        ];
        yield '1:4 - bidirektionale Aggregation' => [
            true, true, 1, 4,
            <<<EXPECTED
            MyType -> TargetType [dir=none arrowtail=odiamond label="my targets (1..4)"];
            EXPECTED,
        ];
        yield '1:n - bidirektionale Aggregation' => [
            true, true, 1, null,
            <<<EXPECTED
            MyType -> TargetType [dir=none arrowtail=odiamond label="my targets (1..n)"];
            EXPECTED,
        ];
        yield '1:1 - bidirektionale Aggregation' => [
            true, true, 1, 1,
            <<<EXPECTED
            MyType -> TargetType [dir=none arrowtail=odiamond label="my targets (1)"];
            EXPECTED,
        ];
    }

    /**
     * @test
     * @dataProvider sampleRelations
     */
    public function format_regular_case(
        bool $bidirectional,
        bool $aggregation,
        ?int $min,
        ?int $max,
        string $expected,
    ): void {
        $relation = new Relation();
        $relation->setSourceType('MyType')
            ->setTargetType('TargetType')
            ->setBidirectional($bidirectional)
            ->setAggregation($aggregation)
            ->setSourceRolename('my targets')
            ->setMinimum($min)
            ->setMaximum($max);

        self::assertEquals(
            $expected,
            $this->formatter->format([$relation]),
        );
    }

    /**
     * @test
     */
    public function format_empty_array_case(): void
    {
        self::assertEquals(
            '',
            $this->formatter->format([]),
        );
    }
}
