<?php
namespace Core;
/*
 *-------------------------------------------------------------------
 * CodeMediator - Constants
 *-------------------------------------------------------------------
 *
 * Do Not Edit Or Remove Anything From This File 
 * This File Has To Be Included In Core->Config File Before Anything 
 *
 */


 /**
  * Directories
  * ------------------------------------------------------------------------
  */
defined('DS')        OR define('DS', DIRECTORY_SEPARATOR);
defined('BASE')      OR define('BASE', dirname(dirname(dirname( __FILE__ ))));
defined('APP')       OR define('APP', BASE.DS.'Application');
defined('CONFIG')    OR define('CONFIG', APP.DS.'Config');
defined('CORE')      OR define('CORE', APP.DS.'Core');
defined('SESS')      OR define('SESS', APP.DS.'Session');
defined('VIEWS')     OR define('VIEWS', APP.DS.'Views');
defined('CONT')      OR define('CONT', APP.DS.'Controllers');
defined('MODELS')    OR define('MODELS', APP.DS.'Models');
defined('LANGUAGES') OR define('LANGUAGES', APP.DS.'Languages');
defined('CACHE')     OR define('CACHE', APP.DS.'Cache');


defined('HELPER')    OR define('HELPER', APP.DS.'Helper');
defined('LIBS')      OR define('LIBS', APP.DS.'Libraries');
defined('CLASSES')   OR define('CLASSES', APP.DS.'Classes');

defined('SERVICES')    OR define('SERVICES', APP.DS.'Services');
defined('MIDDLEWARES') OR define('MIDDLEWARES', APP.DS.'Middlewares');
defined('CORE_MIDDLEWARES') OR define('CORE_MIDDLEWARES', CORE.DS."Middlewares");


/**/
defined('HOME')       OR define('HOME', BASE.DS.'public');
defined('ASSETS')     OR define('ASSETS', HOME.DS.'assets');
defined('UPLOADS')    OR define('UPLOADS', HOME.DS.'uploads');
// PUBLIC_DIR alias of HOME
defined('PUBLIC_DIR') OR define('PUBLIC_DIR', BASE.DS.'public');
defined('DB_BACKUP')  OR define('DB_BACKUP', BASE.DS.'db_backups');

/**/
defined('SUCCESS')   OR define('SUCCESS', 'SUCCESS');
defined('DANGER')    OR define('DANGER', 'DANGER');
defined('WARNING')   OR define('WARNING', 'WARNING');
defined('PRIMARY')   OR define('PRIMARY', 'PRIMARY');
defined('SECONDARY') OR define('SECONDARY', 'SECONDARY');
defined('INFO')      OR define('INFO', 'INFO');
defined('ERROR')     OR define('ERROR', 'ERROR');
/******** Model ********/
defined('OBJ')       OR define('OBJ', 'OBJ');
defined('ASSOC')     OR define('ASSOC', 'ASSOC');
defined('FETCH_OBJ') OR define('FETCH_OBJ', 'FETCH_OBJ');
defined('FETCH_ASSOC')  OR define('FETCH_ASSOC', 'FETCH_ASSOC');
//DataTypes
defined('INTEGER')   OR define('INTEGER', 'INTEGER');
defined('STRING')    OR define('STRING', 'STRING');
defined('BOOLEAN')   OR define('BOOLEAN', 'BOOLEAN');
defined('ARRAY')     OR define('ARRAY', 'ARRAY');
defined('OBJECT')    OR define('OBJECT', 'OBJECT');
defined('MIXED')     OR define('MIXED', 'MIXED');
defined('AUTO')      OR define('AUTO', 'AUTO');
// Model Sql() 
defined('EXECUTE')   OR define('EXECUTE', 'EXECUTE');
defined('COUNT')     OR define('COUNT', 'COUNT');
defined('FETCH')     OR define('FETCH', 'FETCH');
defined('FETCH_ALL') OR define('FETCH_ALL', 'FETCH_ALL');

// URI
defined('CASE_SENSITIVE')   OR define('CASE_SENSITIVE', 'CASE_SENSITIVE');
defined('CASE_INSENSITIVE') OR define('CASE_INSENSITIVE', 'CASE_INSENSITIVE');
