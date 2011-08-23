<?php
class Wxr_Helper_GmtDate extends Zend_View_Helper_Abstract
{
    public function gmtDate($date)
    {
        $dt = new DateTime('@' . $date, new DateTimeZone('America/New_York'));
        $dt->setTimezone(new DateTimeZone('GMT'));
        return $dt->format('Y-m-d H:i:s');
    }
}
