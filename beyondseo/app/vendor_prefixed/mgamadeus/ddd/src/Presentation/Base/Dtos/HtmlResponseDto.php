<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Presentation\Base\Dtos;

use BeyondSEODeps\DDD\Infrastructure\Traits\Serializer\Attributes\HideProperty;
use BeyondSEODeps\Symfony\Component\HttpFoundation\Response;
use BeyondSEODeps\Symfony\Component\HttpFoundation\ResponseHeaderBag;

class HtmlResponseDto extends Response
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
        string $contentType = 'text/html'
    ) {
        $headers = ['Content-Type' => $contentType];
        parent::__construct($content, $status, $headers);
    }

    /**
     * Sends content for the current web response.
     *
     * @return $this
     */
    public function sendContent(): static
    {
        echo $this->getContent();

        return $this;
    }
}