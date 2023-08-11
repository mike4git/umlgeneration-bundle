<?php
declare(strict_types=1);

namespace Tests\Unit\UMLGenerationBundle\Formatter;

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
        yield 'Simple static Attribute' => [
            'name', 'string|null', 'protected', true, '', null,
            <<<EXPECTED
            <tr><td style="text-decoration: underline"># name</td><td>string|null</td></tr>
            EXPECTED,
        ];
        yield 'Simple Attribute' => [
            'name', 'string|null', 'protected', false, '', null,
            <<<EXPECTED
            <tr><td># name</td><td>string|null</td></tr>
            EXPECTED,
        ];
        yield 'Simple private Attribute' => [
            'name', 'string|null', 'private', false, '', null,
            <<<EXPECTED
            <tr><td>- name</td><td>string|null</td></tr>
            EXPECTED,
        ];
        yield 'Simple public Attribute' => [
            'name', 'string|null', 'public', false, '', null,
            <<<EXPECTED
            <tr><td>+ name</td><td>string|null</td></tr>
            EXPECTED,
        ];
        yield 'Simple localized Attribute' => [
            'name', 'string|null', 'protected', false, '', 'localized',
            <<<EXPECTED
            <tr><td># name</td><td>string|null (localized)</td></tr>
            EXPECTED,
        ];
        yield 'Simple Attribute with default value' => [
            'name', 'string|null', 'protected', false, '"Hallo Welt!"', null,
            <<<EXPECTED
            <tr><td># name</td><td>string|null = "Hallo Welt!"</td></tr>
            EXPECTED,
        ];
    }

    /**
     * @test
     *
     * @dataProvider sampleAttribute
     */
    public function format_regular_case(
        string $name,
        string $type,
        string $modifier,
        bool $static,
        string $defaultValue,
        ?string $additionalInfo,
        string $expected,
    ): void {
        /** @var Attribute $attribute */
        $attribute = new Attribute();
        $attribute->setName($name)
            ->setType($type)
            ->setModifier($modifier)
            ->setStatic($static)
            ->setDefaultValue($defaultValue)
        ;

        if ($additionalInfo) {
            $attribute->setAdditionalInfo($additionalInfo);
        }
        self::assertEquals(
            $expected,
            $this->formatter->format($attribute),
        );
    }
}
