<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Presentation\Base\OpenApi\Pathes;

use BeyondSEODeps\DDD\Infrastructure\Exceptions\Exception;
use BeyondSEODeps\DDD\Infrastructure\Reflection\ClassWithNamespace;
use BeyondSEODeps\DDD\Infrastructure\Reflection\ReflectionClass;
use BeyondSEODeps\DDD\Infrastructure\Traits\Serializer\SerializerTrait;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\FileResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\HtmlResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Components\Schema;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Document;

class ResponseBodySchema
{
    use SerializerTrait;

    public array|Schema|null $schema = ['$ref' => ''];

    public function __construct(ReflectionClass &$responseDtoReflectionClass)
    {
        if (
            is_a($responseDtoReflectionClass->getName(), RestResponseDto::class, true) || is_a($responseDtoReflectionClass->getName(), Exception::class, true)
        ) {
            $classWithNamespace = new ClassWithNamespace($responseDtoReflectionClass->getName());
            // in case of request BODY we add schema with $ref to components
            // we are assuming here a complex potentially recoursive schema definition
            $this->schema['$ref'] = '#/components/schemas/' . $classWithNamespace->getNameWithNamespace('.');
            Document::getInstance()->components->addSchemaForClass($classWithNamespace, Parameter::RESPONSE);
        } elseif (is_a($responseDtoReflectionClass->getName(), FileResponseDto::class, true)) {
            $this->schema = new Schema();
            $this->schema->type = 'string';
            $this->schema->format = 'binary';
            $this->schema->buildSchema();
        } elseif (is_a($responseDtoReflectionClass->getName(), HtmlResponseDto::class, true)) {
            $this->schema = new Schema();
            $this->schema->type = 'string';
            $this->schema->buildSchema();
        }
    }
}