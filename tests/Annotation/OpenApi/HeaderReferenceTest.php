<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Annotation\OpenApi;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\AbstractAnnotationReference;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Header;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\HeaderInterface;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\HeaderReference;

/**
 * HeaderReferenceTest
 */
class HeaderReferenceTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $reference = new HeaderReference();

        $this->assertInstanceOf(HeaderInterface::class, $reference);
        $this->assertInstanceOf(AbstractAnnotationReference::class, $reference);
    }

    /**
     * @return void
     */
    public function testGetAnnotationName() : void
    {
        $reference = new HeaderReference();

        $this->assertSame(Header::class, $reference->getAnnotationName());
    }
}
