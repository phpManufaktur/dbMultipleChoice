<?php

/**
 * dbMultipleChoice
 *
 * @author Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @link https://addons.phpmanufaktur.de/de/name/dbmultiplechoice.php
 * @copyright 2010 - 2012 phpManufaktur by Ralf Hertsch
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

$module_directory     = 'multiplechoice';
$module_name          = 'dbMultipleChoice';
$module_function      = 'tool';
$module_version       = '0.20';
$module_status        = 'stable';
$module_platform      = '2.8';
$module_author        = 'phpManufaktur, Berlin (Germany)';
$module_license       = 'GNU General Public License';
$module_description   = 'Multiple Choice tests for WebsiteBaker';
$module_home          = 'http://phpmanufaktur.de';
$module_guid          = '220B9C46-E78D-40C8-90EB-C0B3A0CD6123';

?>