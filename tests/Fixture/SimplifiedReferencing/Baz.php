<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Fixture\SimplifiedReferencing;

/**
 * @OpenApi\Schema(
 *     type="object",
 *     properties={
 *         "value": @OpenApi\SchemaReference("@value"),
 *     },
 * )
 */
class Baz
{

    /**
     * @OpenApi\Schema(
     *     type="string",
     * )
     */
    public function value()
    {
    }
}
