<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Presentation\Base\Dtos;

use BeyondSEODeps\DDD\Infrastructure\Traits\Serializer\Attributes\HideProperty;
use BeyondSEODeps\Symfony\Component\HttpFoundation\Response;
use BeyondSEODeps\Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ImageResponseDto extends FileResponseDto
{
    /**
     * @var ResponseHeaderBag
     */
    #[HideProperty]
    public $headers;

    public function __construct(
        ?string $content = '',
        int $status = 200,
        array $headers = [],
        string $contentType = 'image/jpeg'
    ) {
        $headers = ['Pragma' => 'cache', 'Cache-Control' => 'public, max-age=15552000', 'Content-Type' => $contentType];
        parent::__construct($content, $status, $headers);
    }
}