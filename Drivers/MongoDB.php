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
    private $uri;
    private $bulk;
    private $database;
    private $collection;
//
//    /**
//     * MongoDB constructor.
//     * @param $host
//     * @param $port
//     * @param $username
//     * @param $password
//     */
    public function __construct($username, $password, $host, $port = 27017)
    {
        $username = urlencode($username);
        $password = urlencode($password);

        $this->uri = "mongodb://$username:$password@$host:$port";

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
        $this->manager = new \MongoDB\Driver\Manager($this->uri);
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
        if (isset($result[0])) {
            return $result[0];
        } else {
            return null;
        }
    }
}