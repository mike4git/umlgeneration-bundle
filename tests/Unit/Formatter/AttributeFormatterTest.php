<?php

namespace UMLGenerationBundle\Tests\Unit\Formatter;

use UMLGenerationBundle\Formatter\AttributeFormatter;
use UMLGenerationBundle\Formatter\ClassFormatter;
use UMLGenerationBundle\Model\Attribute;
use UMLGenerationBundle\Model\ObjectClass;
use UMLGenerationBundle\Model\Relation;
use PHPUnit\Framework\TestCase;

class AttributeFormatterTest extends TestCase
{
    private AttributeFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new AttributeFormatter();
    }

    public function sampleAttribute()
    {
        yield "Simple Attribute" => [
            'name', 'string|null', 'protected',null,
            <<<EXPECTED
            <tr><td># name</td><td>string|null</td></tr>
            EXPECTED
        ];
        yield "Simple localized Attribute" => [
            'name', 'string|null', 'protected','localized',
            <<<EXPECTED
            <tr><td># name</td><td>string|null (localized)</td></tr>
            EXPECTED
        ];
    }

    /**
     * @test
     * @dataProvider sampleAttribute
     * @param $name
     * @param $type
     * @param $modifier
     * @param string $expected
     */
    public function format_regular_case(
        string $name, string $type, string $modifier, ?string $additionalInfo, string $expected
    ): void
    {
        /** @var Attribute $attribute */
        $attribute = new Attribute();
        $attribute->setName($name)
            ->setType($type)
            ->setModifier($modifier);

        if ($additionalInfo) {
            $attribute->setAdditionalInfo($additionalInfo);
        }
        self::assertEquals($expected,
            $this->formatter->format($attribute)
        );
    }
}
