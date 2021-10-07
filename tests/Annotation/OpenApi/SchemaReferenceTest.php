<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Annotation\OpenApi;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\AbstractAnnotationReference;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Schema;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\SchemaInterface;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\SchemaReference;

/**
 * SchemaReferenceTest
 */
class SchemaReferenceTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $reference = new SchemaReference();

        $this->assertInstanceOf(SchemaInterface::class, $reference);
        $this->assertInstanceOf(AbstractAnnotationReference::class, $reference);
    }

    /**
     * @return void
     */
    public function testGetAnnotationName() : void
    {
        $reference = new SchemaReference();

        $this->assertSame(Schema::class, $reference->getAnnotationName());
    }
}
