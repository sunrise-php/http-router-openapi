<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2019, Anatoly Fenric
 * @license https://github.com/sunrise-php/http-router-openapi/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-router-openapi
 */

namespace Sunrise\Http\Router\OpenApi;

/**
 * Import functions
 */
use function array_walk_recursive;
use function get_object_vars;
use function in_array;

/**
 * AbstractObject
 */
abstract class AbstractObject implements ObjectInterface
{

    /**
     * The fields specified in this array will not be returned when converting this object to an array
     *
     * @var string[]
     */
    protected const IGNORE_FIELDS = [];

    /**
     * The fields specified in this array will be renamed when converting this object to an array
     *
     * This constant must contain the following structure: [field => alias]
     *
     * @var array<string, string>
     */
    protected const FIELD_ALIASES = [];

    /**
     * Recursively converts the object to an array
     *
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        $fields = $this->getFields();

        array_walk_recursive($fields, function (&$value) {
            if ($value instanceof ObjectInterface) {
                $value = $value->toArray();
            }
        });

        return $fields;
    }

    /**
     * Gets all filled fields from the object
     *
     * @return array
     */
    protected function getFields() : array
    {
        $fields = [];
        $properties = get_object_vars($this);
        foreach ($properties as $name => $value) {
            // the property has no value or is null...
            if (null === $value) {
                continue;
            }

            // the property must be ignored...
            if (in_array($name, static::IGNORE_FIELDS)) {
                continue;
            }

            // the property must be renamed...
            if (isset(static::FIELD_ALIASES[$name])) {
                $name = static::FIELD_ALIASES[$name];
            }

            $fields[$name] = $value;
        }

        return $fields;
    }
}
