<?php

class Repositorio
{
    private $page;

    public function index()
    {
        if(key_exists('page', $_GET))
            $this->page = $_GET['page'];
        else
        {
            $this->printRespononse(array('fail'=>"É necessário definir uma página"));
            return false;
        }

        $this->readPages();
    }

    public function restart()
    {   
        $this->page = $_GET['page'];
        
        if($this->page == 0)
            unlink(FILE_TRBS_ON_REPOSITORY);

        $this->printRespononse($this->readPages());
    }

    private function readPages()
    {
        $offset = $this->page * ITENS_BY_PAGE;
		$pageContent = $this->execCurl(REPOSITORY_URL."?offset={$offset}");
		$offset = ($this->page + 1) * ITENS_BY_PAGE;
		$nextPage = $this->execCurl(REPOSITORY_URL."?offset={$offset}");
		
		$string = '<ul class="ds-artifact-list">';
		
		$mark ['start'] = strpos ( $pageContent, $string );
		// fica só com a lista de trabalhos
		if ($mark ['start']) 
		{
			$mark ['start'] = $mark ['start'] + strlen ( $string );
			$mark ['end'] = strpos ( $pageContent, '</ul>', $mark ['start'] );
            $pageContent = substr ( $pageContent, $mark ['start'], $mark ['end'] - $mark ['start'] );
		}

        $dataStructure = Files::readDataStructure(FILE_TRBS_ON_REPOSITORY);
        
        if(key_exists('pages', $dataStructure))
            $content['pages'] = $dataStructure['pages'];

        $content['pages'][$this->page] = $pageContent;
        $content['morePages'] = is_numeric(strpos($nextPage, $string));
        
        Files::saveDataStructure(FILE_TRBS_ON_REPOSITORY, $content);

        return array(
            'page'=>$this->page,
            'morePages'=>$content['morePages']
        );
    } 

    private function execCurl($url)
    {
        $ch = curl_init();
		
		curl_setopt_array($ch, array(
			CURLOPT_URL=>$url,
			CURLOPT_RETURNTRANSFER=>1,
			CURLOPT_SSL_VERIFYPEER=>FALSE
		));
		
		$content = curl_exec($ch);		
		curl_close($ch);
		
		return $content;
	}	

    private function printRespononse(array $response)
    {
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
}