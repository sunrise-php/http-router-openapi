<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Schema;
use Sunrise\Http\Router\OpenApi\AbstractAnnotation;
use Sunrise\Http\Router\OpenApi\AbstractAnnotationReference;
use Sunrise\Http\Router\OpenApi\ObjectInterface;
use Sunrise\Http\Router\OpenApi\Tests\Fixture;

/**
 * AbstractAnnotationTest
 */
class AbstractAnnotationTest extends TestCase
{
    use Fixture\AwareSimpleAnnotationReader;

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $annotation = $this->createMock(AbstractAnnotation::class);

        $this->assertInstanceOf(ObjectInterface::class, $annotation);
    }

    /**
     * @return void
     */
    public function testGetReferencedObjects() : void
    {
        $annotation = new class extends AbstractAnnotation
        {
            public $child;
            public $reference;

            public function __construct()
            {
                $this->child = clone $this;

                $this->child->reference = new class extends AbstractAnnotationReference
                {
                    public $class = Fixture\PetStore\Entity\Pet::class;

                    public function getAnnotationName() : string
                    {
                        return Schema::class;
                    }
                };
            }
        };

        $referencedObjects = $annotation->getReferencedObjects(
            $this->createSimpleAnnotationReader()
        );

        $this->assertSame([
            'properties' => [
                'id' => [
                    'format' => 'int64',
                    'type' => 'integer',
                ],
                'name' => [
                    'type' => 'string',
                ],
                'tag' => [
                    'type' => 'string',
                ],
            ],
            'required' => [
                'id',
                'name',
            ],
            'type' => 'object',

        ], reset($referencedObjects)->toArray());
    }

    /**
     * @return void
     */
    public function testSimplifiedReferencing() : void
    {
        $annotation = new class extends AbstractAnnotation
        {
            public $child;
            public $reference;

            public function __construct()
            {
                $this->child = clone $this;

                $this->child->reference = new class extends AbstractAnnotationReference
                {
                    public $class = Fixture\SimplifiedReferencing\Foo::class;

                    public function getAnnotationName() : string
                    {
                        return Schema::class;
                    }
                };
            }
        };

        $referencedObjects = $annotation->getReferencedObjects($this->createSimpleAnnotationReader());
        foreach ($referencedObjects as &$referencedObject) {
            $referencedObject = $referencedObject->toArray();
        }

        $this->assertSame([
            [
                'properties' => [
                    'bar' => [
                        '$ref' => '#/components/schemas/Bar',
                    ],
                    'baz' => [
                        '$ref' => '#/components/schemas/Baz',
                    ],
                ],
                'type' => 'object',
            ],
            [
                'properties' => [
                    'value' => [
                        '$ref' => '#/components/schemas/Bar.value',
                    ],
                ],
                'type' => 'object',
            ],
            [
                'type' => 'string',
            ],
            [
                'properties' => [
                    'value' => [
                        '$ref' => '#/components/schemas/Baz.fn_value',
                    ],
                ],
                'type' => 'object',
            ],
            [
                'type' => 'string',
            ],
        ], $referencedObjects);
    }
}
