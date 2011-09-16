<?php
namespace CommonResource;

class Mongo extends \Mongo
{
    public function __construct($server = 'mongodb://localhost:27017', array $options = array('connect' => TRUE))
    {
        parent::__construct($server, $options);
    }
}
