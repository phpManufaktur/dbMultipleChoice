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

// description of module
$module_description 	= 'dbMultipleChoice enables creation and implementation of multiple choice tests';

// name of the person(s) who translated and edited this language file
$module_translation_by = 'Armin Ipfelkofer';

define('mc_btn_abort',													'Cancel');
define('mc_btn_export',													'Export');
define('mc_btn_import',													'Import');
define('mc_btn_ok',															'OK');
define('mc_btn_reset',													'Reset questionnaire');
define('mc_btn_submit',													'Submit questionnaire');

define('mc_desc_cfg_groups',										'You may organize the questions in any groups. Separate groups by comma! The first entry is the default value.');
define('mc_desc_cfg_max_items',									'Define the maximum number of possible answers per question (Limit: <b>15</b>).');
define('mc_desc_cfg_min_items',									'Define the minimum number of possible answers per question.');
define('mc_desc_cfg_preselect_html',						'Define whether HTML is preset <i>for answers</i> when creating new questions (0=NO, 1=YES).');
define('mc_desc_cfg_remember_question',					'Im Eingabedialog für neue Fragen die Felder Gruppe, Bezeichner und Frage merken und bei der nächsten Eingabe als Vorgabe verwenden. (1=JA, 0=NEIN)');

define('mc_error_cfg_id',												'<p>Configuration data record with <b>ID %05d</b> could not be read!</p>');
define('mc_error_cfg_name',											'<p>For identifier <b>%s</b> no configuration data record was found!</p>');
define('mc_error_id_missing',										'<p>Data record with <b>ID %05d</b> was not found.</p>');
define('mc_error_id_invalid',										'<p>An invalid with <b>ID</b> has been submitted. No data could be read.</p>');
define('mc_error_questionaire_id',							'<p>The requested questionnaire with <b>ID %05d</b> is either locked or not existing.</p>');
define('mc_error_questions',										'<p>The questions assigned to questionnaire with <b>ID %05d</b> could not be read.</p>');
define('mc_error_answers',											'<p>The answers assigned to question with <b>ID %05d</b> could not be read.</p>');

define('mc_header_cfg',													'Settings');
define('mc_header_cfg_description',							'Description');
define('mc_header_cfg_identifier',							'Identifier');
define('mc_header_cfg_label',										'Label');
define('mc_header_cfg_typ',											'Type');
define('mc_header_cfg_value',										'Value');
define('mc_header_groups',											'Groups');
define('mc_header_id',													'ID');
define('mc_header_name',												'Name');
define('mc_header_question',										'Question');
define('mc_header_question_edit',								'Create or edit a question');
define('mc_header_question_list',								'List of available questions');
define('mc_header_questions_sum',								'∑');
define('mc_header_questionaire_edit',						'Create or edit a questionnaire');
define('mc_header_questionaire_list',						'List of available questionnaires');
define('mc_header_status',											'Status');
define('mc_header_title',												'Title');

define('mc_hint_shuffle_answers',								'Display answers in random order');
define('mc_hint_shuffle_questions',							'Display questions in random order');
define('mc_hint_use_html',											'<span class="small">Save the data record to store and activate the modified settings!</span>');

define('mc_intro_cfg',													'<p>Edit the settings for dbMultipleChoice.</p>');
define('mc_intro_cfg_add_item',									'<p>Adding entries in the configuration makes only sense when the values entered correspond with the program.</p>');
define('mc_intro_question_edit',								'<p>Create or edit the individual questions and assign each question to any group.</p>');
define('mc_intro_question_list',								'<p>Select the question you want to edit.</p><p>In order to create a new question select tab <b>Edit</b>.<p>');
define('mc_intro_questionaire_edit',						'<p>Compile a questionnaire with the questions available and define its behaviour.</p><p>Availability of individual questions is depending on groups selected and the behaviour of the questionnaire that you definded. Therefor you should save the questionnaire after each modification of the behaviour and selcet the questions subsequently.</p>');
define('mc_intro_questionaire_list',						'<p>Select the questionnaire you want to edit.</p><p>In order to create a new questionnaire select tab <b>%s</b>.</p>');
define('mc_intro_questionaire_questions',				'<p>Wählen Sie die Fragen aus, die im Fragebogen verwendet werden sollen.</p><p>Sie können die Fragen per Drag & Drop sortieren, wenn keine zufällige Ausgabe festgelegt ist werden die Fragen in der hier zu sehenden Reihenfolge verwendet.</p>');

