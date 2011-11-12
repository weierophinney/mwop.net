<?php
namespace Wxr\Helper;

use DateTime,
    DateTimeZone,
    Zend\View\Helper\AbstractHelper;

class GmtDate extends AbstractHelper
{
    public function __invoke($date)
    {
        $dt = new DateTime('@' . $date, new DateTimeZone('America/New_York'));
        $dt->setTimezone(new DateTimeZone('GMT'));
        return $dt->format('Y-m-d H:i:s');
    }
}
