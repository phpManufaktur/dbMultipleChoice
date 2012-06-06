//:Execute a Multiple Choice Test created with dbMultipleChoice
//:Usage: [[mc_questionaire?id=1&show_correct_answer=0&url=domain.tld]]
/**
 * dbMultipleChoice
 * (c) 2010 by Ralf Hertsch
 * ralf.hertsch@phpmanufaktur.de - http://phpManufaktur.de
 *
 * $Id$
 */
if (file_exists(WB_PATH.'/modules/multiplechoice/class.frontend.php')) {
	require_once(WB_PATH.'/modules/multiplechoice/class.frontend.php');
	isset($id) ? $qid = $id : $qid = -1;
	$show_answer = isset($show_correct_answer) ? (bool) $show_correct_answer : false; 
	$use_url = isset($url) ? $url : '';
	$success_url = isset($success_url) ? $success_url : ''; 
	$quest = new multipleChoiceFrontend($qid, $show_answer, $use_url, $success_url);
	return $quest->action(); 
}
else {
	return "<p>FATAL ERROR: <b>dbMultipleChoice</b> ist not installed!</p>";
}
