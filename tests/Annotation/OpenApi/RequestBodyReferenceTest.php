<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Annotation\OpenApi;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\RequestBody;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\RequestBodyInterface;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\RequestBodyReference;
use Sunrise\Http\Router\OpenApi\AbstractAnnotationReference;
use Sunrise\Http\Router\OpenApi\ObjectInterface;

/**
 * RequestBodyReferenceTest
 */
class RequestBodyReferenceTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $reference = new RequestBodyReference();

        $this->assertInstanceOf(AbstractAnnotationReference::class, $reference);
        $this->assertInstanceOf(RequestBodyInterface::class, $reference);
        $this->assertInstanceOf(ObjectInterface::class, $reference);
    }

    /**
     * @return void
     */
    public function testGetAnnotationName() : void
    {
        $reference = new RequestBodyReference();

        $this->assertSame(RequestBody::class, $reference->getAnnotationName());
    }
}
