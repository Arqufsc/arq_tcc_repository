<?php

class Trabalhos
{
    public function __construct()
    {
        /*

            4- Percorrer a listagem e verificar trabalhos sem link para o repositório (JS)
            5- Em cada trabalho buscar seu equivalente nos trabalhos do repositório
            6 - Uma vez localizado o trabalho adicionar link e atualizar título

        */
        
        $connection = new dbMysqlConnection();
        $dbActions = new dbMysqlActionsTrabalhos($connection->getConnection());
        echo json_encode($dbActions->read(), JSON_PRETTY_PRINT);
        
    }

    public function index()
    {

    }
}