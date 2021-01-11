<?php

class dbMysqlActionsTrabalhos extends dbMysqlActions
{

    public function create(object $model)
    {
        
    }

    public function read($where=null)
    {
        $where = (!is_null($where) ? " WHERE {$where}" : null);
        
        $sql = "
            SELECT
                tcc_trb.id,
                tcc_trb.titulo,
                CONCAT (tcc_trb.ano, '-', tcc_trb.semestre) AS semestre,
                tcc_usr.nome AS autor,
                tcc_trb_rep.link AS repository
            FROM tcc_trb
            INNER JOIN tcc_trb_usr ON tcc_trb.id=tcc_trb_usr.trb_id AND tcc_trb_usr.status=2
            INNER JOIN tcc_usr ON tcc_trb_usr.usr_id=tcc_usr.id
            LEFT JOIN tcc_trb_rep ON tcc_trb.id=tcc_trb_rep.trb_id
            {$where}
            ORDER BY semestre DESC
        ";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $response;
    }

    public function find($id)
    {
        $response = $this->read("tcc_trb.id={$id}");

        if(count($response) == 1)
            return $response[0];
        else
            return false;
    }

    public function update(object $model)
    {
        $sql = "UPDATE tcc_trb SET tcc_trb.titulo='{$model->getTitulo()}' WHERE tcc_trb.id={$model->getId()}";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute();
    }

    public function delete(object $model)
    {
        
    }

}