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
use Sunrise\Http\Router\Exception\UnsupportedMediaTypeException;
use Sunrise\Http\Router\OpenApi\Exception\UnsupportedMediaTypeException as LocalUnsupportedMediaTypeException;
use Sunrise\Http\Router\OpenApi\Utility\JsonSchemaBuilder;
use Sunrise\Http\Router\Route;
use Sunrise\Http\Router\RouteInterface;
use ReflectionClass;
use RuntimeException;

/**
 * Import functions
 */
use function class_exists;
use function strpos;
use function substr;

/**
 * RequestBodyValidationMiddleware
 */
class RequestBodyValidationMiddleware implements MiddlewareInterface
{

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
     * Tries to determine the reflection of an object that contains the `@OpenApi\Operation()` annotation
     *
     * @param ServerRequestInterface $request
     *
     * @return null|ReflectionClass
     */
    protected function fetchOperationSource(ServerRequestInterface $request) : ?ReflectionClass
    {
        $route = $request->getAttribute(Route::ATTR_NAME_FOR_ROUTE);

        if ($route instanceof RouteInterface) {
            return new ReflectionClass(
                $route->getRequestHandler()
            );
        }

        return null;
    }

    /**
     * Tries to determine a MIME type for the request body
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     *
     * @link https://tools.ietf.org/html/rfc7231#section-3.1.1.1
     */
    protected function fetchMimeType(ServerRequestInterface $request) : string
    {
        $result = $request->getHeaderLine('Content-Type');
        $semicolon = strpos($result, ';');

        if (false !== $semicolon) {
            $result = substr($result, 0, $semicolon);
        }

        return $result;
    }

    /**
     * Tries to determine a JSON schema for the request body
     *
     * @param ServerRequestInterface $request
     *
     * @return mixed
     */
    protected function fetchJsonSchema(ServerRequestInterface $request)
    {
        $operationSource = $this->fetchOperationSource($request);
        if (!$operationSource) {
            return null;
        }

        $mimeType = $this->fetchMimeType($request);
        if (!$mimeType) {
            return null;
        }

        $builder = new JsonSchemaBuilder($operationSource);

        return $builder->forRequestBody($mimeType);
    }

    /**
     * Validates the given request
     *
     * @param ServerRequestInterface $request
     *
     * @return void
     *
     * @throws BadRequestException
     * @throws UnsupportedMediaTypeException
     */
    protected function validate(ServerRequestInterface $request) : void
    {
        try {
            $jsonSchema = $this->fetchJsonSchema($request);
        } catch (LocalUnsupportedMediaTypeException $e) {
            throw new UnsupportedMediaTypeException($e->getMessage(), [
                'type' => $e->getType(),
                'supported' => $e->getSupportedTypes(),
            ], $e->getCode(), $e);
        }

        if (null === $jsonSchema) {
            return;
        }

        $payload = (array) $request->getParsedBody();
        $payload = Validator::arrayToObjectRecursive($payload);

        $validator = new Validator();
        $validator->validate($payload, $jsonSchema);

        if (!$validator->isValid()) {
            throw new BadRequestException('', [
                'jsonSchema' => $jsonSchema,
                'violations' => $validator->getErrors(),
            ]);
        }
    }
}
