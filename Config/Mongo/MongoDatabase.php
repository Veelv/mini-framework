<?php

namespace Config\Mongo;

use Config\EnvLoader;
use Exception;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\BSON\ObjectID;
use Predis\Connection\ConnectionException;

class MongoDatabase
{
    private string $hostname;
    private string $database;
    private string $username;
    private string $password;
    private $env;
    private ?Client $conn;
    private ?Collection $collection;
    private ?ObjectID $lastInsertedId;

    public function __construct()
    {
        $this->env = new EnvLoader(__DIR__ . '/../../.env');
        $this->hostname = $this->env->get('MONGOHOST');
        $this->database = $this->env->get('MONGODATABASE');
        $this->username = $this->env->get('MONGOUSERNAME');
        $this->password = $this->env->get('MONGOPASSWORD');
        $this->conn = null;
        $this->collection = null;
        $this->lastInsertedId = null;
    }

    public function connect()
    {
        $uri = "mongodb+srv://{$this->username}:{$this->password}@{$this->hostname}/{$this->database}?retryWrites=true&w=majority";
        try {
            $this->conn = new Client($uri);
        } catch (ConnectionException $e) {
            throw new Exception("Erro ao conectar ao banco de dados: " . $e->getMessage());
        }
    }

    public function disconnect()
    {
        $this->conn = null;
    }

    public function getCollection($collectionName): Collection
    {
        if (!$this->conn) {
            throw new Exception("Conexão com o banco de dados não estabelecida.");
        }

        return $this->conn->{$this->database}->{$collectionName};
    }

    public function insert(Collection $collection, $data)
    {
        try {
            $result = $collection->insertOne($data);
            $this->lastInsertedId = $result->getInsertedId();
            $insertedDocument = $collection->findOne(['_id' => $this->lastInsertedId]); // Buscar o documento inserido
            return $insertedDocument;
        } catch (Exception $e) {
            throw new Exception("Erro ao inserir documento: " . $e->getMessage());
        }
    }

    public function update(Collection $collection, $filter, $data)
    {
        try {
            $result = $collection->updateOne($filter, ['$set' => $data]);
            $updatedDocument = $collection->findOne($filter); // Buscar o documento atualizado
            return $updatedDocument;
        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar documento: " . $e->getMessage());
        }
    }

    public function delete(Collection $collection, $filter)
    {
        try {
            $deletedCount = $collection->deleteOne($filter)->getDeletedCount();
            if ($deletedCount > 0) {
                return ['deletedCount' => $deletedCount, 'deletedId' => $filter['_id']];
            }
            return ['deletedCount' => 0, 'deletedId' => null];
        } catch (Exception $e) {
            throw new Exception("Erro ao deletar documento: " . $e->getMessage());
        }
    }

    public function prepare(Collection $collection, $filter, $options = [])
    {
        try {
            return $collection->find($filter, $options);
        } catch (Exception $e) {
            throw new Exception("Erro ao preparar a consulta: " . $e->getMessage());
        }
    }

    public function query(Collection $collection, $filter, $options = [])
    {
        try {
            $document = $collection->findOne($filter, $options);
            return $document;
        } catch (Exception $e) {
            throw new Exception("Erro na consulta ao banco de dados: " . $e->getMessage());
        }
    }

    public function count(Collection $collection, $filter)
    {
        try {
            return $collection->countDocuments($filter);
        } catch (Exception $e) {
            throw new Exception("Erro na consulta ao banco de dados: " . $e->getMessage());
        }
    }

    public function select(Collection $collection, $filter, $options = [])
    {
        try {
            $documents = $collection->find($filter, $options)->toArray();
            return $documents;
        } catch (Exception $e) {
            throw new Exception("Erro na consulta ao banco de dados: " . $e->getMessage());
        }
    }

    public function selectOne(Collection $collection, $filter, $options = [])
    {
        try {
            $document = $collection->findOne($filter, $options);
            return $document;
        } catch (Exception $e) {
            throw new Exception("Erro na consulta ao banco de dados: " . $e->getMessage());
        }
    }

    public function getInsertedId()
    {
        return $this->lastInsertedId;
    }

    public function createObjectId($id = null): ObjectID
    {
        return new class($id) implements ObjectID
        {
            private $id;

            public function __construct($id)
            {
                $this->id = $id;
            }

            public function __toString()
            {
                return (string) $this->id;
            }

            public function jsonSerialize()
            {
                return $this->__toString();
            }

            public function serialize()
            {
                return serialize($this->__toString());
            }

            public function unserialize($serialized)
            {
                $this->id = unserialize($serialized);
            }

            #[\ReturnTypeWillChange]
            public function getTimestamp()
            {
                // Implementação do método getTimestamp() para retornar o timestamp do ObjectId
                return hexdec(substr($this->id, 0, 8));
            }
        };
    }
}
