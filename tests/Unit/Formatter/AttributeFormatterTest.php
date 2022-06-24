<?php

namespace UMLGenerationBundle\Tests\Unit\Formatter;

use PHPUnit\Framework\TestCase;
use UMLGenerationBundle\Formatter\AttributeFormatter;
use UMLGenerationBundle\Model\Attribute;

class AttributeFormatterTest extends TestCase
{
    private AttributeFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new AttributeFormatter();
    }

    /**
     * @return iterable<string, array<mixed>>
     */
    public function sampleAttribute(): iterable
    {
        yield 'Simple Attribute' => [
            'name', 'string|null', 'protected', null,
            <<<EXPECTED
            <tr><td># name</td><td>string|null</td></tr>
            EXPECTED,
        ];
        yield 'Simple localized Attribute' => [
            'name', 'string|null', 'protected', 'localized',
            <<<EXPECTED
            <tr><td># name</td><td>string|null (localized)</td></tr>
            EXPECTED,
        ];
    }

    /**
     * @test
     * @dataProvider sampleAttribute
     *
     * @param $name
     * @param $type
     * @param $modifier
     */
    public function format_regular_case(
        string $name,
        string $type,
        string $modifier,
        ?string $additionalInfo,
        string $expected,
    ): void {
        /** @var Attribute $attribute */
        $attribute = new Attribute();
        $attribute->setName($name)
            ->setType($type)
            ->setModifier($modifier);

        if ($additionalInfo) {
            $attribute->setAdditionalInfo($additionalInfo);
        }
        self::assertEquals(
            $expected,
            $this->formatter->format($attribute),
        );
    }
}
