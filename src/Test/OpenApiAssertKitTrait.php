<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2019, Anatoly Fenric
 * @license https://github.com/sunrise-php/http-router-openapi/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-router-openapi
 */

namespace Sunrise\Http\Router\OpenApi\Test;

/**
 * Import classes
 */
use JsonSchema\Validator as JsonSchemaValidator;
use Psr\Http\Message\ResponseInterface;
use Sunrise\Http\Router\OpenApi\Utility\JsonSchemaBuilder;
use Sunrise\Http\Router\RouteInterface;
use ReflectionClass;

/**
 * Import functions
 */
use function json_decode;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;

/**
 * Import constants
 */
use const JSON_ERROR_NONE;
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

/**
 * OpenApiAssertKitTrait
 */
trait OpenApiAssertKitTrait
{

    /**
     * @param RouteInterface $route
     * @param ResponseInterface $response
     *
     * @return void
     */
    protected function assertResponseBodyMatchesDescription(RouteInterface $route, ResponseInterface $response) : void
    {
        $body = (string) $response->getBody();
        if ('' === $body) {
            $this->fail('Response body MUST be non-empty.');
        }

        $data = json_decode($body);
        if (JSON_ERROR_NONE !== json_last_error()) {
            $this->fail('Response body MUST contain valid JSON: ' . json_last_error_msg());
        }

        $jsonSchemaBuilder = new JsonSchemaBuilder(new ReflectionClass($route->getRequestHandler()));
        $jsonSchema = $jsonSchemaBuilder->forResponseBody($response->getStatusCode(), 'application/json');
        if (null === $jsonSchema) {
            $this->fail('No JSON schema found.');
        }

        $jsonSchemaValidator = new JsonSchemaValidator();
        $jsonSchemaValidator->validate($data, $jsonSchema);
        if (false === $jsonSchemaValidator->isValid()) {
            $flags = JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE;
            $this->fail('Invalid body: ' . json_encode($jsonSchemaValidator->getErrors(), $flags));
        }

        $this->assertTrue(true);
    }
}
