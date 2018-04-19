<?php

declare(strict_types=1);

namespace RulerZ\Compiler;

interface Filesystem
{
    public function has(string $filePath): bool;

    public function write(string $filePath, string $content): void;
}
