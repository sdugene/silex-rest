<?php

namespace App\Services;

class EntityService
{
    private $app;
    protected $db;
    protected $route;
    protected $joinedRoute = [];

    public function __construct($app, $db, $route)
    {
        $this->app = $app;
        $this->db = $db;
        $this->route = $route;
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM ".$this->route['tableName']." WHERE ".$this->route['idColumn']." = ?";
        return $this->db->fetchAssoc($sql, array((int) $id));
    }

    public function getByIdWithJoin($id, $join = null)
    {
        $sql = "SELECT ".$this->preprareColumns($join)." FROM ".$this->route['tableName']." ".$this->prepareJoin($join)." WHERE ".$this->route['tableName'].".".$this->route['idColumn']." = ?";
        return $this->fetchJoined($this->db->fetchAll($sql, array((int) $id)), $join);
    }

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM ".$this->route['tableName']);
    }

    public function getAllWithJoin($join = null)
    {
        $sql = "SELECT ".$this->preprareColumns($join)." FROM ".$this->route['tableName']." ".$this->prepareJoin($join);
        return $this->fetchJoined($this->db->fetchAll($sql), $join);
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

    public function searchWithJoin($criteria, $join = null)
    {
        if (!empty($criteria)) {
            $sql = "SELECT ".$this->preprareColumns($join)." FROM ".$this->route['tableName']." ".$this->prepareJoin($join)." WHERE ".$this->prepareSql($criteria);
            return $this->fetchJoined($this->db->fetchAll($sql, array_values($criteria)), $join);
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

    protected function prepareSql($criteria)
    {
        $keys = array_keys($criteria);
        foreach($keys as &$key) {
            $key = $this->route['tableName'] . '.' . $key . ' = ?';
        }
        return implode(' AND ',$keys);
    }

    protected function prepareJoin($join)
    {
        $sql_on = $this->route['tableName'].
            '.'.$this->route['foreignKeys'][$join]['column'].
            ' = '.$join.'.'.$this->route['foreignKeys'][$join]['referenced'];
        return "LEFT JOIN ".$join." ON ".$sql_on;
    }

    protected function preprareColumns($join = null)
    {
        $list = [];

        $tableColumns = array_merge([$this->route['idColumn']], $this->route['attributes']);
        foreach ($tableColumns as $column) {
            $list[] = $this->route['tableName'].'.'.$column.' as '.$column;
        }

        $joinedRoute = $this->getJoinedRoute($join);
        $joinedColumns = array_merge([$joinedRoute['idColumn']], $joinedRoute['attributes']);
        foreach ($joinedColumns as $column) {
            $list[] = $joinedRoute['tableName'].'.'.$column.' as '.$joinedRoute['tableName'].'_'.$column;
        }

        if (!empty($list)) {
            return implode(', ',$list);
        }
        return '*';
    }

    protected function getJoinedRoute($join)
    {
        if (empty($this->joinedRoute)) {
            $this->joinedRoute = [];
            foreach($this->app['routes.list'] as $route) {
                if ($route['tableName'] == $join) {
                    $this->joinedRoute = $route;
                    break;
                }
            }
        }
        return $this->joinedRoute;
    }

    protected function fetchJoined($array, $join = null)
    {
        $pattern = '/'.$join.'\_(.*)/';
        $joinedRoute = $this->getJoinedRoute($join);

        if (is_numeric(key($array))) {
            foreach ($array as &$line) {
                $line = $this->fetchJoined($line, $join);
            }
        } else {
            $joinArray = [];
            $id = $array[$joinedRoute['tableName'].'_'.$joinedRoute['idColumn']];
            foreach ($array as $key => $line) {
                if (preg_match($pattern, $key, $matches)) {
                    $joinArray[$id][$matches[1]] = $line;
                    unset($array[$key]);
                }
            }
            $array[$join] = $joinArray;
        }
        return $this->mergeResults($array, $join);
    }

    private function mergeResults($array, $join = null)
    {
        if (is_numeric(key($array))) {
            $resultsList = [];
            $resultsIds = [];
            $pos = 0;
            foreach($array as $result) {
                if (!in_array($result[$this->route['idColumn']], array_keys($resultsIds))) {
                    $resultsIds[$result[$this->route['idColumn']]] = $pos;
                    $resultsList[$pos] = $result;
                    $pos++;
                } else {
                    $newpos = $resultsIds[$result[$this->route['idColumn']]];
                    $resultsList[$newpos][$join][key($result[$join])] = $result[$join][key($result[$join])];
                }
            }
            if (!empty($resultsList)) {
                return $resultsList;
            }
        }
        return $array;
    }
}