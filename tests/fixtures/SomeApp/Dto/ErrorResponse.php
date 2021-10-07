<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Fixtures\SomeApp\Dto;

/**
 * @OpenApi\Response(
 *   description="Returns an error model",
 *   content={
 *     "application/json": @OpenApi\MediaType(
 *       schema=@OpenApi\SchemaReference("Error"),
 *     ),
 *   },
 * )
 */
final class ErrorResponse
{
}