define('mc_label_answer_id',										'Answer %s');
define('mc_label_answer_is_correct',						'the answer is <b>correct</b>');
define('mc_label_behaviour',										'Behaviour');
define('mc_label_cfg_groups',										'Group');
define('mc_label_cfg_max_items',								'Max. number of proposals');
define('mc_label_cfg_min_items',								'Min. number of proposals');
define('mc_label_cfg_preselect_html',						'Preset: HTML');
define('mc_label_cfg_remember_question',				'Fragen bei der Eingabe merken');
define('mc_label_csv_import',										'CSV Import');
define('mc_label_csv_export',										'CSV Export');
define('mc_label_description',									'Description');
define('mc_label_filter',												'Filter');
define('mc_label_hint_delete',									'löschen');
define('mc_label_hint_save_as',									'Hinweis speichern unter');
define('mc_label_hint_select',									'Hinweis auswählen');
define('mc_label_mode',													'Mode');
define('mc_label_name',													'Name');
define('mc_label_new_id',												'- <i>new data record</i> -');
define('mc_label_prompt_correct',								'Hint:<br />Answer to question is <b>correct</b>');
define('mc_label_prompt_false',									'Hint:<br />Answer to question is <b>wrong</b>');
define('mc_label_prompt_partial',								'Hint:<br />Question is <b>partially</b> answered');
define('mc_label_question',											'Question');
define('mc_label_question_groups',							'Group(s)<br /><span class="small">Multiple selection possible</span>');
define('mc_label_status',												'Status');
define('mc_label_title',												'Title');
define('mc_label_undefined',										'- not defined -');
define('mc_label_use_html',											'Use HTML');

define('mc_mode_qn_mixed',											'MIXED MODE - all questions will be used');
define('mc_mode_qn_multiple',										'MULTIPLE MODE, STRICT - will use only questions which have several correct answers');
define('mc_mode_qn_none',												'NONE MODE - will use only questions which have no correct answer at all');
define('mc_mode_qn_single',											'SINGLE MODE - will use only  questions which have only one correct answer');
define('mc_mode_qn_partial',										'MULTIPLE MODE, PERCENTAGE - will use only questions which have several correct answers');
define('mc_mode_multiple',											'SEVERAL Aanswers possible, STRICT evaluation');
define('mc_mode_none',													'NO answer is correct');
define('mc_mode_single',												'ONLY ONE answer is correct');
define('mc_mode_partial',												'SEVERAL answers are possible, PERCENTILE evaluation');

