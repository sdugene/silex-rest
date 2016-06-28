<?php

namespace App\Services;

class EntityService
{
    protected $db;
    protected $route;

    public function __construct($db, $route)
    {
        $this->db = $db;
        $this->route = $route;
    }

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM ".$this->route['tableName']);
    }

    public function save($values)
    {
        $this->db->insert($this->route['tableName'], $values);
        return $this->db->lastInsertId();
    }

    public function update($id, $values)
    {
        return $this->db->update($this->route['tableName'], ['name'=> 'test'], ['id' => $id]);
    }

    public function delete($id)
    {
        return $this->db->delete($this->route['tableName'], array("id" => $id));
    }
}