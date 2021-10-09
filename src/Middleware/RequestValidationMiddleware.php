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
use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sunrise\Http\Router\Exception\BadRequestException;
use Sunrise\Http\Router\Exception\UnsupportedMediaTypeException;
use Sunrise\Http\Router\OpenApi\OpenApi;
use Sunrise\Http\Router\RouteInterface;
use Sunrise\Http\Router\Route;
use RuntimeException;

/**
 * Import functions
 */
use function class_exists;
use function strpos;
use function substr;

/**
 * RequestValidationMiddleware
 */
final class RequestValidationMiddleware implements MiddlewareInterface
{

    /**
     * Default validation options
     *
     * @var int
     *
     * @link https://github.com/justinrainbow/json-schema/tree/4c74da50b0ca56469f5c7b1903ab5f2c7bf68f4d#configuration-options
     */
    public const DEFAULT_VALIDATION_OPTIONS = Constraint::CHECK_MODE_TYPE_CAST | Constraint::CHECK_MODE_COERCE_TYPES;

    /**
     * Openapi instance
     *
     * @var OpenApi
     */
    private $openapi;

    /**
     * Validator instance
     *
     * @var Validator
     */
    private $validator;

    /**
     * Validation options
     *
     * @var int
     */
    private $cookieValidationOptions;
    private $headerValidationOptions;
    private $queryValidationOptions;
    private $bodyValidationOptions;

    /**
     * Constructor of the class
     *
     * @param OpenApi $openapi
     * @param int $cookieValidationOptions
     * @param int $headerValidationOptions
     * @param int $queryValidationOptions
     * @param int $bodyValidationOptions
     */
    public function __construct(
        OpenApi $openapi,
        int $cookieValidationOptions = self::DEFAULT_VALIDATION_OPTIONS,
        int $headerValidationOptions = self::DEFAULT_VALIDATION_OPTIONS,
        int $queryValidationOptions = self::DEFAULT_VALIDATION_OPTIONS,
        int $bodyValidationOptions = self::DEFAULT_VALIDATION_OPTIONS
    ) {
        if (!class_exists(Validator::class)) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException('To use request validation, install the "justinrainbow/json-schema".');
            // @codeCoverageIgnoreEnd
        }

        $this->openapi = $openapi;
        $this->cookieValidationOptions = $cookieValidationOptions;
        $this->headerValidationOptions = $headerValidationOptions;
        $this->queryValidationOptions = $queryValidationOptions;
        $this->bodyValidationOptions = $bodyValidationOptions;
        $this->validator = new Validator();
    }

    /**
     * {@inheritdoc}
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     *
     * @throws BadRequestException
     *         If one of the request parts isn't valid.
     *
     * @throws UnsupportedMediaTypeException
     *         If the request body contains an unsupported type.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $route = $request->getAttribute(Route::ATTR_NAME_FOR_ROUTE);
        if (!($route instanceof RouteInterface)) {
            return $handler->handle($request);
        }

        $operationId = $route->getName();

        // https://tools.ietf.org/html/rfc7231#section-3.1.1.1
        $contentType = $request->getHeaderLine('Content-Type');
        if (false !== ($semicolon = strpos($contentType, ';'))) {
            $contentType = substr($contentType, 0, $semicolon);
        }

        $cookieJsonSchema = $this->openapi->getRequestCookieJsonSchema($operationId);
        if (isset($cookieJsonSchema)) {
            $this->validateRequestCookie($request, $cookieJsonSchema);
        }

        $headerJsonSchema = $this->openapi->getRequestHeaderJsonSchema($operationId);
        if (isset($headerJsonSchema)) {
            $this->validateRequestHeader($request, $headerJsonSchema);
        }

        $queryJsonSchema = $this->openapi->getRequestQueryJsonSchema($operationId);
        if (isset($queryJsonSchema)) {
            $this->validateRequestQuery($request, $queryJsonSchema);
        }

        $bodyJsonSchema = $this->openapi->getRequestBodyJsonSchema($operationId, $contentType);
        if (isset($bodyJsonSchema)) {
            $this->validateRequestBody($request, $bodyJsonSchema);
        }

        return $handler->handle($request);
    }

    /**
     * Validates the given request cookie by the given JSON schema
     *
     * @param ServerRequestInterface $request
     * @param array $jsonSchema
     *
     * @return void
     *
     * @throws BadRequestException
     *         If the request cookie isn't valid.
     */
    private function validateRequestCookie(ServerRequestInterface $request, array $jsonSchema) : void
    {
        $cookies = $request->getCookieParams();

        $this->validator->validate($cookies, $jsonSchema, $this->cookieValidationOptions);
        if (!$this->validator->isValid()) {
            throw new BadRequestException('The request cookie is not valid for this resource.', [
                'jsonSchema' => $jsonSchema,
                'errors' => $this->validator->getErrors(),
            ]);
        }
    }

    /**
     * Validates the given request header by the given JSON schema
     *
     * @param ServerRequestInterface $request
     * @param array $jsonSchema
     *
     * @return void
     *
     * @throws BadRequestException
     *         If the request header isn't valid.
     */
    private function validateRequestHeader(ServerRequestInterface $request, array $jsonSchema) : void
    {
        $headers = [];
        foreach ($request->getHeaders() as $header => $_) {
            $headers[$header] = $request->getHeaderLine($header);
        }

        $this->validator->validate($headers, $jsonSchema, $this->headerValidationOptions);
        if (!$this->validator->isValid()) {
            throw new BadRequestException('The request header is not valid for this resource.', [
                'jsonSchema' => $jsonSchema,
                'errors' => $this->validator->getErrors(),
            ]);
        }
    }

    /**
     * Validates the given request query by the given JSON schema
     *
     * @param ServerRequestInterface $request
     * @param array $jsonSchema
     *
     * @return void
     *
     * @throws BadRequestException
     *         If the request query isn't valid.
     */
    private function validateRequestQuery(ServerRequestInterface $request, array $jsonSchema) : void
    {
        $query = $request->getQueryParams();

        $this->validator->validate($query, $jsonSchema, $this->queryValidationOptions);
        if (!$this->validator->isValid()) {
            throw new BadRequestException('The request query is not valid for this resource.', [
                'jsonSchema' => $jsonSchema,
                'errors' => $this->validator->getErrors(),
            ]);
        }
    }

    /**
     * Validates the given request body by the given JSON schema
     *
     * @param ServerRequestInterface $request
     * @param array $jsonSchema
     *
     * @return void
     *
     * @throws BadRequestException
     *         If the request body isn't valid.
     */
    private function validateRequestBody(ServerRequestInterface $request, array $jsonSchema) : void
    {
        $body = $request->getParsedBody();

        $this->validator->validate($body, $jsonSchema, $this->bodyValidationOptions);
        if (!$this->validator->isValid()) {
            throw new BadRequestException('The request body is not valid for this resource.', [
                'jsonSchema' => $jsonSchema,
                'errors' => $this->validator->getErrors(),
            ]);
        }
    }
}
