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
use RuntimeException;

/**
 * Import functions
 */
use function class_exists;
use function sprintf;
use function strpos;
use function substr;

/**
 * Validates the given request using all possible JSON schemes
 *
 * If you cannot pass the openapi to the constructor,
 * or your architecture has problems with autowiring,
 * then inherit this class and override the getOpenapi method.
 *
 * @since 2.0.0
 */
class RequestValidationMiddleware implements MiddlewareInterface
{

    /**
     * Default validation options
     *
     * @var int
     *
     * @link https://github.com/justinrainbow/json-schema/tree/4c74da50b0ca56469f5c7b1903ab5f2c7bf68f4d#configuration-options
     */
    public const DEFAULT_VALIDATION_OPTIONS = Constraint::CHECK_MODE_TYPE_CAST|Constraint::CHECK_MODE_COERCE_TYPES;

    /**
     * The openapi instance
     *
     * @var OpenApi|null
     */
    private $openapi;

    /**
     * Cookie validation options
     *
     * @var int|null
     */
    private $cookieValidationOptions;

    /**
     * Header validation options
     *
     * @var int|null
     */
    private $headerValidationOptions;

    /**
     * Query validation options
     *
     * @var int|null
     */
    private $queryValidationOptions;

    /**
     * Body validation options
     *
     * @var int|null
     */
    private $bodyValidationOptions;

    /**
     * Constructor of the class
     *
     * @param OpenApi|null $openapi
     * @param int|null $cookieValidationOptions
     * @param int|null $headerValidationOptions
     * @param int|null $queryValidationOptions
     * @param int|null $bodyValidationOptions
     *
     * @throws RuntimeException
     *         If the "justinrainbow/json-schema" isn't installed.
     */
    public function __construct(
        ?OpenApi $openapi = null,
        ?int $cookieValidationOptions = self::DEFAULT_VALIDATION_OPTIONS,
        ?int $headerValidationOptions = self::DEFAULT_VALIDATION_OPTIONS,
        ?int $queryValidationOptions = self::DEFAULT_VALIDATION_OPTIONS,
        ?int $bodyValidationOptions = self::DEFAULT_VALIDATION_OPTIONS
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
    }

    /**
     * Gets the openapi instance
     *
     * @return OpenApi
     *
     * @throws RuntimeException
     *         If the class doesn't contain the openapi instance.
     */
    protected function getOpenapi() : OpenApi
    {
        if (null === $this->openapi) {
            throw new RuntimeException(sprintf(
                'The %2$s() method MUST return the %1$s class instance. ' .
                'Pass the %1$s class instance to the constructor, or override the %2$s() method.',
                OpenApi::class,
                __METHOD__
            ));
        }

        return $this->openapi;
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
    final public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $route = $request->getAttribute(RouteInterface::ATTR_ROUTE);
        if (!($route instanceof RouteInterface)) {
            return $handler->handle($request);
        }

        $openapi = $this->getOpenapi();
        $validator = new Validator();
        $operationId = $route->getName();

        // https://tools.ietf.org/html/rfc7231#section-3.1.1.1
        $contentType = $request->getHeaderLine('Content-Type');
        if (false !== ($semicolon = strpos($contentType, ';'))) {
            $contentType = substr($contentType, 0, $semicolon);
        }

        if (isset($this->cookieValidationOptions)) {
            $jsonSchema = $openapi->getRequestCookieJsonSchema($operationId);
            if (isset($jsonSchema)) {
                $request = $this->validateRequestCookie(
                    $request,
                    $jsonSchema,
                    $validator,
                    $this->cookieValidationOptions
                );
            }
        }

        if (isset($this->headerValidationOptions)) {
            $jsonSchema = $openapi->getRequestHeaderJsonSchema($operationId);
            if (isset($jsonSchema)) {
                $request = $this->validateRequestHeader(
                    $request,
                    $jsonSchema,
                    $validator,
                    $this->headerValidationOptions
                );
            }
        }

        if (isset($this->queryValidationOptions)) {
            $jsonSchema = $openapi->getRequestQueryJsonSchema($operationId);
            if (isset($jsonSchema)) {
                $request = $this->validateRequestQuery(
                    $request,
                    $jsonSchema,
                    $validator,
                    $this->queryValidationOptions
                );
            }
        }

        if (isset($this->bodyValidationOptions)) {
            $jsonSchema = $openapi->getRequestBodyJsonSchema($operationId, $contentType);
            if (isset($jsonSchema)) {
                $request = $this->validateRequestBody(
                    $request,
                    $jsonSchema,
                    $validator,
                    $this->bodyValidationOptions
                );
            }
        }

        return $handler->handle($request);
    }

