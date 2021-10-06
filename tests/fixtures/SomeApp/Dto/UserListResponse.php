<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Fixtures\SomeApp\Dto;

/**
 * @OpenApi\Response(
 *   description="Returns a list of user models",
 *   content={
 *     "application/json": @OpenApi\MediaType(
 *       schema=@OpenApi\Schema(
 *         type="array",
 *         items=@OpenApi\SchemaReference("User"),
 *       ),
 *     ),
 *   },
 * )
 */
final class UserListResponse
{
}
