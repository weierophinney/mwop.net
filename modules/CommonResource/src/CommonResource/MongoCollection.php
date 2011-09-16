<?php
namespace CommonResource;

class MongoCollection extends \MongoCollection
{
    public function __construct(\MongoDB $db, $name)
    {
        parent::__construct($db, $name);
    }
}
