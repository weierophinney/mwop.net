<?php
namespace Blog\Compiler;

interface WriterInterface
{
    public function write($filename, $data);
}
