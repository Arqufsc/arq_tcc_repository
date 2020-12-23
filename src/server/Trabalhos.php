<?php

class TrabalhosRepositorio
{
    private static $filename;
    private static $dataStructure;
    
    public static function getTrabalhos()
    {
        self::$filename = "../content/pagesOnRepository.json";

		if(file_exists(self::$filename))
		{
			self::$dataStructure = Files::readDataStructure(self::$filename);

			echo json_encode(self::getDatastructureInfo());
		}else
			echo json_encode(array('fail'=>'Arquivo não encontrado'));
		
    }

    private static function getDatastructureInfo()
    {
        $trabalhos = array();

        foreach(self::$dataStructure['pages'] as $pageContent)
        {
            foreach(self::getTrabalhoInfo($pageContent) as $trabalho)
                $trabalhos[] = $trabalho;
        }

        self::$dataStructure['trabalhos'] = $trabalhos;

        Files::saveDataStructure(self::$filename, self::$dataStructure);

        return $trabalhos;
    }

    private static function getTrabalhoInfo($pageContent)
    {
        $urlBase = "\/handle\/123456789\/";
		preg_match_all ( "/(\<a href\=\"{$urlBase})(\d+)(\">)(.+)(\<\/a\>)/", $pageContent, $data );
		
		// urls
		foreach ( $data [2] as $i => $d )
			$res [$i] ['url'] = stripslashes(REPOSITORY_URL_BASE.$urlBase).$d;
		
		// titulos
		foreach ( $data [4] as $i => $d )
			$res [$i] ['title'] = $d;

		// autor, publicação (ano e local)
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
			endforeach
			;
		endforeach
        ;
        
        return $res;
    }
}