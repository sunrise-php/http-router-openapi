<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Annotation\OpenApi;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\ExternalDocumentation;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\ExternalDocumentationInterface;
use Sunrise\Http\Router\OpenApi\AbstractAnnotation;

/**
 * ExternalDocumentationTest
 */
class ExternalDocumentationTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $object = new ExternalDocumentation();

        $this->assertInstanceOf(ExternalDocumentationInterface::class, $object);
        $this->assertInstanceOf(AbstractAnnotation::class, $object);
    }
}
