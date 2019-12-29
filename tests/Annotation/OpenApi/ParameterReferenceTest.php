<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Annotation\OpenApi;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Parameter;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\ParameterInterface;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\ParameterReference;
use Sunrise\Http\Router\OpenApi\AbstractAnnotationReference;

/**
 * ParameterReferenceTest
 */
class ParameterReferenceTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $reference = new ParameterReference();

        $this->assertInstanceOf(ParameterInterface::class, $reference);
        $this->assertInstanceOf(AbstractAnnotationReference::class, $reference);
    }

    /**
     * @return void
     */
    public function testGetAnnotationName() : void
    {
        $reference = new ParameterReference();

        $this->assertSame(Parameter::class, $reference->getAnnotationName());
    }
}
