<?php

namespace RulerZ\Compiler;

interface Filesystem
{
    public function has($filePath);
    public function write($filePath, $content);
}
