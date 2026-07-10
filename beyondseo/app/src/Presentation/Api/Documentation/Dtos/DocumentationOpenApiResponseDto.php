<?php

declare(strict_types=1);

namespace BeyondSEO\Presentation\Api\Documentation\Dtos;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Infrastructure\Traits\Serializer\Attributes\ExposePropertyInsteadOfClass;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Document;

#[ExposePropertyInsteadOfClass('document')]
class DocumentationOpenApiResponseDto extends RestResponseDto
{
    public ?Document $document = null;
}