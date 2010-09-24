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

$dbCfg = new dbMultipleChoiceCfg();
if ($dbCfg->sqlTableExists()) {
	if (!$dbCfg->sqlDeleteTable()) {
		$error .= sprintf('[UNINSTALL] %s', $dbCfg->getError());
	}
}
if ($dbCfg->isConnected) {
	$dbCfg->close();
	$dbCfg->isConnected = false;
}

$dbMCQuestion = new dbMultipleChoiceQuestion();
if ($dbMCQuestion->sqlTableExists()) {
	if (!$dbMCQuestion->sqlDeleteTable()) {
		$error .= sprintf('[UNINSTALL] %s', $dbMCQuestion->getError());
	}
}
if ($dbMCQuestion->isConnected) {
	$dbMCQuestion->close();
	$dbMCQuestion->isConnected = false;
}

$dbMCQuestionaire = new dbMultipleChoiceQuestionaire();
if ($dbMCQuestionaire->sqlTableExists()) {
	if (!$dbMCQuestionaire->sqlDeleteTable()) {
		$error .= sprintf('[UNINSTALL] %s', $dbMCQuestionaire->getError());
	}
}
if ($dbMCQuestionaire->isConnected) {
	$dbMCQuestionaire->close();
	$dbMCQuestionaire->isConnected = false;
}

$dbMCQuestionItem = new dbMultipleChoiceQuestionItem();
if ($dbMCQuestionItem->sqlTableExists()) {
	if (!$dbMCQuestionItem->sqlDeleteTable()) {
		$error .= sprintf('[UNINSTALL] %s', $dbMCQuestionItem->getError());
	}
}
if ($dbMCQuestionItem->isConnected) {
	$dbMCQuestionItem->close();
	$dbMCQuestionItem->isConnected = false;
}

// Prompt Errors
if (!empty($error)) {
	$admin->print_error($error);
}
	
?>