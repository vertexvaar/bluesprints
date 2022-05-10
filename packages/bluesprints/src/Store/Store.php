<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Store;

use VerteXVaaR\BlueSprints\Store\Exception\ObjectNotFoundByUuidException;
use VerteXVaaR\BlueSprints\Store\Exception\ObjectReconstitutionException;

interface Store
{
    public function __construct(string $class, array $indices);

    /**
     * @param string $uuid
     * @return object
     * @throws ObjectNotFoundByUuidException
     * @throws ObjectReconstitutionException
     */
    public function findByUuid(string $uuid): object;
}
