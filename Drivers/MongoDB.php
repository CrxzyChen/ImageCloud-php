<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/1/10
 * Time: 12:00
 */

namespace Drivers;


class MongoDB implements DatabaseDriver
{
    private $manager;
    private $db_config;
    private $uri;
    private $bulk;
    private $database;
    private $collection;

    /**
     * MongoDB constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->db_config = \SimplePhp\Config::get("db");

        $username = urlencode($this->db_config->username);
        $password = urlencode($this->db_config->password);

        $this->uri = "mongodb://{$username}:{$password}@{$this->db_config->host}:{$this->db_config->port}";
        $this->manager = new \MongoDB\Driver\Manager($this->uri);
        $this->bulk = new \MongoDB\Driver\BulkWrite();
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
     * @param $document
     * @return mixed
     */
    public function insert($document)
    {
        $result = $this->bulk->insert($document);
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
     * @return mixed
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function find_one($query)
    {
        $result = $this->find($query, array("limit" => 1));
        return $result[0];
    }
}