<?php
require_once './server/autoload.php';
new autoload();

define('REPOSITORY_URL_BASE', 'https://repositorio.ufsc.br/');
define('REPOSITORY_URL', REPOSITORY_URL_BASE.'handle/123456789/7443/recent-submissions');
define('ITENS_BY_PAGE', 20);
define('FILE_TRBS_ON_REPOSITORY', '../content/pagesOnRepository.json');



if(key_exists('ctrl', $_GET))
{
	$controllerName = ucfirst($_GET['ctrl']);
	$action = (key_exists('act', $_GET) ? $_GET['act'] : 'index');
	$controller = new $controllerName();
	$controller->$action();
}