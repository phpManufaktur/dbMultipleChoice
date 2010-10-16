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
  
  $Id: install.php 5 2010-07-17 14:29:51Z ralf $
  
**/

// prevent this file from being accessed directly
if (!defined('WB_PATH')) die('invalid call of '.$_SERVER['SCRIPT_NAME']);

require_once(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/class.droplets.php');
require_once(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/initialize.php');

global $admin;

$error = '';

$tables = array('dbMultipleChoiceCfg', 'dbMultipleChoiceQuestion', 'dbMultipleChoiceQuestionaire', 'dbMultipleChoiceQuestionItem');

foreach ($tables as $table) {
	unset($create);
	$create = new $table();
	if (!$create->sqlTableExists()) {
		if (!$create->sqlCreateTable()) {
			$error .= sprintf('[INSTALLATION] %s', $create->getError());
		}
	}
}
/*
$dbCfg = new dbMultipleChoiceCfg();
if (!$dbCfg->sqlTableExists()) {
	if (!$dbCfg->sqlCreateTable()) {
		$error .= sprintf('[INSTALLATION] %s', $dbCfg->getError());
	}
}

$dbMCQuestion = new dbMultipleChoiceQuestion();
if (!$dbMCQuestion->sqlTableExists()) {
	if (!$dbMCQuestion->sqlCreateTable()) {
		$error .= sprintf('[INSTALLATION] %s', $dbMCQuestion->getError());
	}
}

$dbMCQuestionaire = new dbMultipleChoiceQuestionaire();
if (!$dbMCQuestionaire->sqlTableExists()) {
	if (!$dbMCQuestionaire->sqlCreateTable()) {
		$error .= sprintf('[INSTALLATION] %s', $dbMCQuestionaire->getError());
	}
}

$dbMCQuestionItem = new dbMultipleChoiceQuestionItem();
if (!$dbMCQuestionItem->sqlTableExists()) {
	if (!$dbMCQuestionItem->sqlCreateTable()) {
		$error .= sprintf('[INSTALLATION] %s', $dbMCQuestionItem->getError());
	}
}
*/
// Install Droplets
$droplets = new checkDroplets();
if ($droplets->insertDropletsIntoTable()) {
  $message = 'The Droplets for dbMultipleChoice where successfully installed! Please look at the Help for further informations.';
}
else {
  $message = 'The installation of the Droplets for dbMultipleChoice failed. Error: '. $droplets->getError();
}
if ($message != "") {
  echo '<script language="javascript">alert ("'.$message.'");</script>';
}

// Prompt Errors
if (!empty($error)) {
	$admin->print_error($error);
}
	
?>