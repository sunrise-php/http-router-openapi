<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Annotation\OpenApi;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\AbstractAnnotation;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Parameter;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\ParameterCookie;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\ParameterHeader;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\ParameterQuery;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\ParameterInterface;
use Sunrise\Http\Router\OpenApi\ComponentInterface;

/**
 * Import functions
 */
use function spl_object_hash;

/**
 * ParameterTest
 */
class ParameterTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $object = new Parameter();

        $this->assertInstanceOf(ParameterInterface::class, $object);
        $this->assertInstanceOf(AbstractAnnotation::class, $object);
        $this->assertInstanceOf(ComponentInterface::class, $object);
    }

    /**
     * @return void
     */
    public function testPreparedClasses() : void
    {
        $object = new ParameterCookie();
        $this->assertInstanceOf(Parameter::class, $object);
        $this->assertSame('cookie', $object->in);

        $object = new ParameterHeader();
        $this->assertInstanceOf(Parameter::class, $object);
        $this->assertSame('header', $object->in);

        $object = new ParameterQuery();
        $this->assertInstanceOf(Parameter::class, $object);
        $this->assertSame('query', $object->in);
    }

    /**
     * @return void
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-componentsparameters
     */
    public function testGetComponentName() : void
    {
        $object = new Parameter();

        $this->assertSame('parameters', $object->getComponentName());
    }

    /**
     * @return void
     */
    public function testGetDefaultReferenceName() : void
    {
        $object = new Parameter();
        $expected = spl_object_hash($object);

        $this->assertSame($expected, $object->getReferenceName());
    }

    /**
     * @return void
     */
    public function testGetCustomReferenceName() : void
    {
        $object = new Parameter();
        $object->refName = 'foo';

        $this->assertSame('foo', $object->getReferenceName());
    }

    /**
     * @return void
     */
    public function testIgnoreFields() : void
    {
        $object = new Parameter();
        $object->refName = 'foo';
        $object->foo = 'bar';

        $this->assertSame(['foo' => 'bar'], $object->toArray());
    }
}
