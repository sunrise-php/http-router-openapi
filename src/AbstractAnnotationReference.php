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
use Sunrise\Http\Router\OpenApi\Exception\InvalidReferenceException;

/**
 * Import functions
 */
use function hash;
use function sprintf;
use function class_exists;
use function method_exists;
use function property_exists;
use function get_called_class;

/**
 * AbstractAnnotationReference
 *
 * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#reference-object
 */
abstract class AbstractAnnotationReference implements ObjectInterface
{

    /**
     * Storage for referenced objects
     *
     * @var ComponentObjectInterface[]
     */
    private static $cache = [];

    /**
     * @Required
     *
     * @var string
     */
    public $class;

    /**
     * @var string
     */
    public $method;

    /**
     * @var string
     */
    public $property;

    /**
     * @var ComponentObjectInterface
     */
    private $referencedObject;

    /**
     * {@inheritDoc}
     */
    public function toArray() : array
    {
        // theoretically this condition will never be confirmed...
        if (null === $this->referencedObject) {
            return ['$ref' => 'undefined'];
        }

        return ['$ref' => sprintf(
            '#/components/%s/%s',
            $this->referencedObject->getComponentName(),
            $this->referencedObject->getReferenceName()
        )];
    }

    /**
     * The child class must return a class name that implements the `ComponentObjectInterface` interface
     *
     * @return string
     */
    abstract public function getAnnotationName() : string;

    /**
     * Tries to find a referenced object that implements the `ComponentObjectInterface` interface
     *
     * @param AnnotationReader $annotationReader
     * @param ReflectionClass $holder
     *
     * @return ComponentObjectInterface
     *
     * @throws InvalidReferenceException
     */
    public function getAnnotation(AnnotationReader $annotationReader, ReflectionClass $holder = null) : ComponentObjectInterface
    {
        if (false !== \strpos($this->class, '.')) {
            list($this->class, $this->property) = \explode('.', $this->class, 2);
        } elseif (false !== \strpos($this->class, '@')) {
            list($this->class, $this->method) = \explode('@', $this->class, 2);
        }

        if (isset($holder)) {
            if ('' === $this->class) {
                $this->class = $holder->getName();
            } elseif (false === \strpos($this->class, '\\') && $holder->getNamespaceName()) {
                $this->class = $holder->getNamespaceName() . '\\' . $this->class;
            }
        }

        $key = hash(
            'md5',
            $this->class .
            $this->method .
            $this->property .
            $this->getAnnotationName()
        );

        $this->referencedObject =& self::$cache[$key];

        if (isset($this->referencedObject)) {
            return $this->referencedObject;
        }

        if (isset($this->method)) {
            return $this->referencedObject = $this->getMethodAnnotation($annotationReader);
        }

        if (isset($this->property)) {
            return $this->referencedObject = $this->getPropertyAnnotation($annotationReader);
        }

        return $this->referencedObject = $this->getClassAnnotation($annotationReader);
    }

    /**
     * Proxy to `AnnotationReader::getMethodAnnotation()` with validation
     *
     * @param AnnotationReader $annotationReader
     *
     * @return ComponentObjectInterface
     *
     * @throws InvalidReferenceException
     *
     * @see AnnotationReader::getMethodAnnotation()
     */
    private function getMethodAnnotation(AnnotationReader $annotationReader) : ComponentObjectInterface
    {
        if (!method_exists($this->class, $this->method)) {
            $message = 'Annotation %s refers to non-existent method %s::%s()';
            throw new InvalidReferenceException(
                sprintf($message, get_called_class(), $this->class, $this->method)
            );
        }

        $reflection = new ReflectionMethod($this->class, $this->method);

        $object = $annotationReader->getMethodAnnotation($reflection, $this->getAnnotationName());

        if (null === $object) {
            $message = 'Method %s::%s() does not contain the annotation %s';
            throw new InvalidReferenceException(
                sprintf($message, $this->class, $this->method, $this->getAnnotationName())
            );
        }

        $object->_holder = $reflection->getDeclaringClass();

        if (null === $object->refName) {
            $object->refName = $reflection->getDeclaringClass()->getShortName() . '.fn_' . $reflection->getName();
        }

        return $object;
    }

    /**
     * Proxy to `AnnotationReader::getPropertyAnnotation()` with validation
     *
     * @param AnnotationReader $annotationReader
     *
     * @return ComponentObjectInterface
     *
     * @throws InvalidReferenceException
     *
     * @see AnnotationReader::getPropertyAnnotation()
     */
    private function getPropertyAnnotation(AnnotationReader $annotationReader) : ComponentObjectInterface
    {
        if (!property_exists($this->class, $this->property)) {
            $message = 'Annotation %s refers to non-existent property %s::$%s';
            throw new InvalidReferenceException(
                sprintf($message, get_called_class(), $this->class, $this->property)
            );
        }

        $reflection = new ReflectionProperty($this->class, $this->property);

        $object = $annotationReader->getPropertyAnnotation($reflection, $this->getAnnotationName());

        if (null === $object) {
            $message = 'Property %s::$%s does not contain the annotation %s';
            throw new InvalidReferenceException(
                sprintf($message, $this->class, $this->property, $this->getAnnotationName())
            );
        }

        $object->_holder = $reflection->getDeclaringClass();

        if (null === $object->refName) {
            $object->refName = $reflection->getDeclaringClass()->getShortName() . '.' . $reflection->getName();
        }

        return $object;
    }

    /**
     * Proxy to `AnnotationReader::getClassAnnotation()` with validation
     *
     * @param AnnotationReader $annotationReader
     *
     * @return ComponentObjectInterface
     *
     * @throws InvalidReferenceException
     *
     * @see AnnotationReader::getClassAnnotation()
     */
    private function getClassAnnotation(AnnotationReader $annotationReader) : ComponentObjectInterface
    {
        if (!class_exists($this->class)) {
            $message = 'Annotation %s refers to non-existent class %s';
            throw new InvalidReferenceException(
                sprintf($message, get_called_class(), $this->class)
            );
        }

        $reflection = new ReflectionClass($this->class);

        $object = $annotationReader->getClassAnnotation($reflection, $this->getAnnotationName());

        if (null === $object) {
            $message = 'Class %s does not contain the annotation %s';
            throw new InvalidReferenceException(
                sprintf($message, $this->class, $this->getAnnotationName())
            );
        }

        $object->_holder = $reflection;

        if (null === $object->refName) {
            $object->refName = $reflection->getShortName();
        }

        return $object;
    }
}
