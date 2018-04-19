<?php

declare(strict_types=1);

namespace RulerZ\Compiler;

class NativeFilesystem implements Filesystem
{
    public function has(string $filePath): bool
    {
        return file_exists($filePath);
    }

    public function write(string $filePath, string $content): void
    {
        file_put_contents($filePath, $content, LOCK_EX);
    }
}
