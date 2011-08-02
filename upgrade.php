<?php

/**
 * dbMultipleChoice
 * 
 * @author Ralf Hertsch (ralf.hertsch@phpmanufaktur.de)
 * @link http://phpmanufaktur.de
 * @copyright 2011
 * @license GNU GPL (http://www.gnu.org/licenses/gpl.html)
 * @version $Id$
 * 
 * FOR VERSION- AND RELEASE NOTES PLEASE LOOK AT INFO.TXT!
 */

// try to include LEPTON class.secure.php to protect this file and the whole CMS!
if (defined('WB_PATH')) {	
	if (defined('LEPTON_VERSION')) include(WB_PATH.'/framework/class.secure.php');
} elseif (file_exists($_SERVER['DOCUMENT_ROOT'].'/framework/class.secure.php')) {
	include($_SERVER['DOCUMENT_ROOT'].'/framework/class.secure.php'); 
} else {
	$subs = explode('/', dirname($_SERVER['SCRIPT_NAME']));	$dir = $_SERVER['DOCUMENT_ROOT'];
	$inc = false;
	foreach ($subs as $sub) {
		if (empty($sub)) continue; $dir .= '/'.$sub;
		if (file_exists($dir.'/framework/class.secure.php')) { 
			include($dir.'/framework/class.secure.php'); $inc = true;	break; 
		} 
	}
	if (!$inc) trigger_error(sprintf("[ <b>%s</b> ] Can't include LEPTON class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
}
// end include LEPTON class.secure.php

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