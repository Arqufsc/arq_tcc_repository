<?php

class dbMysqlActionsTrabalhos extends dbMysqlActions
{

    public function create(object $model)
    {
        
    }

    public function read()
    {
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
            ORDER BY semestre DESC

        ";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        
        $response = array();
        foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $trb)
        {
            $dataStructure = array(
                'id'=>$trb['id'],
                'titulo'=>$trb['titulo'],
                'autor'=>$trb['autor'],
                'semestre'=>$trb['semestre'],
                'repository'=>$trb['repository']
            );

            if($dataStructure['semestre']==$trb['semestre'])
                $response[$trb['semestre']][] = $dataStructure;
        }

        return $response;
    }

    public function update(object $model)
    {
        
    }

    public function delete(object $model)
    {
        
    }

}