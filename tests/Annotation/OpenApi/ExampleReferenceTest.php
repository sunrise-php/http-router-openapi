<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Annotation\OpenApi;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Example;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\ExampleInterface;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\ExampleReference;
use Sunrise\Http\Router\OpenApi\AbstractAnnotationReference;
use Sunrise\Http\Router\OpenApi\ObjectInterface;

/**
 * ExampleReferenceTest
 */
class ExampleReferenceTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $reference = new ExampleReference();

        $this->assertInstanceOf(AbstractAnnotationReference::class, $reference);
        $this->assertInstanceOf(ExampleInterface::class, $reference);
        $this->assertInstanceOf(ObjectInterface::class, $reference);
    }

    /**
     * @return void
     */
    public function testGetAnnotationName() : void
    {
        $reference = new ExampleReference();

        $this->assertSame(Example::class, $reference->getAnnotationName());
    }
}
