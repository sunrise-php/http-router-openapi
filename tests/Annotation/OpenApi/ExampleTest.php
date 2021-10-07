<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Annotation\OpenApi;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\AbstractAnnotation;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Example;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\ExampleInterface;
use Sunrise\Http\Router\OpenApi\ComponentInterface;

/**
 * Import functions
 */
use function spl_object_hash;

/**
 * ExampleTest
 */
class ExampleTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $object = new Example();

        $this->assertInstanceOf(ExampleInterface::class, $object);
        $this->assertInstanceOf(AbstractAnnotation::class, $object);
        $this->assertInstanceOf(ComponentInterface::class, $object);
    }

    /**
     * @return void
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-componentsexamples
     */
    public function testGetComponentName() : void
    {
        $object = new Example();

        $this->assertSame('examples', $object->getComponentName());
    }

    /**
     * @return void
     */
    public function testGetDefaultReferenceName() : void
    {
        $object = new Example();
        $expected = spl_object_hash($object);

        $this->assertSame($expected, $object->getReferenceName());
    }

    /**
     * @return void
     */
    public function testGetCustomReferenceName() : void
    {
        $object = new Example();
        $object->refName = 'foo';

        $this->assertSame('foo', $object->getReferenceName());
    }

    /**
     * @return void
     */
    public function testIgnoreFields() : void
    {
        $object = new Example();
        $object->refName = 'foo';
        $object->foo = 'bar';

        $this->assertSame(['foo' => 'bar'], $object->toArray());
    }

    /**
     * @return void
     */
    public function testFieldAliases() : void
    {
        $object = new Example();
        $object->anyValue = 'foo';

        $this->assertSame(['value' => 'foo'], $object->toArray());
    }
}
