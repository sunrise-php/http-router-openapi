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
 * Import classes
 */
use Doctrine\Common\Annotations\Reader as AnnotationReader;

/**
 * Import functions
 */
use function array_merge;
use function array_walk_recursive;

/**
 * AbstractAnnotation
 */
abstract class AbstractAnnotation extends AbstractObject
{

    /**
     * @var \ReflectionClass
     */
    public $_holder = null;

    /**
     * Recursively collects all annotations referenced by this object or its children
     *
     * @param AnnotationReader $annotationReader
     *
     * @return ComponentObjectInterface[]
     */
    public function getReferencedObjects(AnnotationReader $annotationReader) : array
    {
        $fields = $this->getFields();
        $objects = [];

        array_walk_recursive($fields, function ($value) use ($annotationReader, &$objects) {
            if ($value instanceof AbstractAnnotation) {
                $objects = array_merge($objects, $value->getReferencedObjects($annotationReader));
            } elseif ($value instanceof AbstractAnnotationReference) {
                $object = $value->getAnnotation($annotationReader, $this->_holder);
                $objects[] = $object;

                if ($object instanceof AbstractAnnotation) {
                    $objects = array_merge($objects, $object->getReferencedObjects($annotationReader));
                }
            }
        });

        return $objects;
    }
}
