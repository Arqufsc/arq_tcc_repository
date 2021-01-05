<?php
require_once './server/autoload.php';

define('REPOSITORY_URL_BASE', 'https://repositorio.ufsc.br/');
define('REPOSITORY_URL', REPOSITORY_URL_BASE.'handle/123456789/7443/recent-submissions');
define('ITENS_BY_PAGE', 20);

if(key_exists('page', $_GET))
	Repository::start($_GET['page']);
elseif(key_exists('ctrl', $_GET))
{
	$controllerName = ucfirst($_GET['ctrl']);
	$action = (key_exists('act', $_GET) ? $_GET['act'] : 'index');
	$controller = new $controllerName();
	$controller->$action();
}
else
	TrabalhosRepositorio::getTrabalhos();