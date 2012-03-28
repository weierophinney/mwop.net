<?php
namespace PhlyBlog\Compiler;

class FileWriter implements WriterInterface
{
    public function write($filename, $data)
    {
        file_put_contents($filename, $data);
    }
}
