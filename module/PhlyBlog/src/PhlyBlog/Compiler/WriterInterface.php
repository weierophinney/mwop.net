<?php
namespace PhlyBlog\Compiler;

interface WriterInterface
{
    public function write($filename, $data);
}
