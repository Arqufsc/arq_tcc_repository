<?php

class Trabalhos
{
    
    public function index()
    {
        $connection = new dbMysqlConnection();
        $dbActions = new dbMysqlActionsTrabalhos($connection->getConnection());
        echo json_encode($dbActions->read(), JSON_PRETTY_PRINT);
    }

    public function find()
    {
        echo json_encode(array('msg'=>'tamo junto=>'.$_GET['id']));
    }
}