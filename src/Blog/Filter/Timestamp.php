<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog\Filter;

use DateTime;
use MongoDate;
use Zend\Filter\FilterInterface;

class Timestamp implements FilterInterface
{
    public function filter($value) : int
    {
        if ($value instanceof DateTime) {
            $value = $value->getTimestamp();
        } elseif ($value instanceof MongoDate) {
            $value = $value->sec;
        } elseif (is_string($value) && ! is_numeric($value)) {
            $value = strtotime($value);
        } elseif (is_int($value) || (is_string($value) && is_numeric($value))) {
            if (is_string($value)) {
                $value = (int) $value;
            }
            $dt = new DateTime();
            $return = $dt->setTimestamp($value);
            if ($return->format('Y-m-d H:i:s') === $value) {
                $value = $_SERVER['REQUEST_TIME'];
            }
        } else {
            $value = $_SERVER['REQUEST_TIME'];
        }
        return (int) $value;
    }
}
