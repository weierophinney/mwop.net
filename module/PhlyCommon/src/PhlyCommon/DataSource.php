<?php
namespace PhlyCommon;

interface DataSource
{
    public function query(Query $query);
    public function get($id);
    public function create(array $definition);
    public function update($id, array $fields);
    public function delete($id);
}
