<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog\Filter;

use Zend\Filter\FilterInterface;

class Permalink implements FilterInterface
{
    public function filter(string $value) : string
    {
        $str = strtolower(trim($value));

        // replace all non valid characters and spaces with a dash
        $str = preg_replace('/[^a-z0-9_]/', '-', $str);
        $str = preg_replace('/-{2,}/', "-", $str);
        $str = trim($str, '-');
        $str = trim($str, '_');
        return $str;
    }
}
