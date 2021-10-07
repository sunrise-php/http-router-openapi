<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Annotation\OpenApi;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\AbstractAnnotation;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\MediaType;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\MediaTypeInterface;

/**
 * MediaTypeTest
 */
class MediaTypeTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $object = new MediaType();

        $this->assertInstanceOf(MediaTypeInterface::class, $object);
        $this->assertInstanceOf(AbstractAnnotation::class, $object);
    }
}
