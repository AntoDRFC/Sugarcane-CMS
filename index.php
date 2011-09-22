<?php
error_reporting(E_ALL | E_STRICT);
set_include_path(get_include_path() . PATH_SEPARATOR . 'lib');
date_default_timezone_set('Europe/London');

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);
$autoloader->suppressNotFoundWarnings(true);;

switch (getenv('SERVER_CONFIG')) {
	case 'dev':
		$config = new Zend_Config_Ini("conf/dev.ini", null);
		break;
	case 'live':
		$config = new Zend_Config_Ini("conf/live.ini", null);
		break;
	default:
		$config = new Zend_Config_Ini("conf/local.ini", null);
		break;
}
Zend_Registry::set('config', $config);

//session_start();

$frontController = Zend_Controller_Front::getInstance();
$frontController->setParam('noViewRenderer', true);

$frontController->setControllerDirectory($config->paths->base . DIRECTORY_SEPARATOR . $config->paths->controllers);
$frontController->addControllerDirectory($config->paths->base . DIRECTORY_SEPARATOR . $config->paths->controllers . DIRECTORY_SEPARATOR . 'Admin', 'admin');

$frontController->registerPlugin(new Zend_Controller_Plugin_ErrorHandler());

$router = $frontController->getRouter();


$pagesRoute = new Zend_Controller_Router_Route(
    '/view/:currentpage/*',
    array('controller'  => 'index',
          'action'      => 'renderpage',
          'currentpage' => 'index')
);
$router->addRoute('pagesRoute', $pagesRoute);

$blogRoute = new Zend_Controller_Router_Route(
    '/blog/:blog_id',
    array('controller'  => 'blog',
          'action'      => 'index')
);
$router->addRoute('blogRoute', $blogRoute);

$blogPostRoute = new Zend_Controller_Router_Route(
    '/blog/view/:postid',
    array('controller'  => 'blog',
          'action'      => 'viewpost')
);
$router->addRoute('blogPostRoute', $blogPostRoute);

$blogPostCommentRoute = new Zend_Controller_Router_Route(
    '/blog/postcomment',
    array('controller'  => 'blog',
          'action'      => 'postcomment')
);
$router->addRoute('blogCommentRoute', $blogPostCommentRoute);

$blogArchiveRoute = new Zend_Controller_Router_Route(
    '/blog/archive',
    array('controller'  => 'blog',
          'action'      => 'archive')
);
$router->addRoute('blogArchiveRoute', $blogArchiveRoute);

// Here we go
$frontController->dispatch();