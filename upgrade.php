<?php

/**
 * dbMultipleChoice
 *
 * @author Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @link https://phpmanufaktur.de
 * @copyright 2010 - 2013 phpManufaktur by Ralf Hertsch
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

// include class.secure.php to protect this file and the whole CMS!
if (defined('WB_PATH')) {
  if (defined('LEPTON_VERSION'))
    include(WB_PATH.'/framework/class.secure.php');
}
else {
  $oneback = "../";
  $root = $oneback;
  $level = 1;
  while (($level < 10) && (!file_exists($root.'/framework/class.secure.php'))) {
    $root .= $oneback;
    $level += 1;
  }
  if (file_exists($root.'/framework/class.secure.php')) {
    include($root.'/framework/class.secure.php');
  }
  else {
    trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
  }
}
// end include class.secure.php

require_once(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/initialize.php');
require_once(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/class.droplets.php');

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

// remove Droplets
$dbDroplets = new dbDroplets();
$where = array(dbDroplets::field_name => 'mc_questionaire');
if (!$dbDroplets->sqlDeleteRecord($where)) {
	$message = sprintf('[UPGRADE] Error uninstalling Droplet: %s', $dbDroplets->getError());
}


// Install Droplets
$droplets = new checkDroplets();

if ($droplets->insertDropletsIntoTable()) {
  $message = 'Droplets for dbMultipleChoice successfull updated';
}
else {
  $message = sprintf('Error updating droplets for dbMultipleChoice: %s', $droplets->getError());
}
if ($message != "") {
  echo '<script language="javascript">alert ("'.$message.'");</script>';
}


// Prompt Errors
if (!empty($error)) {
	$admin->print_error($error);
}

?>