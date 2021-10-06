<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Annotation\OpenApi;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\AbstractAnnotationReference;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Link;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\LinkInterface;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\LinkReference;

/**
 * LinkReferenceTest
 */
class LinkReferenceTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $reference = new LinkReference();

        $this->assertInstanceOf(LinkInterface::class, $reference);
        $this->assertInstanceOf(AbstractAnnotationReference::class, $reference);
    }

    /**
     * @return void
     */
    public function testGetAnnotationName() : void
    {
        $reference = new LinkReference();

        $this->assertSame(Link::class, $reference->getAnnotationName());
    }
}
