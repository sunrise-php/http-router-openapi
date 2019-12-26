<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Annotation\OpenApi;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Discriminator;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\DiscriminatorInterface;
use Sunrise\Http\Router\OpenApi\AbstractAnnotation;
use Sunrise\Http\Router\OpenApi\ObjectInterface;

/**
 * DiscriminatorTest
 */
class DiscriminatorTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $object = new Discriminator();

        $this->assertInstanceOf(AbstractAnnotation::class, $object);
        $this->assertInstanceOf(DiscriminatorInterface::class, $object);
        $this->assertInstanceOf(ObjectInterface::class, $object);
    }
}
