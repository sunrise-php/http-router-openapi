<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2019, Anatoly Fenric
 * @license https://github.com/sunrise-php/http-router-openapi/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-router-openapi
 */

namespace Sunrise\Http\Router\OpenApi\Command;

/**
 * Import classes
 */
use RuntimeException;
use Sunrise\Http\Router\OpenApi\OpenApi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Import functions
 */
use function json_encode;
use function sprintf;

/**
 * Import constants
 */
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

/**
 * This command generates JSON schema
 *
 * If you cannot pass the openapi to the constructor,
 * or your architecture has problems with autowiring,
 * then inherit this class and override the getOpenapi method.
 *
 * @since 2.0.0
 */
class GenerateJsonSchemaCommand extends Command
{

    /**
     * {@inheritdoc}
     */
    protected static $defaultName = 'router:generate-json-schema';

    /**
     * {@inheritdoc}
     */
    protected static $defaultDescription = 'Generates JSON schema';

    /**
     * The openapi instance
     *
     * @var OpenApi|null
     */
    private $openapi;

    /**
     * Constructor of the class
     *
     * @param OpenApi|null $openapi
     */
    public function __construct(?OpenApi $openapi = null)
    {
        $this->openapi = $openapi;

        parent::__construct();

        $this->setName(static::$defaultName);
        $this->setDescription(static::$defaultDescription);

        $this->addArgument(
            'operation-id',
            InputArgument::REQUIRED,
            'Operation ID (aka route name)'
        );

        $this->addArgument(
            'operation-section',
            InputArgument::REQUIRED,
            'Operation section ("cookie", "header", "query", "body")'
        );

        $this->addOption(
            'content-type',
            null,
            InputOption::VALUE_REQUIRED,
            'Content type of the body section (e.g. "application/json")',
            'application/json'
        );
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
     */
    final protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $openapi = $this->getOpenapi();
        $operationId = $input->getArgument('operation-id');
        $operationSection = $input->getArgument('operation-section');

        switch ($operationSection) {
            case 'cookie':
                $jsonSchema = $openapi->getRequestCookieJsonSchema($operationId);
                break;
            case 'header':
                $jsonSchema = $openapi->getRequestHeaderJsonSchema($operationId);
                break;
            case 'query':
                $jsonSchema = $openapi->getRequestQueryJsonSchema($operationId);
                break;
            case 'body':
                $jsonSchema = $openapi->getRequestBodyJsonSchema($operationId, $input->getOption('content-type'));
                break;
            default:
                $output->writeln('<error>Unknown operation section ("cookie", "header", "query", "body")</error>');
                return 1;
        }

        if (!isset($jsonSchema)) {
            $output->writeln('<comment>Not enough data to build JSON schema</comment>');
            return 1;
        }

        $output->writeln(json_encode($jsonSchema, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
        return 0;
    }
}
