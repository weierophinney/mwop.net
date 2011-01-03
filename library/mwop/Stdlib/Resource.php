<?php
namespace mwop\Stdlib;

interface Resource
{
    public function getAll();
    public function get($id);
    public function create($spec);
    public function update($id, $spec);
    public function delete($id);
    public function setDataSource(DataSource $data);
    public function getDataSource();
}
