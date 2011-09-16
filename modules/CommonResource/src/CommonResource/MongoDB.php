<?php
namespace CommonResource;

class MongoDB extends \MongoDB
{
    public function __construct(\Mongo $conn, $name)
    {
        parent::__construct($conn, $name);
    }
}
