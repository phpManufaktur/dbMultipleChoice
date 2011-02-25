<?php

/**
 * dbMultipleChoice
 * 
 * @author Ralf Hertsch (ralf.hertsch@phpmanufaktur.de)
 * @link http://phpmanufaktur.de/multiplechoice
 * @copyright 2010 - 2011
 * @license GNU GPL (http://www.gnu.org/licenses/gpl.html)
 * @version $Id$
 * 
 * FOR VERSION- AND RELEASE NOTES PLEASE READ THE INFO.TXT!
 */

// prevent this file from being accessed directly
if (!defined('WB_PATH')) die('invalid call of '.$_SERVER['SCRIPT_NAME']);

if (!defined('DEBUG_MODE')) define('DEBUG_MODE', true);

if (DEBUG_MODE) {
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
}
else {
	ini_set('display_errors', 0);
	error_reporting(E_ERROR);
}

require_once(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/initialize.php');

global $admin;

$error = '';

$tables = array('dbMultipleChoiceCfg', 'dbMultipleChoiceQuestion', 'dbMultipleChoiceQuestionaire', 'dbMultipleChoiceQuestionItem', 'dbMultipleChoiceQuestionHint', 'dbMultipleChoiceTableSort');

$delete = null;
foreach ($tables as $table) {
	unset($delete);
	$delete = new $table();
	if (!$delete->sqlTableExists()) {
		if (!$delete->sqlDeleteTable()) {
			$error .= sprintf('[INSTALLATION] %s', $delete->getError());
		}
	}
}

// Prompt Errors
if (!empty($error)) {
	$admin->print_error($error);
}
	
?>