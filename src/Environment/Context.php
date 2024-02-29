<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Environment;

enum Context: string
{
    case Production = 'prod';
    case Testing = 'test';
    case Development = 'dev';
}