    /**
     * Validates the given request cookie by the given JSON schema
     *
     * @param ServerRequestInterface $request
     * @param array $jsonSchema
     * @param Validator $validator
     * @param int $validationOptions
     *
     * @return ServerRequestInterface
     *         New request with changed data.
     *
     * @throws BadRequestException
     *         If the validation data isn't valid.
     */
    private function validateRequestCookie(
        ServerRequestInterface $request,
        array $jsonSchema,
        Validator $validator,
        int $validationOptions
    ) : ServerRequestInterface {
        $cookies = $request->getCookieParams();
        $validator->validate($cookies, $jsonSchema, $validationOptions);
        if (!$validator->isValid()) {
            throw new BadRequestException('The request cookie is not valid for this resource.', [
                'errors' => $validator->getErrors(),
            ]);
        }

        return $request;
    }

    /**
     * Validates the given request header by the given JSON schema
     *
     * @param ServerRequestInterface $request
     * @param array $jsonSchema
     * @param Validator $validator
     * @param int $validationOptions
     *
     * @return ServerRequestInterface
     *         New request with changed data.
     *
     * @throws BadRequestException
     *         If the validation data isn't valid.
     */
    private function validateRequestHeader(
        ServerRequestInterface $request,
        array $jsonSchema,
        Validator $validator,
        int $validationOptions
    ) : ServerRequestInterface {
        $headers = [];
        foreach ($request->getHeaders() as $header => $_) {
            $headers[$header] = $request->getHeaderLine($header);
        }

        $validator->validate($headers, $jsonSchema, $validationOptions);
        if (!$validator->isValid()) {
            throw new BadRequestException('The request header is not valid for this resource.', [
                'errors' => $validator->getErrors(),
            ]);
        }

        return $request;
    }

    /**
     * Validates the given request query by the given JSON schema
     *
     * @param ServerRequestInterface $request
     * @param array $jsonSchema
     * @param Validator $validator
     * @param int $validationOptions
     *
     * @return ServerRequestInterface
     *         New request with changed data.
     *
     * @throws BadRequestException
     *         If the validation data isn't valid.
     */
    private function validateRequestQuery(
        ServerRequestInterface $request,
        array $jsonSchema,
        Validator $validator,
        int $validationOptions
    ) : ServerRequestInterface {
        $query = $request->getQueryParams();
        $validator->validate($query, $jsonSchema, $validationOptions);
        if (!$validator->isValid()) {
            throw new BadRequestException('The request query is not valid for this resource.', [
                'errors' => $validator->getErrors(),
            ]);
        }

        return $request->withQueryParams($query);
    }

    /**
     * Validates the given request body by the given JSON schema
     *
     * @param ServerRequestInterface $request
     * @param array $jsonSchema
     * @param Validator $validator
     * @param int $validationOptions
     *
     * @return ServerRequestInterface
     *         New request with changed data.
     *
     * @throws BadRequestException
     *         If the validation data isn't valid.
     */
    private function validateRequestBody(
        ServerRequestInterface $request,
        array $jsonSchema,
        Validator $validator,
        int $validationOptions
    ) : ServerRequestInterface {
        $body = $request->getParsedBody();
        $validator->validate($body, $jsonSchema, $validationOptions);
        if (!$validator->isValid()) {
            throw new BadRequestException('The request body is not valid for this resource.', [
                'errors' => $validator->getErrors(),
            ]);
        }

        return $request->withParsedBody($body);
    }
}
