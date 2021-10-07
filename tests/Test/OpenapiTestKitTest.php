<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Test;

/**
 * Import classes
 */
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Factory\ResponseFactory;
use Sunrise\Http\Router\OpenApi\Test\OpenapiTestKit;
use Sunrise\Http\Router\OpenApi\Tests\Fixtures\OpenapiAwareTrait;

/**
 * OpenapiTestKitTest
 */
class OpenapiTestKitTest extends TestCase
{
    use OpenapiAwareTrait;
    use OpenapiTestKit;

    /**
     * @return void
     */
    public function testUndescribedBody() : void
    {
        $response = (new ResponseFactory)->createResponse(200);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Undescribed response body.');

        $this->assertResponseBodyMatchesDescription('users.read', $response);
    }

    /**
     * @return void
     */
    public function testEmptyBody() : void
    {
        $response = (new ResponseFactory)->createResponse(200)
            ->withHeader('Content-Type', 'application/json');

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Empty response body.');

        $this->assertResponseBodyMatchesDescription('users.read', $response);
    }

    /**
     * @return void
     */
    public function testUndeserializableBody() : void
    {
        $response = (new ResponseFactory)->createResponse(200)
            ->withHeader('Content-Type', 'application/json');

        $response->getBody()->write('!');

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Undeserializable response body (Syntax error).');

        $this->assertResponseBodyMatchesDescription('users.read', $response);
    }

    /**
     * @return void
     */
    public function testInvalidBody() : void
    {
        $response = (new ResponseFactory)->createJsonResponse(200, [
            'email' => '@',
        ]);

        $this->expectException(AssertionFailedError::class);
        // $this->expectExceptionMessageMatches('/^Invalid response body:$/m');

        $this->assertResponseBodyMatchesDescription('users.read', $response);
    }

    /**
     * @return void
     */
    public function testValidBody() : void
    {
        $response = (new ResponseFactory)->createJsonResponse(200, [
            'email' => 'foo@acme.com',
            'password' => 'P@$$w0rd',
        ]);

        $this->assertResponseBodyMatchesDescription('users.read', $response);
    }
}
