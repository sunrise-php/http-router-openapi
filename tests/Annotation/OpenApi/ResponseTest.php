<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Annotation\OpenApi;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\AbstractAnnotation;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Response;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\ResponseInterface;
use Sunrise\Http\Router\OpenApi\ComponentInterface;

/**
 * Import functions
 */
use function spl_object_hash;

/**
 * ResponseTest
 */
class ResponseTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $object = new Response();

        $this->assertInstanceOf(ResponseInterface::class, $object);
        $this->assertInstanceOf(AbstractAnnotation::class, $object);
        $this->assertInstanceOf(ComponentInterface::class, $object);
    }

    /**
     * @return void
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-componentsresponses
     */
    public function testGetComponentName() : void
    {
        $object = new Response();

        $this->assertSame('responses', $object->getComponentName());
    }

    /**
     * @return void
     */
    public function testGetDefaultReferenceName() : void
    {
        $object = new Response();
        $expected = spl_object_hash($object);

        $this->assertSame($expected, $object->getReferenceName());
    }

    /**
     * @return void
     */
    public function testGetCustomReferenceName() : void
    {
        $object = new Response();
        $object->refName = 'foo';

        $this->assertSame('foo', $object->getReferenceName());
    }

    /**
     * @return void
     */
    public function testIgnoreFields() : void
    {
        $object = new Response();
        $object->refName = 'foo';
        $object->foo = 'bar';

        $this->assertSame(['foo' => 'bar'], $object->toArray());
    }
}
