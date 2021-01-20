<?php

abstract class dbMysqlActions
{
    protected $connection;
    
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public abstract function create($model);

    public abstract function read($where=null);

    public abstract function find($id);

    public abstract function update($model);

    public abstract function delete($model);
}