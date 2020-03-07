<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/1/10
 * Time: 12:00
 */

namespace Drivers;

use MongoDB\Driver\Cursor;

class MongoDB implements DatabaseDriver
{
    private $manager;
    private $uri;
    private $bulk;
    private $database;
    private $collection;

    /**
     * MongoDB constructor.
     * @param string $username
     * @param string $password
     * @param string $host
     * @param int $port
     */
    public function __construct(string $username, string $password, string $host, int $port = 27017)
    {
        $username = urlencode($username);
        $password = urlencode($password);

        $this->uri = "mongodb://$username:$password@$host:$port";

        $this->manager = new \MongoDB\Driver\Manager($this->uri);
    }

    /**
     * @param array $query
     * @param array $option
     * @return array|bool
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function find($query = array(), $option = array())
    {
        $query = new \MongoDB\Driver\Query($query, $option);
        $cursor = $this->manager->executeQuery("$this->database.$this->collection", $query);

        $documents = [];
        foreach ($cursor as $document) {
            $documents[] = $document;
        }
        return $documents;
    }

    /**
     * @param array $query
     * @param array $update
     * @param array $option
     * @return array
     * @throws \MongoDB\Driver\Exception\Exception
     * @link https://docs.mongodb.com/manual/reference/method/db.collection.findAndModify/
     */
    public function findAndModify($query = array(), $update = array(), $option = array())
    {
        $command_array = array(
            "findAndModify" => "$this->collection",
            "query" => $query,
            "update" => $update);
        $command_array = array_merge($command_array, $option);
        $command = new \MongoDB\Driver\Command($command_array);
        $cursor = $this->manager->executeCommand($this->database, $command);
        $documents = [];
        foreach ($cursor as $document) {
            $documents[] = $document->value;
        }
        return $documents;
    }

    /**
     * @param $document
     * @return mixed
     */
    public function insert($document)
    {
        $bulk = new \MongoDB\Driver\BulkWrite();
        $bulk->insert($document);
        $result = $this->manager->executeBulkWrite("$this->database.$this->collection", $bulk);

        return $result->getInsertedCount();
    }

    /**
     * @param $query
     * @param $option
     * @return mixed
     */
    public function delete($query, $option)
    {
        $result = $this->bulk->delete($query, $option);
        return $result->getDeletedCount();
    }

    /**
     * @param $document
     */
    public function save($document)
    {
    }

    /**
     * @param $database
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }

    /**
     * @param $collection
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    /**
     * @param $query
     * @param array $option
     * @return mixed
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function findOne($query, $option = array())
    {
        $option = array_merge($option, array("limit" => 1));
        $result = $this->find($query, $option);
        if (isset($result[0])) {
            return $result[0];
        } else {
            return null;
        }
    }

    /**
     * @param $keys
     * @param $options
     * @return \MongoDB\Driver\Cursor
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function createIndexes($keys, $options)
    {
        $indexes = array();
        foreach ($keys as $keyName => $sort) {
            $index = array(
                "name" => "$keyName",
                "key" => ["$keyName" => $sort],
                "ns" => "$this->database.$this->collection",
            );
            $index = array_merge($index, $options);
            $indexes[] = $index;
        }
        $command = new \MongoDB\Driver\Command([
            "createIndexes" => "$this->collection",
            "indexes" => $indexes
        ]);
        $cursor = $this->manager->executeCommand($this->database, $command);
        return $cursor;
    }

    /**
     * @param array $query
     * @return int
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function count($query = array()): int
    {
        $command = new \MongoDB\Driver\Command(array(
            'count' => $this->collection,
            'query' => $query ?: new \stdClass(),
        ));

        $cursor = $this->manager->executeCommand($this->database, $command);
        $result = $cursor->toArray();
        return $result[0]->n;
    }

    /**
     * @param array $pipeline
     * @return array
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function aggregate(array $pipeline)
    {
        $command = new \MongoDB\Driver\Command(array(
            'aggregate' => $this->collection,
            'pipeline' => $pipeline,
            'cursor' => new \stdClass()
        ));
        $cursor = $this->manager->executeCommand($this->database, $command);
        $documents = [];
        foreach ($cursor as $document) {
            $documents[] = $document;
        }
        return $documents;
    }
}