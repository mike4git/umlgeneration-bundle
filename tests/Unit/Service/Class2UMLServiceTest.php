<?php

namespace UMLGenerationBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use UMLGenerationBundle\Model\ObjectClass;

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

        self::assertEquals(
            [
                $expected
            ],
            $this->service->getClasses()
        );
    }
}

class TestKlasse
{

}
