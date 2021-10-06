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
use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;
use Psr\Http\Message\ResponseInterface;
use Sunrise\Http\Router\OpenApi\OpenApi;

/**
 * Import functions
 */
use function class_exists;
use function json_encode;
use function json_decode;
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
 * OpenapiTestKit
 */
trait OpenapiTestKit
{

    /**
     * Gets Openapi instance
     *
     * @return OpenApi
     */
    abstract protected function getOpenapi() : OpenApi;

    /**
     * The assertion fails if the given response body doesn't
     * match a description of the operation identified by the given ID
     *
     * @param string $operationId
     * @param ResponseInterface $response
     *
     * @return void
     */
    protected function assertResponseBodyMatchesDescription(string $operationId, ResponseInterface $response) : void
    {
        if (!class_exists(Validator::class)) {
            // @codeCoverageIgnoreStart
            $this->markTestSkipped('To use Openapi Test Kit, install the "justinrainbow/json-schema".');
            // @codeCoverageIgnoreEnd
        }

        $statusCode = $response->getStatusCode();

        // https://tools.ietf.org/html/rfc7231#section-3.1.1.1
        $contentType = $response->getHeaderLine('Content-Type');
        if (false !== ($semicolon = strpos($contentType, ';'))) {
            $contentType = substr($contentType, 0, $semicolon);
        }

        $jsonSchema = $this->getOpenapi()->getResponseBodyJsonSchema($operationId, $statusCode, $contentType);
        if (null === $jsonSchema) {
            $this->fail('Undescribed response body.');
        }

        $json = (string) $response->getBody();
        if ('' === $json) {
            $this->fail('Empty response body.');
        }

        json_decode('');
        $data = json_decode($json, true);
        if (JSON_ERROR_NONE <> json_last_error()) {
            $this->fail('Undeserializable response body (' . json_last_error_msg() . ').');
        }

        $validator = new Validator();
        $validator->validate($data, $jsonSchema, Constraint::CHECK_MODE_TYPE_CAST);
        if (false === $validator->isValid()) {
            $flags = JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE;
            $this->fail("Invalid response body:\n" . json_encode($validator->getErrors(), $flags));
        }

        $this->assertTrue(true);
    }
}
