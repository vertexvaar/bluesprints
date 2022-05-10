<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Store\Exception;

use Throwable;
use VerteXVaaR\BlueSprints\BluesprintsException;

class ObjectNotFoundByUuidException extends BluesprintsException
{
    public const CODE = 1642591100;

    private string $class;

    private string $uuid;

    public function __construct(string $class, string $uuid, Throwable $previous = null)
    {
        $this->class = $class;
        $this->uuid = $uuid;
        parent::__construct("The object $class $uuid does not exist", self::CODE, $previous);
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }
}
