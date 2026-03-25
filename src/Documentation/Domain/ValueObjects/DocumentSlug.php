<?php

namespace Giovani\DocumentationPlatformEngine\Documentation\Domain\ValueObjects;

use InvalidArgumentException;

class DocumentSlug
{
    private string $value;

    public function __construct(string $slug)
    {
        $slug = trim($slug);

        if ($slug === '') {
            throw new InvalidArgumentException('Document slug cannot be empty');
        }

        if (! preg_match('/^[a-z0-9\-\/]+$/', $slug)) {
            throw new InvalidArgumentException(
                "Invalid document slug: {$slug}"
            );
        }

        $this->value = strtolower($slug);
    }

    public static function fromPath(string $path): self
    {
        $filename = pathinfo($path, PATHINFO_FILENAME);

        $slug = strtolower(
            preg_replace('/[^a-z0-9]+/', '-', $filename)
        );

        return new self(trim($slug, '-'));
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
