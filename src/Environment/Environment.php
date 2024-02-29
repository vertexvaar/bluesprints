<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Environment;

use function getenv;

final readonly class Environment
{
    public Context $context;

    public function __construct()
    {
        $this->context = Context::from((string)(getenv('APP_ENV')));
    }
}
