<?php
namespace Mwop\Blog;

use DirectoryIterator;
use FilterIterator;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Usage:
 *
 * <code>
 * $files = new MarkdownFileFilter($path);
 *
 * // or
 * $dir   = new DirectoryIterator($path);
 * $files = new MarkdownFileIterator($dir);
 *
 * // or
 * $dir   = new RecursiveDirectoryIterator($path);
 * $files = new MarkdownFileIterator($dir);
 * </code>
 */
class MarkdownFileFilter extends FilterIterator
{
    public function __construct(string $dirOrIterator = '.')
    {
        if (is_string($dirOrIterator)) {
            if (! is_dir($dirOrIterator)) {
                throw new InvalidArgumentException('Expected a valid directory name');
            }

            $dirOrIterator = new RecursiveDirectoryIterator($dirOrIterator);
        }
        if (! $dirOrIterator instanceof DirectoryIterator) {
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

    public function accept() : bool
    {
        $current = $this->getInnerIterator()->current();
        if (! $current instanceof SplFileInfo) {
            return false;
        }

        if (! $current->isFile()) {
            return false;
        }

        $ext = $current->getExtension();
        if ($ext != 'md') {
            return false;
        }

        return true;
    }
}
