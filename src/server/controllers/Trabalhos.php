<?php

class Trabalhos
{
    private $dbConnection;
    private $dbActions;
    private $msgFail;
    private $trbId;

    public function __construct()
    {
        $this->dbConnection = new dbMysqlConnection();
        $this->dbConnection = $this->dbConnection->getConnection();
        $this->dbActions = new dbMysqlActionsTrabalhos($this->dbConnection);
    }
    
    public function index()
    {
        $trbList = $this->dbActions->read();        
        $response = $this->separateBySemester($trbList);
        $this->printRespononse($response);
    }

    public function find()
    {
        $this->trbId = $this->extractId();
        if(!$this->trbId)
            return false;

        $trb = $this->dbActions->find($this->trbId);

        $trbOnRepository = $this->getTrbOnRepository($trb);
        
        if($trbOnRepository)
        {
            $this->updateTrbTitle($trbOnRepository['title']);

            $this->addRepositoryLink($trbOnRepository['url']);

            $response['trb'] = array(
                'title'=>$trbOnRepository['title'],
                'url'=>$trbOnRepository['url']
            );
        }
        else
        {
            if(!is_null($this->msgFail))
                $response = $this->msgFail;
            else
            {
                $fileDateChange = date('d-m-Y H:i:s', filectime(FILE_TRBS_ON_REPOSITORY));
                $response['fail'] = "Sem um trabalho correspondente no repositorio. Busca realizada em {$fileDateChange}";
            }
        }
        $this->printRespononse($response);
    }

    private function printRespononse(array $response)
    {
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    private function separateBySemester(array $trbs)
    {
        $response = array();

        foreach($trbs as $trb)
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

    private function extractId()
    {
        if(key_exists('id', $_GET))
        {
            return $_GET['id'];
        }
        else
        {
            $this->msgFail['fail'] = 'É necessário definir um id';
            return false;
        }
    }

    private function getTrbOnRepository($trb)
    {
        $trbOnRepository = false;        
        $trbsOnRepository = Files::readDataStructure(FILE_TRBS_ON_REPOSITORY);

        if(empty($trbsOnRepository))
        {
            $this->msgFail['fail'] = "Arquivo vazio ou inexistente!";
            return false;
        }

        foreach($trbsOnRepository['trabalhos'] as $trbRepository)
        {
            $autorArray = array();

            if(key_exists('author', $trbRepository))
                $autorArray = explode(', ', $trbRepository['author']);
                
            if(count($autorArray)>1)
            {
                $autor = "{$autorArray[1]} {$autorArray[0]}";
                if($trb['autor'] == $autor)
                    $trbOnRepository = $trbRepository;
            }            
        }

        return $trbOnRepository;
    }

    private function updateTrbTitle($trbTitle)
    {
        $trbModel = new tcc_trb();
        $trbModel->setId($this->trbId);
        $trbModel->setTitulo($trbTitle);
        try {
            $this->dbActions->update($trbModel);
        } catch (\Throwable $th) {
            $this->msgFail['fail'] = $th->getMessage(); 
        }
        
    }

    private function addRepositoryLink($url)
    {
        $trbRepositoryModel = new tcc_trb_rep();
        $trbRepositoryModel->setTrb_id($this->trbId);
        $trbRepositoryModel->setLink($url);
        
        $trbRepositoryActions = new dbMysqlActionsRepository($this->dbConnection);
        try {
            $trbRepositoryActions->create($trbRepositoryModel);
        } catch (\Throwable $th) {
            $this->msgFail['fail'] = $th->getMessage();
        }
        
    }
}