<?php

namespace UMLGenerationBundle\Tests\Unit\Formatter;

use UMLGenerationBundle\Formatter\AttributeFormatter;
use UMLGenerationBundle\Formatter\ClassFormatter;
use UMLGenerationBundle\Model\ObjectClass;
use PHPUnit\Framework\TestCase;

class ClassFormatterTest extends TestCase
{
    private ClassFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new ClassFormatter(new AttributeFormatter());
    }

    public function sampleClass()
    {
        yield "Simple Class without fields" => [
            'MyType', 'my_type', 'DataObject',
            <<<EXPECTED
            MyType [
                shape=plain
                label=<
                    <table border="0" cellborder="1" cellspacing="0" cellpadding="4">
                        <tr><td>&lt;DataObject&gt;<br/><b>MyType (ID: my_type)</b></td></tr>
                        <tr><td>
                            <table border="0" cellborder="0" cellspacing="0">
                            </table>
                        </td></tr>
                    </table>
                >
            ];
            EXPECTED
        ];
    }

    /**
     * @test
     * @dataProvider sampleClass
     * @param $className
     * @param $classId
     * @param $stereotype
     * @param string $expected
     */
    public function format_regular_case(
        string $className, string $classId, string $stereotype, string $expected
    ): void
    {
        $objectClass = new ObjectClass();
        $objectClass->setClassName($className)
            ->setClassId($classId)
            ->setStereotype($stereotype);

        self::assertEquals($expected,
            $this->formatter->format($objectClass)
        );
    }
}
