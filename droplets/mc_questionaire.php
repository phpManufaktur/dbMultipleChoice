//:Execute a Multiple Choice Test created with dbMultipleChoice
//:Usage: [[mc_questionaire?id=1&show_correct_answer=0&url=domain.tld]]
/**
 * dbMultipleChoice
 *
 * @author Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @link http://phpmanufaktur.de
 * @copyright 2010 - 2012
 * @license http://www.gnu.org/licenses/gpl.html GNU Public License (GPL)
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
