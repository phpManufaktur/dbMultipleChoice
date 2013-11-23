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

require_once(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/class.droplets.php');
require_once(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/initialize.php');

global $admin;

$error = '';

$tables = array('dbMultipleChoiceCfg', 'dbMultipleChoiceQuestion', 'dbMultipleChoiceQuestionaire', 'dbMultipleChoiceQuestionItem', 'dbMultipleChoiceQuestionHint', 'dbMultipleChoiceTableSort');

$create = null;
foreach ($tables as $table) {
	unset($create);
	$create = new $table();
	if (!$create->sqlTableExists()) {
		if (!$create->sqlCreateTable()) {
			$error .= sprintf('[INSTALLATION] %s', $create->getError());
		}
	}
}

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