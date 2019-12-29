<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Annotation\OpenApi;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Response;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\ResponseInterface;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\ResponseReference;
use Sunrise\Http\Router\OpenApi\AbstractAnnotationReference;

/**
 * ResponseReferenceTest
 */
class ResponseReferenceTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $reference = new ResponseReference();

        $this->assertInstanceOf(ResponseInterface::class, $reference);
        $this->assertInstanceOf(AbstractAnnotationReference::class, $reference);
    }

    /**
     * @return void
     */
    public function testGetAnnotationName() : void
    {
        $reference = new ResponseReference();

        $this->assertSame(Response::class, $reference->getAnnotationName());
    }
}
