<?php
declare(strict_types=1);

namespace UMLGenerationBundle\Tests\Unit\Service;

use const false;
use PHPUnit\Framework\TestCase;
use const true;
use UMLGenerationBundle\Handler\Relation\ManyToManyRelationHandler;
use UMLGenerationBundle\Handler\Relation\ManyToOneRelationHandler;
use UMLGenerationBundle\Model\Attribute;
use UMLGenerationBundle\Model\ObjectClass;
use UMLGenerationBundle\Model\Relation;
use UMLGenerationBundle\Service\Class2UMLService;

class Class2UMLServiceTest extends TestCase
{
    private Class2UMLService $service;

    protected function setUp(): void
    {
        $this->service = new Class2UMLService(
            [
                new ManyToOneRelationHandler(),
                new ManyToManyRelationHandler(),
            ],
        );
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
            DECL=> [
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
    public function generateClassBoxForClass(int $attributeIndex, string $name, string $type, string $modifier, bool $static): void
    {
        $this->service->generateClassBox(TestClass::class);

        $expected = new ObjectClass();
        $expected->setClassName('TestClass');
        $expected->setClassId('UMLGenerationBundle\Tests\Unit\Service\TestClass');
        $expected->setStereotype('');

        $actualClassBox = array_values($this->service->getClasses())[0];
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

    public function sampleRelations()
    {
        yield 'Many to one aggregation' => [
            0, 'TestClassForRelations', 'TestClass', 'parent', false, true, 1, 1,
        ];
        yield 'nullable Many to one aggregation' => [
            1, 'TestClassForRelations', 'TestClass', 'nullableParent', false, true, 0, 1,
        ];
        yield 'Many to many aggregation' => [
            2, 'TestClassForRelations', 'TestClass', 'children', false, true, 0, null,
        ];
    }

    /**
     * @test
     * @dataProvider sampleRelations
     */
    public function generateRelationsForClasses(
        int $relationIndex,
        string $sourceType,
        string $targetType,
        string $targetRolename,
        bool $bidirectional,
        bool $aggregation,
        int $minimum,
        ?int $maximum,
    ): void {
        $this->service->generateClassBox(TestClass::class);
        $this->service->generateClassBox(TestClassForRelations::class);

        $expected = $this->createExpectedRelation(
            $sourceType,
            $targetType,
            $targetRolename,
            $bidirectional,
            $aggregation,
            $minimum,
            $maximum,
        );

        self::assertEquals(
            $expected,
            $this->service->getRelations()[$relationIndex],
        );
    }

    private function createExpectedAttribute(
        string $name,
        string $type,
        string $modifier,
        bool $static,
    ): Attribute {
        return (new Attribute())
            ->setName($name)
            ->setType($type)
            ->setModifier($modifier)
            ->setStatic($static);
    }

    private function createExpectedRelation(
        string $sourceType,
        string $targetType,
        string $targetRolename,
        bool $bidirectional,
        bool $aggregation,
        int $minimum,
        ?int $maximum,
    ): Relation {
        return (new Relation())->setSourceType($sourceType)
            ->setTargetType($targetType)
            ->setBidirectional($bidirectional)
            ->setTargetRolename($targetRolename)
            ->setAggregation($aggregation)
            ->setMinimum($minimum)
            ->setMaximum($maximum);
    }
}

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

class TestClassForRelations
{
    private TestClass $parent;
    private ?TestClass $nullableParent;

    /** @var TestClass[] */
    private array $children;
}
