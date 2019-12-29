<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Fixture\PetStore;

/**
 * Import classes
 */
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Endpoint
 *
 * @OpenApi\Operation(
 *   responses = {
 *     200 = @OpenApi\Response(
 *       description = "All okay",
 *     ),
 *     "default" = @OpenApi\Response(
 *       description = "Any error",
 *       content = {
 *         "application/json" = @OpenApi\MediaType(
 *           schema = @OpenApi\SchemaReference(
 *             class = "Sunrise\Http\Router\OpenApi\Tests\Fixture\PetStore\Endpoint",
 *             method = "error",
 *           ),
 *         ),
 *       },
 *     ),
 *   },
 * )
 */
class Endpoint implements RequestHandlerInterface
{

    /**
     * @OpenApi\Parameter(
     *   refName = "queryLimit",
     *   name = "limit",
     *   in = "query",
     *   description = "How many items to return at one time (max 100)",
     *   required = false,
     *   schema = @OpenApi\Schema(
     *     type = "integer",
     *     format = "int32",
     *   ),
     * )
     *
     * @var int
     */
    protected $limit = 50;

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        throw new \RuntimeException(\sprintf(__METHOD__ . ': cannot be called.'));
    }

    /**
     * @OpenApi\Schema(
     *   refName = "Error",
     *   type = "object",
     *   required = {
     *     "code",
     *     "message",
     *   },
     *   properties = {
     *     "code" = @OpenApi\Schema(
     *       type = "integer",
     *       format = "int32",
     *     ),
     *     "message" = @OpenApi\Schema(
     *       type = "string",
     *     ),
     *   },
     * )
     *
     * @param int $code
     * @param string $message
     *
     * @return array
     */
    protected function error(int $code, string $message) : array
    {
        return \compact('code', 'message');
    }
}
