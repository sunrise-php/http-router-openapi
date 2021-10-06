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
use Sunrise\Http\Router\OpenApi\Exception\InvalidReferenceException;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;

/**
 * Import functions
 */
use function hash;
use function sprintf;
use function class_exists;
use function method_exists;
use function property_exists;
use function get_called_class;
use function explode;
use function strpos;

/**
 * AbstractAnnotationReference
 *
 * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#reference-object
 */
abstract class AbstractAnnotationReference implements ObjectInterface
{

    /**
     * Cached annotations referenced by this annotation
     *
     * @var ComponentInterface[]
     */
    private static $annotations = [];

    /**
     * An object referenced by this annotation
     *
     * @var ComponentInterface|null
     */
    private $referencedObject = null;

    /**
     * A class name referenced by this annotation
     *
     * @Required
     *
     * @var string
     */
    public $class;

    /**
     * A class method referenced by this annotation
     *
     * @var string
     */
    public $method;

    /**
     * A class property referenced by this annotation
     *
     * @var string
     */
    public $property;

    /**
     * Gets an object referenced by this annotation
     *
     * @return ComponentInterface|null
     */
    public function getReferencedObject() : ?ComponentInterface
    {
        return $this->referencedObject;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        // It's impossible...
        if (null === $this->referencedObject) {
            // @codeCoverageIgnoreStart
            return ['$ref' => 'undefined'];
            // @codeCoverageIgnoreEnd
        }

        return ['$ref' => sprintf(
            '#/components/%s/%s',
            $this->referencedObject->getComponentName(),
            $this->referencedObject->getReferenceName()
        )];
    }

    /**
     * The child class must return a class name that implements the ComponentInterface interface
     *
     * @return string
     */
    abstract public function getAnnotationName() : string;

    /**
     * Tries to get an annotation referenced by this annotation and returns the result
     *
     * @param AnnotationReader $annotationReader
     * @param ReflectionClass|ReflectionMethod|ReflectionProperty|null $holder
     *
     * @return ComponentInterface
     *
     * @throws InvalidReferenceException
     */
    public function getAnnotation(AnnotationReader $annotationReader, ?Reflector $holder = null) : ComponentInterface
    {
        $this->normalizeSelf($holder);

        $key = hash(
            'md5',
            $this->class .
            $this->method .
            $this->property .
            $this->getAnnotationName()
        );

        $this->referencedObject =& self::$annotations[$key];

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
     * Tries to get an annotation referenced by this annotation through the class name and returns the result
     *
     * @param AnnotationReader $annotationReader
     *
     * @return ComponentInterface
     *
     * @throws InvalidReferenceException
     */
    private function getClassAnnotation(AnnotationReader $annotationReader) : ComponentInterface
    {
        if (!class_exists($this->class)) {
            throw new InvalidReferenceException(sprintf(
                'Annotation %s refers to non-existent class %s',
                get_called_class(),
                $this->class
            ));
        }

        $class = new ReflectionClass($this->class);

        $annotation = $annotationReader->getClassAnnotation($class, $this->getAnnotationName());

        if (null === $annotation) {
            throw new InvalidReferenceException(sprintf(
                'Class %s does not contain the annotation %s',
                $this->class,
                $this->getAnnotationName()
            ));
        }

        if (null === $annotation->refName) {
            $annotation->refName = $class->getShortName();
        }

        $annotation->setHolder($class);

        return $annotation;
    }

    /**
     * Tries to get an annotation referenced by this annotation through the class method and returns the result
     *
     * @param AnnotationReader $annotationReader
     *
     * @return ComponentInterface
     *
     * @throws InvalidReferenceException
     */
    private function getMethodAnnotation(AnnotationReader $annotationReader) : ComponentInterface
    {
        if (!method_exists($this->class, $this->method)) {
            throw new InvalidReferenceException(sprintf(
                'Annotation %s refers to non-existent method %s::%s()',
                get_called_class(),
                $this->class,
                $this->method
            ));
        }

        $method = new ReflectionMethod($this->class, $this->method);

        $annotation = $annotationReader->getMethodAnnotation($method, $this->getAnnotationName());

        if (null === $annotation) {
            throw new InvalidReferenceException(sprintf(
                'Method %s::%s() does not contain the annotation %s',
                $this->class,
                $this->method,
                $this->getAnnotationName()
            ));
        }

        if (null === $annotation->refName) {
            $annotation->refName = $method->getDeclaringClass()->getShortName() . '.' . $method->getName();
        }

        $annotation->setHolder($method);

        return $annotation;
    }

    /**
     * Tries to get an annotation referenced by this annotation through the class property and returns the result
     *
     * @param AnnotationReader $annotationReader
     *
     * @return ComponentInterface
     *
     * @throws InvalidReferenceException
     */
    private function getPropertyAnnotation(AnnotationReader $annotationReader) : ComponentInterface
    {
        if (!property_exists($this->class, $this->property)) {
            throw new InvalidReferenceException(sprintf(
                'Annotation %s refers to non-existent property %s::$%s',
                get_called_class(),
                $this->class,
                $this->property
            ));
        }

        $property = new ReflectionProperty($this->class, $this->property);

        $annotation = $annotationReader->getPropertyAnnotation($property, $this->getAnnotationName());

        if (null === $annotation) {
            throw new InvalidReferenceException(sprintf(
                'Property %s::$%s does not contain the annotation %s',
                $this->class,
                $this->property,
                $this->getAnnotationName()
            ));
        }

        if (null === $annotation->refName) {
            $annotation->refName = $property->getDeclaringClass()->getShortName() . '.' . $property->getName();
        }

        $annotation->setHolder($property);

        return $annotation;
    }

    /**
     * Normalizes the reference
     *
     * @param ReflectionClass|ReflectionMethod|ReflectionProperty|null $holder
     *
     * @return void
     */
    private function normalizeSelf(?Reflector $holder = null) : void
    {
        // e.g. SomeReference("App\Class@method")
        if (false !== strpos($this->class, '@')) {
            list($this->class, $this->method) = explode('@', $this->class, 2);

        // e.g. SomeReference("App\Class.property")
        } elseif (false !== strpos($this->class, '.')) {
            list($this->class, $this->property) = explode('.', $this->class, 2);
        }

        // It's impossible...
        if (null === $holder) {
            // @codeCoverageIgnoreStart
            return;
            // @codeCoverageIgnoreEnd
        }

        $refClass = ($holder instanceof ReflectionClass) ? $holder : $holder->getDeclaringClass();

        // When a property or method only was set,
        // e.g. SomeReference(".property")
        if ('' === $this->class) {
            $this->class = $refClass->getName();

        // When an unnamespaced class was set,
        // e.g. SomeReference("Class.property")
        } elseif (false === strpos($this->class, '\\')) {
            $this->class = $refClass->getNamespaceName() . '\\' . $this->class;
        }
    }
}
