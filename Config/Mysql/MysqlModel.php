<?php

namespace Config\Mysql;
use Config\Mysql\MysqlDatabase;

class MysqlModel
{
    protected $db;
    protected $table;

    public function __construct()
    {
        $this->db = new MysqlDatabase();
    }

    public function connect()
    {
        $this->db->connect();
    }

    public function disconnect()
    {
        $this->db->disconnect();
    }

    public function beginTransaction()
    {
        $this->db->beginTransaction();
    }

    public function commit()
    {
        $this->db->commit();
    }

    public function rollback()
    {
        $this->db->rollback();
    }

    public function find($id)
    {
        $result = $this->db->selectOne($this->table, "*", "id = ?", [$id]);
        return $result;
    }

    public function findAll()
    {
        try {
            $result = $this->db->select($this->table);

            if (!empty($result)) {
                return $result;
            } else {
                throw new \Exception("Nenhum resultado encontrado.");
            }
        } catch (\Exception $e) {
            throw new \Exception("Erro na consulta: " . $e->getMessage());
        }
    }

    public function create($data)
    {
        $result = $this->db->insert($this->table, $data);

        if ($this->db->getLastInsertId() !== null) {
            return $this->db->getLastInsertId();
        }

        return null;
    }

    public function update($id, $data)
    {
        $result = $this->db->update($this->table, $data, "id = ?", [$id]);
        return $result;
    }

    public function delete($id)
    {
        $result = $this->db->delete($this->table, "id = ?", [$id]);
        return $result;
    }

    public function count($where = '', $params = [])
    {
        return $this->db->count($this->table, $where, $params);
    }
}