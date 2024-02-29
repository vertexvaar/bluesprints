<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Mvcr\Model;

abstract class Entity
{
    public function __construct(public readonly string $identifier)
    {
    }
}