define('mc_msg_cfg_add_exists',									'<p>A configuration data record with the name <b>%s</b> is already existing and therefor cannot be added again!</p>');
define('mc_msg_cfg_add_incomplete',							'<p>The newly created configration data record is incomplete! Please check your entries!</p>');
define('mc_msg_cfg_add_success',								'<p>The configuration data record with <b>ID #%05d</b> and name <b>%s</b> has been added.</p>');
define('mc_msg_cfg_csv_export',									'<p>The configuration data hhave been saved as <b>%s</b> in /MEDIA directory.</p>');
define('mc_msg_cfg_id_updated',									'<p>The configuration data record with <b>ID #%05d</b> and name <b>%s</b> has been updated.</p>');
define('mc_msg_hint_deleted',										'<p>Der Hinweis mit der <b>ID %d</b> wurde entfernt.</p>');
define('mc_msg_hint_inserted',									'<p>Der Hinweis mit der Bezeichnung <b>%s</b> wurde hinzugefügt.</p>');
define('mc_msg_hint_inserted_locked',						'<p>Der Hinweis mit der Bezeichnung <b>%s</b> wurde hinzugefügt kann jedoch nicht in die Frage übernommen werden, da diese Fehler enthält und gesperrt ist.</p>');
define('mc_msg_hint_usage_locked',							'<p>Der Hinweis <b>%s</b> kann nicht eingefügt werden da die Frage Fehler enthält und gesperrt ist.</p>');
define('mc_msg_invalid_email',									'<p>The e-mail addresss <b>%s</b> is invalid. Please check your entry!</p>');
define('mc_msg_question_item_count_multiple',		'<p>In the mode you selected (<b>%s</b>) you have to define at least <b>one correct</b> answer!</p>');
define('mc_msg_question_item_count_none',				'<p>In the mode you selected (<b>%s</b>) there is <b>no answer</b> allowed. Please check your entries!</p>');
define('mc_msg_question_item_count_single',			'<p>In the mode you selected (<b>%s</b>) there is exactly one correct answer allowed. You have <b>%d</b> answers defined.</p>');
define('mc_msg_question_item_deleted',					'<p>Answer with <b>ID %05d</b> has beeen deleted!</p>');
define('mc_msg_question_item_inserted',					'<p>Answer with <b>ID %05d</b> has beeen added.</p>');
define('mc_msg_question_items_max',							'<p>You have defined <b>%d</b> possible answers, while only <b>%d</b> answers are allowed.</p>');
define('mc_msg_question_items_min',							'<p>You have defined <b>%d</b> possible answers, while at least <b>%d</b> answers are required.</p>');
define('mc_msg_question_item_updated',					'<p>Answer with <b>ID %05d</b> has been updated.</p>');
define('mc_msg_question_locked_temp',						'<p>The data record contains errors and has provisionally been locked. Automatic unlock will occur as soon as the errors have been corrected</p>');
define('mc_msg_question_no_group',							'<p>You have to select at least one <b>group</b>!</p>');
define('mc_msg_question_no_questions',					'<p>There are no questions definet yet. Please create one or more questions by means of <b>%s</b>.</p>');
define('mc_msg_question_name_missing',					'<p>You have do enter an <b>identifier</b> for the question!</p>');
define('mc_msg_question_question_missing',			'<p>Please enter a <b>question</b>!</p>');
define('mc_msg_question_remembered',						'<p><b>Hinweis:</b> Sie erstellen eine neue Frage bei der die Felder teilweise mit den Werten der letzten Frage die Sie bearbeitet haben vorbelegt sind!</p>');
define('mc_msg_question_inserted',							'<p>The data record with <b>ID %05d</b> has been added.</p>');
define('mc_msg_question_updated',								'<p>The data record with <b>ID %05d</b> has been updated.</p>');
define('mc_msg_questionaire_groups_empty',			'<p>No <b>groups</b> are selected.</p><p>Please select one or more groups, define the behaviour of the questionnaire, and save the questionnaire so the available questions may be selected.</p>');
define('mc_msg_questionaire_inserted',					'<p>The questionnaire with <b>ID %05d</b> has been added.</p>');
define('mc_msg_questionaire_name_missing',			'<p>You have to define an <b>identifier</b> for the questionnaire!</p>');
define('mc_msg_questionaire_list_empty',				'<p>Ther are no questionnaires defined yet. Creat one or more new questionnaires via <b>%s</b>.</p>');
define('mc_msg_questionaire_title_missing',			'<p>You have to define a <b>title</b> for the questionnaire!</p>');
define('mc_msg_questionaire_updated',						'<p>The questionnaire with <b>ID %05d</b> has been updated.</p>');
define('mc_msg_solutions_mixed',								'<p>You have answer correctly %01.2f%% of the questions.</p>');
define('mc_msg_solutions_strict',								'<p>You have answerrd %d of %d questions correctly.</p>');

define('mc_status_active',											'Active');
define('mc_status_deleted',											'Deleted');
define('mc_status_locked',											'Locked');

define('mc_tab_config',													'Settings');
define('mc_tab_question',												'Questions');
define('mc_tab_question_edit',									'Edit question');
define('mc_tab_question_list',									'List of questions');
define('mc_tab_questionaire',										'Questionnaires');
define('mc_tab_questionaire_edit',							'Edit questionnaire');
define('mc_tab_questionaire_list',							'List of questionnaires');
define('mc_tab_report',													'Report');
define('mc_tab_help',														'?');

define('mc_text_create_new_hint',								'- create new hint -');
define('mc_text_question_grps_select',					'komplette Gruppe von Fragen auswählen: %s und zum Markieren auf "Übernehmen" klicken');
define('mc_text_select',												'- bitte auswählen -');
define('mc_text_select_filter',									'- kein Filter ausgewählt -');

?>