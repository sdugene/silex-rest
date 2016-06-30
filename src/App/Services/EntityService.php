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

    public function getById($id)
    {
        $sql = "SELECT * FROM ".$this->route['tableName']." WHERE id = ?";
        return $this->db->fetchAssoc($sql, array((int) $id));
    }

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM ".$this->route['tableName']);
    }

    public function save($values)
    {
        if (!empty($values)) {
            $this->db->insert($this->route['tableName'], $values);
            return $this->db->lastInsertId();
        }
    }
    
    public function search($criteria)
    {
        if (!empty($criteria)) {
            $sql = "SELECT * FROM ".$this->route['tableName']." WHERE ".$this->prepareSql($criteria);
            return $this->db->fetchAll($sql, array_values($criteria));
        }
    }

    public function update($id, $values)
    {
        if (!empty($values)) {
            return $this->db->update($this->route['tableName'], $values, ['id' => $id]);
        } else {
            return false;
        }
    }

    public function delete($id)
    {
        return $this->db->delete($this->route['tableName'], array("id" => $id));
    }

    private function prepareSql($criteria)
    {
        $keys = array_keys($criteria);
        foreach($keys as &$key) {
            $key .= ' = ?';
        }
        return implode(' AND ',$keys);
    }
}