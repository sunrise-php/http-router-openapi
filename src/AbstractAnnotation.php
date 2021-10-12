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
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;

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
     * The annotation holder
     *
     * @var ReflectionClass|ReflectionMethod|ReflectionProperty|null
     */
    private $holder = null;

    /**
     * Objects referenced by this annotation
     *
     * @var ComponentInterface[]
     */
    private $referencedObjects = [];

    /**
     * Sets the annotation holder
     *
     * @param ReflectionClass|ReflectionMethod|ReflectionProperty|null $holder
     *
     * @return void
     */
    public function setHolder(?Reflector $holder) : void
    {
        $this->holder = $holder;
    }

    /**
     * Gets objects referenced by this annotation
     *
     * @return ComponentInterface[]
     */
    public function getReferencedObjects() : array
    {
        return $this->referencedObjects;
    }

    /**
     * Recursively collects objects referenced by this annotation and returns the result
     *
     * @param AnnotationReader $annotationReader
     *
     * @return ComponentInterface[]
     */
    public function collectReferencedObjects(AnnotationReader $annotationReader) : array
    {
        $fields = $this->getFields();
        $referencedObjects = [];

        array_walk_recursive($fields, function ($value) use ($annotationReader, &$referencedObjects) {
            if ($value instanceof AbstractAnnotation) {
                // passing the annotation holder to all child items...
                $value->holder = $this->holder;

                $referencedObjects = array_merge(
                    $referencedObjects,
                    $value->collectReferencedObjects($annotationReader)
                );
            } elseif ($value instanceof AbstractAnnotationReference) {
                $referencedObject = $value->getAnnotation($annotationReader, $this->holder);
                $referencedObjects[] = $referencedObject;

                if ($referencedObject instanceof AbstractAnnotation) {
                    $referencedObjects = array_merge(
                        $referencedObjects,
                        $referencedObject->collectReferencedObjects($annotationReader)
                    );
                }
            }
        });

        $this->referencedObjects = $referencedObjects;

        return $this->referencedObjects;
    }

    /**
     * Serializes the object
     *
     * @return array
     */
    public function __serialize() : array
    {
        // reflector can't be serialized...
        $this->holder = null;

        $data = [];
        foreach ($this as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * Unserializes the object
     *
     * @param array $data
     *
     * @return void
     */
    public function __unserialize(array $data) : void
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}
