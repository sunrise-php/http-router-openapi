<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Fixture\SimplifiedReferencing;

/**
 * @OpenApi\Schema(
 *     type="object",
 *     properties={
 *         "bar": @OpenApi\SchemaReference("Bar"),
 *         "baz": @OpenApi\SchemaReference("Baz"),
 *     },
 * )
 */
class Foo
{
}
