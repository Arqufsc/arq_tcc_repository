<?php

abstract class dbMysqlActions
{
    protected $connection;
    
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public abstract function create(object $model);

    public abstract function read();

    public abstract function update(object $model);

    public abstract function delete(object $model);
}