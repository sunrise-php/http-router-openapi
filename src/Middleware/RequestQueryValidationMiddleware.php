<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2019, Anatoly Fenric
 * @license https://github.com/sunrise-php/http-router-openapi/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-router-openapi
 */

namespace Sunrise\Http\Router\OpenApi\Middleware;

/**
 * Import classes
 */
use JsonSchema\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sunrise\Http\Router\Exception\BadRequestException;
use Sunrise\Http\Router\OpenApi\Utility\JsonSchemaBuilder;
use Sunrise\Http\Router\Route;
use Sunrise\Http\Router\RouteInterface;
use ReflectionClass;
use RuntimeException;

/**
 * Import functions
 */
use function class_exists;
use function json_decode;
use function json_encode;

/**
 * RequestQueryValidationMiddleware
 *
 * Don't use this middleware globally!
 */
class RequestQueryValidationMiddleware implements MiddlewareInterface
{

    /**
     * @var bool
     */
    private $useCache = false;

    /**
     * Constructor of the class
     *
     * @throws RuntimeException
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        if (!class_exists('JsonSchema\Validator')) {
            throw new RuntimeException('To use request body validation, install the "justinrainbow/json-schema"');
        }
    }

    /**
     * @return void
     */
    public function useCache() : void
    {
        $this->useCache = true;
    }

    /**
     * {@inheritDoc}
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $this->validate($request);

        return $handler->handle($request);
    }

    /**
     * Validates the given request
     *
     * @param ServerRequestInterface $request
     *
     * @return void
     *
     * @throws BadRequestException
     */
    protected function validate(ServerRequestInterface $request) : void
    {
        $route = $request->getAttribute(Route::ATTR_NAME_FOR_ROUTE);
        if (!($route instanceof RouteInterface)) {
            return;
        }

        $operationSource = new ReflectionClass($route->getRequestHandler());
        $jsonSchemaBuilder = new JsonSchemaBuilder($operationSource);

        if ($this->useCache) {
            $jsonSchemaBuilder->useCache();
        }

        $jsonSchema = $jsonSchemaBuilder->forRequestQueryParams();
        if (null === $jsonSchema) {
            return;
        }

        $payload = json_encode($request->getQueryParams());
        $payload = (object) json_decode($payload);

        $validator = new Validator();
        $validator->validate($payload, $jsonSchema);

        if (!$validator->isValid()) {
            throw new BadRequestException('The request query parameters is not valid for this resource.', [
                'jsonSchema' => $jsonSchema,
                'violations' => $validator->getErrors(),
            ]);
        }
    }
}
