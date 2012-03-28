<?php
namespace PhlyBlog\Compiler;

use DirectoryIterator,
    FilterIterator,
    InvalidArgumentException,
    Iterator,
    RecursiveDirectoryIterator,
    RecursiveIterator,
    RecursiveIteratorIterator,
    SplFileInfo;

/**
 * Usage:
 * 
 * <code>
 * $files = new PhpFileFilter($path);
 *
 * // or
 * $dir   = new DirectoryIterator($path);
 * $files = new PhpFileIterator($dir);
 *
 * // or
 * $dir   = new RecursiveDirectoryIterator($path);
 * $files = new PhpFileIterator($dir);
 * </code>
 */
class PhpFileFilter extends FilterIterator
{
    public function __construct($dirOrIterator = '.')
    {
        if (is_string($dirOrIterator)) {
            if (!is_dir($dirOrIterator)) {
                throw new InvalidArgumentException('Expected a valid directory name');
            }

            $dirOrIterator = new RecursiveDirectoryIterator($dirOrIterator);
        }
        if (!$dirOrIterator instanceof DirectoryIterator) {
            throw new InvalidArgumentException('Expected a DirectoryIterator');
        }

        if ($dirOrIterator instanceof RecursiveIterator) {
            $iterator = new RecursiveIteratorIterator($dirOrIterator);
        } else {
            $iterator = $dirOrIterator;
        }

        parent::__construct($iterator);
        $this->rewind();
    }

    public function accept()
    {
        $current = $this->getInnerIterator()->current();
        if (!$current instanceof SplFileInfo) {
            return false;
        }

        if (!$current->isFile()) {
            return false;
        }

        $ext = $current->getExtension();
        if ($ext != 'php') {
            return false;
        }

        return true;
    }
}

