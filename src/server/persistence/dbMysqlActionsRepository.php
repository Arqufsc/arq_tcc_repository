<?php

class dbMysqlActionsRepository extends dbMysqlActions
{
    public function create(object $model)
    {
        $sql = "INSERT INTO tcc_trb_rep(trb_id, link) VALUES({$model->getTrb_id()}, '{$model->getLink()}')";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute();
    }

    public function read($where=null)
    {

    }

    public function find($id)
    {

    }

    public function update(object $model)
    {
        
    }

    public function delete(object $model)
    {
        
    }
}