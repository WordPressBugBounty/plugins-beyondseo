<?php

declare(strict_types=1);

namespace BeyondSEO\Presentation\Api\Documentation\Dtos;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Infrastructure\Traits\Serializer\Attributes\ExposePropertyInsteadOfClass;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\RequestDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

#[ExposePropertyInsteadOfClass('document')]
class DocumentationOpenApiRequestDto extends RequestDto
{
    /**
     * @var bool If true, Schema Tags are ommited:
     * Schema Tags are usefull: on Documentation Platofrms such as redocly to document all Entity / DTO schemas
     * Schema Tags are not usefull: On Postman, if you want to use a tag based organisation structure, as it will create empty folders for all schema tags
     */
    #[Parameter(in: Parameter::QUERY, required: false)]
    public ?bool $removeSchemaTags = false;
}