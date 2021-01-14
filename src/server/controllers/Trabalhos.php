<?php

class Trabalhos
{
    private $dbConnection;
    private $dbActions;
    private $msgFail;
    private $trbId;
    private $trb;

    public function __construct()
    {
        $this->dbConnection = new dbMysqlConnection();
        $this->dbConnection = $this->dbConnection->getConnection();
        $this->dbActions = new dbMysqlActionsTrabalhos($this->dbConnection);

        if(key_exists('act', $_GET) AND in_array($_GET['act'], array('find', 'refind')))
        {
            $this->trbId = $this->extractId();
            if(!$this->trbId)
                return false;
    
            $this->trb = $this->dbActions->find($this->trbId);
        }
    }
    
    public function index()
    {
        $trbList = $this->dbActions->read();        
        $response = $this->separateBySemester($trbList);
        $this->printRespononse($response);
    }

    public function find()
    {

        $trbOnRepository = $this->getTrbOnRepository();
        
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
    
    private function convertNameInArray($name)
    {
        $response = array();

        foreach(explode(' ', $name) as $namePart)
        {
            if(strlen($namePart)>0)
                $response[] = $namePart;
        }

        return $response;
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

    private function getTrbOnRepository()
    {
        if($this->trb === false)
        {
            $this->msgFail['fail'] = "Sem correspondencia do site para o id {$this->trbId}";
        }

        $trbOnRepository = false;        
        $trbsOnRepository = Files::readDataStructure(FILE_TRBS_ON_REPOSITORY);

        if(empty($trbsOnRepository))
        {
            $this->msgFail['fail'] = "Arquivo vazio ou inexistente!";
            return false;
        }

        if(!key_exists('trabalhos', $trbsOnRepository))
        {
            $this->msgFail['error'] = "Não houve a fitragem de dados do repositório!";
            return false;
        }
        
        $response = $this->search($trbsOnRepository['trabalhos'], $this->trb['autor']);

        if(empty($response))
            $this->msgFail['fail'] = "Nenhuma trabalho correspondente...";
        elseif(count($response)>1)
        {
            $this->msgFail['multiplos'] = "Foram encontradas múltiplas ocorrências...";
            $this->msgFail['result'] = $response;
        }
        else
            {
                $keys = array_keys($response);
                $trbOnRepository = $response[$keys[0]];
            }

        return $trbOnRepository;
    }

    private function search(array $trabalhosBySemestre, $nameComplete)
    {
        $nameParts = $this->convertNameInArray($nameComplete);
        
        $stages = array(
            $nameParts[count($nameParts)-1],
            $nameParts[0],
            $nameParts[1]
        );
        
        $continue = true;
        
        while($continue)
        {
            foreach($stages as $stage)
            {
                foreach($trabalhosBySemestre as $key=>$trabalho)
                {
                    $autor = $this->getNameFromRepository($trabalho);
                    $autorArray = $this->convertNameInArray($autor);

                    if(!in_array($stage, $autorArray))
                        unset($trabalhosBySemestre[$key]);
                }

                if(count($trabalhosBySemestre)<=1)
                    $continue = false;
            }

            foreach($trabalhosBySemestre as $key=>$trabalho)
            {
                $autor = $this->getNameFromRepository($trabalho);
                $autorArray = $this->convertNameInArray($autor);
                if(count($nameParts) != count($autorArray))
                    unset($trabalhosBySemestre[$key]);
            }

            $continue = false;
        }

        return $trabalhosBySemestre;
    }

    private function getNameFromRepository($trbRepository)
    {
        $autorArray = array();
        
        if(key_exists('author', $trbRepository))
            $autorArray = explode(', ', $trbRepository['author']);
            
        if(count($autorArray)>1)
            return "{$autorArray[1]} {$autorArray[0]}";
        else
            return false;
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