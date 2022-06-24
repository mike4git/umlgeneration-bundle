<?php
declare(strict_types=1);

namespace UMLGenerationBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use UMLGenerationBundle\Model\Attribute;
use UMLGenerationBundle\Model\ObjectClass;
use UMLGenerationBundle\Service\Class2UMLService;

class Class2UMLServiceTest extends TestCase
{
    private Class2UMLService $service;

    protected function setUp(): void
    {
        $this->service = new Class2UMLService();
    }

    /**
     * @test
     */
    public function generateClassBoxForSimpleClass(): void
    {
        $this->service->generateClassBox(TestKlasse::class);

        $expected = new ObjectClass();
        $expected->setClassName('TestKlasse');
        $expected->setClassId('UMLGenerationBundle\Tests\Unit\Service\TestKlasse');
        $expected->setStereotype('');

        $actualClassBox = $this->service->getClasses()[0];
        self::assertEquals($expected->getClassName(), $actualClassBox->getClassName());
        self::assertEquals($expected->getClassId(), $actualClassBox->getClassId());
        self::assertEquals($expected->getStereotype(), $actualClassBox->getStereotype());

        $classBox = $actualClassBox;
        self::assertCount(4, $classBox->getAttributes());

        $expectedAttribute = new Attribute();

        $expectedAttribute->setName('attribute3');
        $expectedAttribute->setType('float');
        $expectedAttribute->setModifier('public');
        $expectedAttribute->setStatic(false);
        self::assertEquals(
            $expectedAttribute,
            $classBox->getAttributes()[0],
        );

        $expectedAttribute->setName('attribute2');
        $expectedAttribute->setType('int');
        $expectedAttribute->setModifier('protected');
        $expectedAttribute->setStatic(false);
        self::assertEquals(
            $expectedAttribute,
            $classBox->getAttributes()[1],
        );

        $expectedAttribute->setName('attribute1');
        $expectedAttribute->setType('string');
        $expectedAttribute->setModifier('private');
        $expectedAttribute->setStatic(false);
        self::assertEquals(
            $expectedAttribute,
            $classBox->getAttributes()[2],
        );

        $expectedAttribute->setName('classAttribute');
        $expectedAttribute->setType('');
        $expectedAttribute->setModifier('private');
        $expectedAttribute->setStatic(true);
        self::assertEquals(
            $expectedAttribute,
            $classBox->getAttributes()[3],
        );
    }
}

class TestKlasse
{
    public float $attribute3;
    protected int $attribute2;
    // @phpstan-ignore-next-line
    private string $attribute1;
    // @phpstan-ignore-next-line
    private static $classAttribute;
}
