<?php

namespace BeyondSEODeps\Laminas\Code\Reflection\DocBlock\Tag;

use BeyondSEODeps\Laminas\Code\Generic\Prototype\PrototypeInterface;

interface TagInterface extends PrototypeInterface
{
    /**
     * @param  string $content
     * @return void
     */
    public function initialize($content);
}
