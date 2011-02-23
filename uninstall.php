<?php
/**
  Module developed for the Open Source Content Management System Website Baker (http://websitebaker.org)
  Copyright (c) 2010, Ralf Hertsch
  Contact me: ralf.hertsch@phpManufaktur.de, http://phpManufaktur.de

  This module is free software. You can redistribute it and/or modify it
  under the terms of the GNU General Public License  - version 2 or later,
  as published by the Free Software Foundation: http://www.gnu.org/licenses/gpl.html.

  This module is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  
  $Id: uninstall.php 5 2010-07-17 14:29:51Z ralf $
  
**/

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