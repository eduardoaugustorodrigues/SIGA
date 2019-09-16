<?php
/**
 * Arquivo de Configuração do sistema | BootStrap
 * @package 	
 * @subpackage
 * @author      Thiago Lênin
 * @since 	@2011
 * @version     1.0
 */

/*
 * Variáveis para acesso a diretórios do sistema
 */
$DIR_LIB        = "./library";                        #bibliotecas
$DIR_FORM       = "./application/forms/";             #formulários
$DIR_CONFIG     = "./application/configs/config.ini"; #configurações do(s) banco(s)
$DIR_LAYOUT     = "./application/layouts/";           #layout
$DIR_MODEL      = "./application/models/";            #modelo | dao
$DIR_MAPPER     = "./application/models/table/";      #modelo | Mapeamento de tabelas
$DIR_VIEW       = "./application/views/";             #visões | View
$DIR_CONTROLLER = "./application/controllers/";       #controles | Control

/*
 * Configura as mensagens de erro a ser exibido nas telas
 */
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors','on');

/*
 * Formato
 * Idioma
 * Localização */
setlocale(LC_ALL, 'pt_BR'); #lingua
setlocale(LC_CTYPE, 'de_DE.iso-8859-1'); #charset
date_default_timezone_set('America/Sao_Paulo'); #localidade

/*
 * Configuração do caminho dos includes
 */
set_include_path('.' . PATH_SEPARATOR . $DIR_LIB .
                       PATH_SEPARATOR . $DIR_MODEL .
                       PATH_SEPARATOR . $DIR_MAPPER .
                       PATH_SEPARATOR . $DIR_FORM .
                       PATH_SEPARATOR . $DIR_CONTROLLER .
                       PATH_SEPARATOR . get_include_path());

/*
 * Componente obrigatório para carregar arquivos, classes e recursos
 */
require_once "$DIR_LIB/Zend/Loader/Autoloader.php";
Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);


/*
 * Configurações do banco de dados
 */
$config = new Zend_Config_Ini($DIR_CONFIG, 'local');
$registry = Zend_Registry::getInstance();
$registry->set('config', $config); #Registra a base a ser utilizada pelo sistema

$db = Zend_Db::factory($config->db);
Zend_Db_Table::setDefaultAdapter($db);
Zend_Registry::set('db', $db); #Registra o nome do banco de dados

/*
 * Instância da sessão
 * Criação da NameSpace
 */
Zend_Session::start();
Zend_Registry::set('session', new Zend_Session_Namespace()); #Manipulador de sessão

/*
 * Configurações do layout padrão do sistema
 */
Zend_Layout::startMvc(array(
	'layout'     => 'layout',
	'layoutPath' => $DIR_LAYOUT,
	'contentKey' => 'content'));

/*
 * Configura a visão e a codificação das páginas
 */
$view = new Zend_View();
$view->setEncoding('ISO-8859-1');
$view->setEscape('htmlentities');
$view->setBasePath($DIR_VIEW);     #Diretório das visões
Zend_Registry::set('view', $view); #Registra na memória

/*
 * Classes Util
 */
require_once "$DIR_LIB/Util/Loader.php";

/*
 * Variáveis para receber dados vindos via get, post e request
 */
$filter = new Zend_Filter();
$filter->addFilter(new Zend_Filter_StringTrim()); #retira espaços antes e depois
$filter->addFilter(new Zend_Filter_StripTags()); #retira código html e etc
$options = array('escapeFilter' => $filter);
Zend_Registry::set('post', new Zend_Filter_Input(NULL, NULL, $_POST, $options));
Zend_Registry::set('get',  new Zend_Filter_Input(NULL, NULL, $_GET,  $options));
Zend_Registry::set('request',  new Zend_Filter_Input(NULL, NULL, $_REQUEST,  $options));

/*
 * Tradução do Zend_Form
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
$controller->throwExceptions(true);                   #Mostra exceções (para teste)
$controller->setControllerDirectory($DIR_CONTROLLER); #Diretório
$controller->dispatch();                              #Executa o controlador