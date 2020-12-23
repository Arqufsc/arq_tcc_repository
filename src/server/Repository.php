<?php
class Repository
{
	private static $filename;

    public static function start($page)
    {
		self::$filename = "../content/pagesOnRepository.json";
         
        echo json_encode(self::readPages($page));
    }

    private static function readPages($page)
    {
		$offset = $page * ITENS_BY_PAGE;
		$pageContent = self::execCurl(REPOSITORY_URL."?offset={$offset}");
		$offset = ($page + 1) * ITENS_BY_PAGE;
		$nextPage = self::execCurl(REPOSITORY_URL."?offset={$offset}");
		
		$string = '<ul class="ds-artifact-list">';
		
		$mark ['start'] = strpos ( $pageContent, $string );
		// fica só com a lista de trabalhos
		if ($mark ['start']) 
		{
			$mark ['start'] = $mark ['start'] + strlen ( $string );
			$mark ['end'] = strpos ( $pageContent, '</ul>', $mark ['start'] );
            $pageContent = substr ( $pageContent, $mark ['start'], $mark ['end'] - $mark ['start'] );
		}

        $dataStructure = Files::readDataStructure(self::$filename);
        
        if(key_exists('pages', $dataStructure))
            $content['pages'] = $dataStructure['pages'];

        $content['pages'][$page] = $pageContent;
		$content['morePages'] = is_numeric(strpos($nextPage, $string));
		
        Files::saveDataStructure(self::$filename, $content);

        return array(
            'page'=>$page,
            'morePages'=>$content['morePages']
        );
    }

    private static function execCurl($url)
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
}
