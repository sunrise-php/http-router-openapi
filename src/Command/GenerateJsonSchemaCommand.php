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

/**
 * Import constants
 */
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

/**
 * GenerateJsonSchemaCommand
 */
final class GenerateJsonSchemaCommand extends Command
{

    /**
     * Openapi instance
     *
     * @var OpenApi
     */
    private $openapi;

    /**
     * {@inheritdoc}
     *
     * @param OpenApi $openapi
     * @param string|null $name
     */
    public function __construct(OpenApi $openapi, ?string $name = null)
    {
        $this->openapi = $openapi;

        parent::__construct($name ?? 'router:generate-json-schema');

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
     * {@inheritdoc}
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int Exit code
     */
    public function execute(InputInterface $input, OutputInterface $output) : int
    {
        $operationId = $input->getArgument('operation-id');
        $operationSection = $input->getArgument('operation-section');
        $contentType = $input->getOption('content-type');

        switch ($operationSection) {
            case 'cookie':
                $output->writeln($this->jsonify($this->openapi->getRequestCookieJsonSchema($operationId)));
                return 0;
            case 'header':
                $output->writeln($this->jsonify($this->openapi->getRequestHeaderJsonSchema($operationId)));
                return 0;
            case 'query':
                $output->writeln($this->jsonify($this->openapi->getRequestQueryJsonSchema($operationId)));
                return 0;
            case 'body':
                $output->writeln($this->jsonify($this->openapi->getRequestBodyJsonSchema($operationId, $contentType)));
                return 0;
            default:
                $output->writeln('<error>Unknown operation section ("cookie", "header", "query", "body")</error>');
                return 1;
        }
    }

    /**
     * Jsonifies the given data and returns the result
     *
     * @param array|null $data
     *
     * @return string
     */
    private function jsonify(?array $data) : string
    {
        return json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
    }
}
