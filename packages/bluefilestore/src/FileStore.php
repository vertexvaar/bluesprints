<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueFileStore;

use Exception;
use ReflectionClass;
use Throwable;
use VerteXVaaR\BlueSprints\Store\Exception\ObjectNotFoundByUuidException;
use VerteXVaaR\BlueSprints\Store\Exception\ObjectReconstitutionException;
use VerteXVaaR\BlueSprints\Store\Store;

use function array_keys;
use function array_values;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function hash;
use function json_decode;
use function json_encode;
use function ksort;
use function natsort;
use function property_exists;
use function serialize;
use function str_replace;
use function unserialize;

use const DIRECTORY_SEPARATOR as DS;
use const JSON_THROW_ON_ERROR;

class FileStore implements Store
{
    private const STORE_FOLDER = 'database';

    protected array $indices;

    private string $class;

    private array $classProperties;

    private string $folder;

    public function __construct(string $class, array $indices)
    {
        $this->class = $class;

        $reflection = new ReflectionClass($this->class);
        $reflectionProperties = $reflection->getProperties();
        $properties = [];
        foreach ($reflectionProperties as $reflectionProperty) {
            $properties[] = $reflectionProperty->getName();
        }
        natsort($properties);
        $this->classProperties = $properties;

        $this->folder = self::STORE_FOLDER . DS . str_replace('\\', DS, $this->class) . DS;

        $indicesList = [];
        foreach ($indices as $index) {
            foreach ($index as $property) {
                if (!property_exists($this->class, $property)) {
                    throw new Exception("Property $property not found in class $this->class", 1642592800);
                }
            }
            ksort($index);
            $hash = hash('sha265', json_encode($index, JSON_THROW_ON_ERROR));
            $indicesList[$hash] = $index;
        }
        $this->indices = $indicesList;
    }

    public function findByUuid(string $uuid): object
    {
        $file = $this->folder . $uuid;
        if (!file_exists($file)) {
            throw new ObjectNotFoundByUuidException($this->class, $uuid);
        }
        $fileContents = file_get_contents($file);
        try {
            return unserialize($fileContents, ['allowed_classes' => $this->class]);
        } catch (Throwable $exception) {
            throw new ObjectReconstitutionException($this->class, $uuid, $exception);
        }
    }

    public function findByProperties(array $properties): array
    {
        ksort($properties);
        $hash = hash('sha265', json_encode($properties, JSON_THROW_ON_ERROR));
        if (isset($this->indices[$hash])) {
            $indicesFile = $this->folder . 'Indices' . DS . $hash;
            if (!file_exists($indicesFile)) {
                file_put_contents($indicesFile, json_encode([], JSON_THROW_ON_ERROR));
            }
            $index = json_decode(file_get_contents($indicesFile), null, JSON_THROW_ON_ERROR);

            $valueHash = json_encode(array_values($properties), JSON_THROW_ON_ERROR);
            if (isset($index[$valueHash])) {
                $objects = [];
                foreach ($index[$valueHash] as $uuid) {
                    try {
                        $objects[] = $this->findByUuid($uuid);
                    } catch (ObjectNotFoundByUuidException $exception) {
                        // ignore
                    }
                }
                return $objects;
            }
        }
    }

    private function getIndices(): array
    {
        $indicesFile = $this->folder . 'Indices';
        if (!file_exists($indicesFile)) {
            file_put_contents($indicesFile, serialize([]));
            return [];
        }
        return unserialize(file_get_contents($indicesFile), ['allowed_classes' => false]);
    }
}
