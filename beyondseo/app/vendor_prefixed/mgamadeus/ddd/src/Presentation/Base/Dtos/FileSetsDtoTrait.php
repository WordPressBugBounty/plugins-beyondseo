<?php

namespace BeyondSEODeps\DDD\Presentation\Base\Dtos;

use BeyondSEODeps\DDD\Domain\Common\Entities\Files\Files;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

trait FileSetsDtoTrait
{
    /** @var object[] The list of uploaded files */
    #[Parameter(in: Parameter::FILES, required: true)]
    public array $fileList;

    /**
     * @return Files
     */
    public function getFiles(): Files
    {
        return new Files($this->fileList);
    }
}
