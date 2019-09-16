<?php
/**
 * Arquivo de Configura��o do sistema | BootStrap
 * @package 	
 * @subpackage
 * @author      Thiago L�nin
 * @since 	@2011
 * @version     1.0
 */

/*
 * Vari�veis para acesso a diret�rios do sistema
 */
$DIR_LIB        = "./library";                        #bibliotecas
$DIR_FORM       = "./application/forms/";             #formul�rios
$DIR_CONFIG     = "./application/configs/config.ini"; #configura��es do(s) banco(s)
$DIR_LAYOUT     = "./application/layouts/";           #layout
$DIR_MODEL      = "./application/models/";            #modelo | dao
$DIR_MAPPER     = "./application/models/table/";      #modelo | Mapeamento de tabelas
$DIR_VIEW       = "./application/views/";             #vis�es | View
$DIR_CONTROLLER = "./application/controllers/";       #controles | Control

/*
 * Configura as mensagens de erro a ser exibido nas telas
 */
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors','on');

/*
 * Formato
 * Idioma
 * Localiza��o */
setlocale(LC_ALL, 'pt_BR'); #lingua
setlocale(LC_CTYPE, 'de_DE.iso-8859-1'); #charset
date_default_timezone_set('America/Sao_Paulo'); #localidade

/*
 * Configura��o do caminho dos includes
 */
set_include_path('.' . PATH_SEPARATOR . $DIR_LIB .
                       PATH_SEPARATOR . $DIR_MODEL .
                       PATH_SEPARATOR . $DIR_MAPPER .
                       PATH_SEPARATOR . $DIR_FORM .
                       PATH_SEPARATOR . $DIR_CONTROLLER .
                       PATH_SEPARATOR . get_include_path());

/*
 * Componente obrigat�rio para carregar arquivos, classes e recursos
 */
require_once "$DIR_LIB/Zend/Loader/Autoloader.php";
Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);


/*
 * Configura��es do banco de dados
 */
$config = new Zend_Config_Ini($DIR_CONFIG, 'local');
$registry = Zend_Registry::getInstance();
$registry->set('config', $config); #Registra a base a ser utilizada pelo sistema

$db = Zend_Db::factory($config->db);
Zend_Db_Table::setDefaultAdapter($db);
Zend_Registry::set('db', $db); #Registra o nome do banco de dados

/*
 * Inst�ncia da sess�o
 * Cria��o da NameSpace
 */
Zend_Session::start();
Zend_Registry::set('session', new Zend_Session_Namespace()); #Manipulador de sess�o

/*
 * Configura��es do layout padr�o do sistema
 */
Zend_Layout::startMvc(array(
	'layout'     => 'layout',
	'layoutPath' => $DIR_LAYOUT,
	'contentKey' => 'content'));

/*
 * Configura a vis�o e a codifica��o das p�ginas
 */
$view = new Zend_View();
$view->setEncoding('ISO-8859-1');
$view->setEscape('htmlentities');
$view->setBasePath($DIR_VIEW);     #Diret�rio das vis�es
Zend_Registry::set('view', $view); #Registra na mem�ria

/*
 * Classes Util
 */
require_once "$DIR_LIB/Util/Loader.php";

/*
 * Vari�veis para receber dados vindos via get, post e request
 */
$filter = new Zend_Filter();
$filter->addFilter(new Zend_Filter_StringTrim()); #retira espa�os antes e depois
$filter->addFilter(new Zend_Filter_StripTags()); #retira c�digo html e etc
$options = array('escapeFilter' => $filter);
Zend_Registry::set('post', new Zend_Filter_Input(NULL, NULL, $_POST, $options));
Zend_Registry::set('get',  new Zend_Filter_Input(NULL, NULL, $_GET,  $options));
Zend_Registry::set('request',  new Zend_Filter_Input(NULL, NULL, $_REQUEST,  $options));

/*
 * Tradu��o do Zend_Form
 */
$translator = new Zend_Translate ( 
                                    array (
                                            'adapter' => 'array',
                                            'content' => './library/translate',
                                            'locale' => 'pt_BR', 'scan' => Zend_Translate::LOCALE_DIRECTORY
                                        )
             );
Zend_Validate_Abstract::setDefaultTranslator ( $translator );

/*
 *  Configura o controlador (controller) do sistema
 */
$controller = Zend_Controller_Front::getInstance();
$controller->throwExceptions(true);                   #Mostra exce��es (para teste)
$controller->setControllerDirectory($DIR_CONTROLLER); #Diret�rio
$controller->dispatch();                              #Executa o controlador