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

if (!defined('WYSIWYG_EDITOR') || 
		WYSIWYG_EDITOR == "none" || 
		!file_exists(WB_PATH.'/modules/'.WYSIWYG_EDITOR.'/include.php')) {
	// no WYSIWYG editor present...
	function show_wysiwyg_editor($name, $id, $content, $width, $height) {
			echo '<textarea name="'.$name.'" id="'.$id.'" style="width: '.$width.'; height: '.$height.';">'.$content.'</textarea>';
	}
} 
else {
	// include default WYSIWYG editor
	require_once(WB_PATH.'/modules/'.WYSIWYG_EDITOR.'/include.php');
}


?>