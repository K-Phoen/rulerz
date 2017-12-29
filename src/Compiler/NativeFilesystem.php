<?php

namespace RulerZ\Compiler;

class NativeFilesystem implements Filesystem
{
    public function has($filePath)
    {
        return file_exists($filePath);
    }

    public function write($filePath, $content)
    {
        file_put_contents($filePath, $content, LOCK_EX);
    }
}
