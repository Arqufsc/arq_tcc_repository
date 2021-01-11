<?php

class Repositorio
{
    private $page;
    private $dataStructure;

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
        {
            if(file_exists(FILE_TRBS_ON_REPOSITORY))
                unlink(FILE_TRBS_ON_REPOSITORY);
        }

        $this->printRespononse($this->readPages());
    }

    public function trabalhos()
    {
		if(file_exists(FILE_TRBS_ON_REPOSITORY))
		{
			$this->dataStructure = Files::readDataStructure(FILE_TRBS_ON_REPOSITORY);

			$this->printRespononse($this->getDatastructureInfo());
		}else
			$this->printRespononse(array('fail'=>'Arquivo não encontrado'));
		
    }

    private function getDatastructureInfo()
    {
        $trabalhos = array();

        foreach($this->dataStructure['pages'] as $pageContent)
        {
            foreach($this->getTrabalhoInfo($pageContent) as $trabalho)
                $trabalhos[] = $trabalho;
        }

        $this->dataStructure['trabalhos'] = $trabalhos;

        Files::saveDataStructure(FILE_TRBS_ON_REPOSITORY, $this->dataStructure);

        return $trabalhos;
    }

    private function getTrabalhoInfo($pageContent)
    {
        $res = array();

        $res = $this->getTrabalhoUrlAndTitle($pageContent);
        $res = $this->getTrabalhoAuthorAndYearAndLocal($pageContent, $res);		
        
        return $res;
    }

    private function getTrabalhoAuthorAndYearAndLocal($pageContent, $res)
    {
        $limits = array (
            'author' => array (
                    'inicio' => '<span>',
                    'fim' => '</span>'
            ),
            'year' => array (
                    'inicio' => '<span class="date">',
                    'fim' => '</span>'
            ),
            'local' => array (
                    'inicio' => '<span class="publisher">',
                    'fim' => '</span>'
            )
    );

        foreach ( explode ( '<!-- External Metadata URL:', $pageContent ) as $i => $item ) :
            foreach ( $limits as $info => $limit ) :
                $start = strpos ( $item, $limit ['inicio'] );
                if ($start) {
                    $start = $start + strlen ( $limit ['inicio'] );
                    $end = strpos ( $item, $limit ['fim'], $start );
                    $res [$i - 1] [$info] = substr ( $item, $start, $end - $start );
                }
            endforeach;
        endforeach;

        return $res;
    }

    private function getTrabalhoUrlAndTitle($pageContent)
    {
        $res = array();
        $data = array();
        $urlBase = "\/handle\/123456789\/";

		preg_match_all ( "/(\<a href\=\"{$urlBase})(\d+)(\">)(.+)(\<\/a\>)/", $pageContent, $data );

        foreach ( $data[2] as $i => $d )
            $res [$i] ['url'] = stripslashes(REPOSITORY_URL_BASE.$urlBase).$d;

        foreach ( $data [4] as $i => $d )
			$res [$i] ['title'] = $d;

        return $res;
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