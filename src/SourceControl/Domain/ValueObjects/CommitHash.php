<?php

namespace Giovani\DocumentationPlatformEngine\SourceControl\Domain\ValueObjects;

use InvalidArgumentException;

class CommitHash
{
    public function __construct(
        private string $value
    ) {
        if (! preg_match('/^[a-f0-9]{7,40}$/', $value)) {
            throw new InvalidArgumentException("Invalid commit hash");
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}