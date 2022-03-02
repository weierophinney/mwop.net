<?php

declare(strict_types=1);

namespace Mwop\Art;

class UploadPhotoResult
{
    private ?string $filename = null;
    private ?string $error = null;

    private function __construct()
    {
    }

    public static function fromError(string $error): self
    {
        $instance = new self();
        $instance->error = $error;
        return $instance;
    }

    public static function withFilename(string $filename): self
    {
        $instance = new self();
        $instance->filename = $filename;
        return $instance;
    }

    public function isError(): bool
    {
        return (null !== $this->error);
    }

    public function getError(): string
    {
        return $this->error ?? '';
    }

    public function filename(): ?string
    {
        return $this->filename;
    }
}
