<?php

namespace Giovani\DocumentationPlatformEngine\Documentation\Domain\ValueObjects;

use InvalidArgumentException;

class DocumentPath
{
    private string $value;

    public function __construct(string $path)
    {
        $path = trim($path);

        if ($path === '') {
            throw new InvalidArgumentException('Document path cannot be empty');
        }

        if (! str_ends_with($path, '.md')) {
            throw new InvalidArgumentException(
                "Documentation file must be markdown (.md): {$path}"
            );
        }

        $this->value = str_replace('\\', '/', $path);
    }

    public function filename(): string
    {
        return basename($this->value);
    }

    public function directory(): string
    {
        return dirname($this->value);
    }

    public function extension(): string
    {
        return pathinfo($this->value, PATHINFO_EXTENSION);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}