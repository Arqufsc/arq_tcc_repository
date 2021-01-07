<?php

class Trabalhos
{
    private $dbConnection;
    private $dbActions;

    public function __construct()
    {
        $this->dbConnection = new dbMysqlConnection();
        $this->dbConnection = $this->dbConnection->getConnection();
        $this->dbActions = new dbMysqlActionsTrabalhos($this->dbConnection);
    }
    
    public function index()
    {
        $trbList = $this->dbActions->read();
        $response = array();

        foreach($trbList as $trb)
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

        $this->printRespononse($response);
    }

    public function find()
    {
        if(!key_exists('id', $_GET))
        {
            $this->printRespononse(array(
                'fail'=>'É necessário definir um id'
            ));
            return false;
        }

        $trb = $this->dbActions->find($_GET['id']);
        $link = false;
        
        $trbsOnRepository = Files::readDataStructure(FILE_TRBS_ON_REPOSITORY);
        foreach($trbsOnRepository['trabalhos'] as $trbRepository)
        {
            $autorArray = array();

            if(key_exists('author', $trbRepository))
                $autorArray = explode(', ', $trbRepository['author']);
                
            if(count($autorArray)>1)
            {
                $autor = "{$autorArray[1]} {$autorArray[0]}";
                if($trb['autor'] == $autor)
                    $link = $trbRepository;
            }
            
        }
/*
        */
        if($link)
        {
            //atualizar título
            $trbModel = new tcc_trb();
            $trbModel->setId($_GET['id']);
            $trbModel->setTitulo($link['title']);
            $this->dbActions->update($trbModel);

            //cadastrar link
            $trbRepositoryModel = new tcc_trb_rep();
            $trbRepositoryModel->setTrb_id($_GET['id']);
            $trbRepositoryModel->setLink($link['url']);
            $trbRepositoryActions = new dbMysqlActionsRepository($this->dbConnection);
            $trbRepositoryActions->create($trbRepositoryModel);

            $response['url'] = $link['url'];
        }
        else
        {
            $fileDateChange = date('d-m-Y H:i:s', filectime(FILE_TRBS_ON_REPOSITORY));
            $response['fail'] = "Sem um trabalho correspondente no repositorio. Busca realizada em {$fileDateChange}";
        }
        $this->printRespononse($response);
    }

    private function printRespononse(array $response)
    {
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
}