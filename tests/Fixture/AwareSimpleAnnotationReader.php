<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Fixture;

/**
 * Import classes
 */
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Sunrise\Http\Router\OpenApi\OpenApi;

/**
 * AwareSimpleAnnotationReader
 */
trait AwareSimpleAnnotationReader
{

    /**
     * @return SimpleAnnotationReader
     */
    private function createSimpleAnnotationReader() : SimpleAnnotationReader
    {
        $annotationReader = new SimpleAnnotationReader();
        $annotationReader->addNamespace(OpenApi::ANNOTATIONS_NAMESPACE);

        return $annotationReader;
    }
}
