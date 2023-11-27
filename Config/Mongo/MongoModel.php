<?php

namespace Config\Mongo;

use Config\Mongo\MongoDatabase;
use Exception;

class MongoModel
{
    protected MongoDatabase $db;

    public function __construct()
    {
        $this->db = new MongoDatabase();
    }

    public function connect()
    {
        $this->db->connect();
    }

    public function __destruct()
    {
        $this->db->disconnect();
    }

    public function insertData($collectionName, $data)
    {
        $collection = $this->db->getCollection($collectionName);
        $result = $this->db->insert($collection, $data);

        if ($result) {
            return $result;
        } else {
            throw new Exception("Erro ao inserir documento: não foi possível obter o _id do documento inserido.");
        }
    }

    public function insertAndGetId($collectionName, $data)
    {
        $collection = $this->db->getCollection($collectionName);
        $result = $this->db->insert($collection, $data);

        if ($result) {
            return $result->getInsertedId();
        } else {
            throw new Exception("Erro ao inserir documento: não foi possível obter o _id do documento inserido.");
        }
    }

    public function updateData($collectionName, $filter, $data)
    {
        $collection = $this->db->getCollection($collectionName);
        return $this->db->update($collection, $filter, $data);
    }

    public function deleteData($collectionName, $filter)
    {
        $collection = $this->db->getCollection($collectionName);
        return $this->db->delete($collection, $filter);
    }

    public function queryData($collectionName, $filter, $options = [])
    {
        $collection = $this->db->getCollection($collectionName);
        return $this->db->query($collection, $filter, $options);
    }

    public function countData($collectionName, $filter)
    {
        $collection = $this->db->getCollection($collectionName);
        return $this->db->count($collection, $filter);
    }

    public function getInsertedId()
    {
        return $this->db->getInsertedId();
    }
    public function createObjectId($id = null)
    {
        return $this->db->createObjectId($id);
    }

    public function findAllData($collectionName, $filter, $options = [])
    {
        $collection = $this->db->getCollection($collectionName);
        return $this->db->select($collection, $filter, $options);
    }
    
    public function findOneData($collectionName, $filter = [], $options = [])
    {
        $collection = $this->db->getCollection($collectionName);
        return $this->db->query($collection, $filter, $options);
    }
    

    public function findAndSortData($collectionName, $filter, $sort, $options = [])
    {
        $collection = $this->db->getCollection($collectionName);
        $options['sort'] = $sort;
        return $this->db->select($collection, $filter, $options);
    }

    public function findOneDataById($collectionName, $id)
    {
        $collection = $this->db->getCollection($collectionName);
        $filter = ['_id' => $this->db->createObjectId($id)];
        return $this->db->query($collection, $filter);
    }
}
