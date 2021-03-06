<?php

/**
 * dbMultipleChoice
 *
 * @author Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @link https://phpmanufaktur.de
 * @copyright 2010 - 2013 phpManufaktur by Ralf Hertsch
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */


// Mindestparameter gesetzt?
if (!isset($_POST['rowID']) || !isset($_POST['sorter_table'])) exit();
// Sorter ausgeschaltet?
if (isset($_POST['sorter_active']) && ($_POST['sorter_active'] == 0)) exit();

require_once('../../config.php');
require_once(WB_PATH.'/framework/initialize.php');

global $database;

$sorter_table = $_POST['sorter_table'];
switch ($sorter_table):
case 'mod_mc_question':
case 'mod_mc_questionaire':
    // Frageboegen sortieren
    $rowIDs = implode(',', $_POST['rowID']);
    $sorter_value = $_POST['sorter_value'];
    $SQL = sprintf(    "UPDATE %smod_mc_table_sort SET sort_order='%s' WHERE sort_table='%s' AND sort_value='%s'",
                                    TABLE_PREFIX, $rowIDs, $sorter_table, $sorter_value);
    $database->query($SQL);
    if ($database->is_error()) {
        echo $database->get_error();
    }
    else {
        echo "Sorted: $rowIDs";
    }
    break;
default:
    echo "no handling defined for: ".$_POST['sorter_table'];
endswitch;
?>
