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

require_once(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/initialize.php');

$error = '';

global $admin;

$dbMCHint = new dbMultipleChoiceQuestionHint();
if (!$dbMCHint->sqlTableExists()) {
	if (!$dbMCHint->sqlCreateTable()) {
		$error .= sprintf('[UPGRADE] %s', $dbMCHint->getError());
	}
}

$dbMCTableSort = new dbMultipleChoiceTableSort();
if (!$dbMCTableSort->sqlTableExists()) {
	if (!$dbMCTableSort->sqlCreateTable()) {
		$error .= sprintf('[UPGRADE] %s', $dbMCTableSort->getError());
	}
}

// Prompt Errors
if (!empty($error)) {
	$admin->print_error($error);
}
	
?>