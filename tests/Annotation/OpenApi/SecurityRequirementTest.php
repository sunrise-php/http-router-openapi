<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Annotation\OpenApi;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\SecurityRequirement;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\SecurityRequirementInterface;
use Sunrise\Http\Router\OpenApi\ObjectInterface;

/**
 * SecurityRequirementTest
 */
class SecurityRequirementTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $object = new SecurityRequirement();

        $this->assertInstanceOf(SecurityRequirementInterface::class, $object);
        $this->assertInstanceOf(ObjectInterface::class, $object);
    }

    /**
     * @return void
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#patterned-fields-3
     */
    public function testToArray() : void
    {
        $object = new SecurityRequirement();
        $object->name = 'foo';
        $object->scopes = ['bar', 'baz'];

        $this->assertSame(['foo' => ['bar', 'baz']], $object->toArray());
    }
}
