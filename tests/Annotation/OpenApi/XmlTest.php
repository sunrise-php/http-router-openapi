<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Annotation\OpenApi;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Xml;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\XmlInterface;
use Sunrise\Http\Router\OpenApi\AbstractAnnotation;
use Sunrise\Http\Router\OpenApi\ObjectInterface;

/**
 * XmlTest
 */
class XmlTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $object = new Xml();

        $this->assertInstanceOf(AbstractAnnotation::class, $object);
        $this->assertInstanceOf(XmlInterface::class, $object);
        $this->assertInstanceOf(ObjectInterface::class, $object);
    }
}
