<?php
declare(strict_types=1);

namespace UMLGenerationBundle\Tests\Unit\Service;

use const false;
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
     * @return iterable<mixed>
     */
    public function sampleAttributes(): iterable
    {
        yield 'public float $attribute3;' => [
            0, 'attribute3', 'float', 'public', false,
        ];
        yield 'protected int $attribute2;' => [
            1, 'attribute2', 'int', 'protected', false,
        ];
        yield 'protected int|string|null $unionTypedAttribute;' => [
            2, 'unionTypedAttribute', 'string|int|null', 'protected', false,
        ];
        yield 'private string $attribute1;' => [
            3, 'attribute1', 'string', 'private', false,
        ];
        yield 'private static $classAttribute;' => [
            4, 'classAttribute', Class2UMLService::UNTYPED, 'private', true,
        ];
        yield <<<DECL
            /** @var string[] */
            private array arrayAttribute
            DECL => [
            5, 'arrayAttribute', 'string[]', 'private', false,
        ];
        yield 'private array $arrayWithoutDocAttribute;' => [
            6, 'arrayWithoutDocAttribute', 'array', 'private', false,
        ];
        yield 'private $attributeWithoutType;' => [
            7, 'attributeWithoutType', Class2UMLService::UNTYPED, 'private', false,
        ];
    }

    /**
     * @test
     * @dataProvider sampleAttributes
     */
    public function generateClassBoxForSimpleClass(int $attributeIndex, string $name, string $type, string $modifier, bool $static): void
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

        $expectedAttribute = $this->createExpectedAttribute($name, $type, $modifier, $static);
        self::assertEquals(
            $expectedAttribute,
            $classBox->getAttributes()[$attributeIndex],
        );
    }

    /**
     * @param $name
     * @param $type
     * @param $modifier
     * @param $static
     */
    private function createExpectedAttribute(string $name, string $type, string $modifier, bool $static): Attribute
    {
        return (new Attribute())
            ->setName($name)
            ->setType($type)
            ->setModifier($modifier)
            ->setStatic($static);
    }
}

class TestKlasse
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
