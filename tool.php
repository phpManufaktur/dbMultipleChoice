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

// prevent this file from being accessed directly
if (!defined('WB_PATH')) die('invalid call of '.$_SERVER['SCRIPT_NAME']);

require_once(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/initialize.php');
require_once(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/class.editor.php');

if (DEBUG_MODE) {
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
}
else {
	ini_set('display_errors', 0);
	error_reporting(E_ERROR);
}

global $parser;
global $tools;
global $dbCfg;
global $dbMCQuestion;
global $dbMCQuestionaire;
global $dbMCQuestionItem;
global $dbMCQuestionHint;
global $dbMCTableSort;

if (!is_object($parser)) $parser = new Dwoo();
if (!is_object($tools)) $tools = new rhTools();
if (!is_object($dbCfg)) $dbCfg = new dbMultipleChoiceCfg();
if (!is_object($dbMCQuestion)) $dbMCQuestion = new dbMultipleChoiceQuestion();
if (!is_object($dbMCQuestionaire)) $dbMCQuestionaire = new dbMultipleChoiceQuestionaire();
if (!is_object($dbMCQuestionItem)) $dbMCQuestionItem = new dbMultipleChoiceQuestionItem();
if (!is_object($dbMCQuestionHint)) $dbMCQuestionHint = new dbMultipleChoiceQuestionHint();
if (!is_object($dbMCTableSort)) $dbMCTableSort = new dbMultipleChoiceTableSort();

$mcBackend = new mcBackend();
$mcBackend->action();

class mcBackend {

	const request_action									= 'act';
	const request_sub_action							= 'sa';
	const request_csv_export							= 'csvex';
	const request_csv_import							= 'csvim';
	const request_items										= 'its';
	const request_question_tab						= 'qt';
	const request_questionaire_tab				= 'qnt';
	const request_hint_correct_select			= 'hcsel';
	const request_hint_correct_save				= 'hcsav';
	const request_hint_correct_delete			= 'hcdel';
	const request_hint_correct_name				= 'hcnam';
	const request_hint_false_select				= 'hfsel';
	const request_hint_false_save					= 'hfsav';
	const request_hint_false_name					= 'hfnam';
	const request_hint_false_delete				= 'hfdel';
	const request_hint_partial_select			= 'hpsel';
	const request_hint_partial_save				= 'hpsav';
	const request_hint_partial_name				= 'hpnam';
	const request_hint_partial_delete			= 'hpdel';
	const request_questionaire_filter			= 'qnflt';
	const request_question_select_grps		= 'qselgrp';

	const action_default									= 'def';
	const action_help											= 'hlp';
	const action_question									= 'qst';
	const action_question_edit						= 'qste';
	const action_question_edit_check			= 'qstechk';
	const action_question_tab_list				= 'qtl';
	const action_question_tab_edit				= 'qte';
	const action_questionaire							= 'qnr';
	const action_questionaire_edit				= 'qne';
	const action_questionaire_edit_check 	= 'qnec';
	const action_questionaire_tab_list		= 'qntl';
	const action_questionaire_tab_edit		= 'qnte';
	const action_report										= 'rep';
	const action_config										= 'cfg';
	const action_config_check							= 'cfgc';

	private $tab_navigation_array = array(
		self::action_questionaire			=> mc_tab_questionaire,
		self::action_question					=> mc_tab_question,
		//self::action_report						=> mc_tab_report,
		self::action_config						=> mc_tab_config,
		self::action_help							=> mc_tab_help
	);

	private $tab_question_array = array(
		self::action_question_tab_list		=> mc_tab_question_list,
		self::action_question_tab_edit		=> mc_tab_question_edit
	);

	private $tab_questionaire_array = array(
		self::action_questionaire_tab_list	=> mc_tab_questionaire_list,
		self::action_questionaire_tab_edit	=> mc_tab_questionaire_edit
	);

	private $page_link 							= '';
	private $img_url								= '';
	private $template_path					= '';
	private $help_path							= '';
	private $error									= '';
	private $message								= '';

	private $swNavHide							= array();

	/**
	 * Konstruktor
	 */
	public function __construct() {
		$this->page_link = ADMIN_URL.'/admintools/tool.php?tool=multiplechoice';
		$this->template_path = WB_PATH . '/modules/' . basename(dirname(__FILE__)) . '/htt/' ;
		$this->help_path = WB_PATH . '/modules/' . basename(dirname(__FILE__)) . '/languages/' ;
		$this->img_url = WB_URL.'/modules/'.basename(dirname(__FILE__)).'/img/';
	} // __construct()

	/**
    * Set $this->error to $error
    *
    * @param STR $error
    */
  public function setError($error) {
    $this->error = $error;
  } // setError()

  /**
    * Get Error from $this->error;
    *
    * @return STR $this->error
    */
  public function getError() {
    return $this->error;
  } // getError()

  /**
    * Check if $this->error is empty
    *
    * @return BOOL
    */
  public function isError() {
    return (bool) !empty($this->error);
  } // isError

  /**
   * Reset Error to empty String
   */
  public function clearError() {
  	$this->error = '';
  }

  /** Set $this->message to $message
    *
    * @param STR $message
    */
  public function setMessage($message) {
    $this->message = $message;
  } // setMessage()

  /**
    * Get Message from $this->message;
    *
    * @return STR $this->message
    */
  public function getMessage() {
    return $this->message;
  } // getMessage()

  /**
    * Check if $this->message is empty
    *
    * @return BOOL
    */
  public function isMessage() {
    return (bool) !empty($this->message);
  } // isMessage

  /**
   * Return Version of Module
   *
   * @return FLOAT
   */
  public function getVersion() {
    // read info.php into array
    $info_text = file(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/info.php');
    if ($info_text == false) {
      return -1;
    }
    // walk through array
    foreach ($info_text as $item) {
      if (strpos($item, '$module_version') !== false) {
        // split string $module_version
        $value = explode('=', $item);
        // return floatval
        return floatval(preg_replace('([\'";,\(\)[:space:][:alpha:]])', '', $value[1]));
      }
    }
    return -1;
  } // getVersion()

  /**
   * return the official supported languages of KIT
   */
  public function getSupportedLanguages() {
    // read info.php into array
    $info_text = file(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/info.php');
    if ($info_text == false) {
      return false;
    }
    // walk through array
    foreach ($info_text as $item) {
      if (strpos($item, '$module_languages') !== false) {
        // split string $module_version
        $value = explode('=', $item);
        // return floatval
        return explode(',', $value);
      }
    }
    return false;
  } // getSupportedLanguages()


  /**
   * Verhindert XSS Cross Site Scripting
   *
   * @param REFERENCE $_REQUEST Array
   * @return $request
   */
	public function xssPrevent(&$request) {
  	if (is_string($request)) {
	    $request = html_entity_decode($request);
	    $request = strip_tags($request);
	    $request = trim($request);
	    $request = stripslashes($request);
  	}
	  return $request;
  } // xssPrevent()

  public function action() {
  	// fields with HTML code
  	$html_allowed = array('qi_question_0', 'qi_question_1', 'qi_question_2', 'qi_question_3', 'qi_question_4', 'qi_question_5',
  												'qi_question_6', 'qi_question_8', 'qi_question_9', 'qi_question_10', 'qi_question_11', 'qi_question_12',
  												'qi_question_13', 'qi_question_14', 'qi_question_15',
  												dbMultipleChoiceQuestion::field_description, dbMultipleChoiceQuestion::field_prompt_correct,
  												dbMultipleChoiceQuestion::field_prompt_false, dbMultipleChoiceQuestion::field_prompt_partial,
  												dbMultipleChoiceQuestionaire::field_description);
  	foreach ($_REQUEST as $key => $value) {
  		if (!in_array($key, $html_allowed)) {
  			$_REQUEST[$key] = $this->xssPrevent($value);
  		}
  	}
    isset($_REQUEST[self::request_action]) ? $action = $_REQUEST[self::request_action] : $action = self::action_default;
  	switch ($action):
  	case self::action_config:
  		$this->show(self::action_config, $this->dlgConfig());
  		break;
  	case self::action_config_check:
  		$this->show(self::action_config, $this->configCheck());
  		break;
  	case self::action_question:
  		$this->show(self::action_question, $this->dlgQuestion());
  		break;
  	case self::action_help:
  		$this->show(self::action_help, $this->dlgHelp());
  		break;
  	case self::action_default:
  	default:
  		$this->show(self::action_questionaire, $this->dlgQuestionaire());
  		break;
  	endswitch;
  } // action


  /**
   * Erstellt eine Navigationsleiste
   *
   * @param $action - aktives Navigationselement
   * @return STR Navigationsleiste
   */
  public function getNavigation($action) {
  	$result = '';
  	foreach ($this->tab_navigation_array as $key => $value) {
  		if (!in_array($key, $this->swNavHide)) {
	  		($key == $action) ? $selected = ' class="selected"' : $selected = '';
	  		$result .= sprintf(	'<li%s><a href="%s">%s</a></li>',
	  												$selected,
	  												sprintf('%s&%s=%s', $this->page_link, self::request_action, $key),
	  												$value
	  												);
  		}
  	}
  	$result = sprintf('<ul class="nav_tab">%s</ul>', $result);
  	return $result;
  } // getNavigation()


  /**
   * Ausgabe des formatierten Ergebnis mit Navigationsleiste
   *
   * @param $action - aktives Navigationselement
   * @param $content - Inhalt
   *
   * @return ECHO RESULT
   */
  public function show($action, $content) {
  	global $parser;
  	if ($this->isError()) {
  		$content = $this->getError();
  		$class = ' class="error"';
  	}
  	else {
  		$class = '';
  	}
  	$data = array(
  		'WB_URL'					=> WB_URL,
  		'navigation' 			=> $this->getNavigation($action),
  		'class'						=> $class,
  		'content'					=> $content,
  	);
  	$parser->output($this->template_path.'backend.body.htt', $data);
  } // show()

  /**
   * Konfigurationsdialog
   *
   * @return STR dialog
   */
  public function dlgConfig() {
  	global $parser;
  	global $dbCfg;
		$SQL = sprintf(	"SELECT * FROM %s WHERE NOT %s='%s' ORDER BY %s",
										$dbCfg->getTableName(),
										dbMultipleChoiceCfg::field_status,
										dbMultipleChoiceCfg::status_deleted,
										dbMultipleChoiceCfg::field_name);
		$config = array();
		if (!$dbCfg->sqlExec($SQL, $config)) {
			$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbCfg->getError()));
			return false;
		}
		$count = array();
		$items = sprintf(	'<tr><th>%s</th><th>%s</th><th>%s</th></tr>',
											'&nbsp;',
											mc_header_cfg_value,
											mc_header_cfg_description );
		$row = '<tr><td>%s</td><td>%s</td><td>%s</td></tr>';
		// bestehende Eintraege auflisten
		foreach ($config as $entry) {
			$id = $entry[dbMultipleChoiceCfg::field_id];
			$count[] = $id;
			$label = constant($entry[dbMultipleChoiceCfg::field_label]);
			(isset($_REQUEST[dbMultipleChoiceCfg::field_value.'_'.$id])) ?
				$val = $_REQUEST[dbMultipleChoiceCfg::field_value.'_'.$id] :
				$val = $entry[dbMultipleChoiceCfg::field_value];
				// Hochkommas maskieren
				$val = str_replace('"', '&quot;', $val);
			$value = sprintf(	'<input type="text" name="%s_%s" value="%s" />', dbMultipleChoiceCfg::field_value, $id,	$val);
			$desc = constant($entry[dbMultipleChoiceCfg::field_description]);
			$items .= sprintf($row, $label, $value, $desc);
		}
		$items_value = implode(",", $count);

		// Checkbox fuer CSV Export
		$items .= sprintf('<tr><td>&nbsp;</td><td colspan="2"><input type="checkbox" name="%s" value="1">&nbsp;%s</td></tr>',
											self::request_csv_export,
											mc_label_csv_export);

		// Mitteilungen anzeigen
		if ($this->isMessage()) {
			$intro = sprintf('<div class="message">%s</div>', $this->getMessage());
		}
		else {
			$intro = sprintf('<div class="intro">%s</div>', mc_intro_cfg);
		}
		$data = array(
			'form_name'						=> 'konfiguration',
			'form_action'					=> $this->page_link,
			'action_name'					=> self::request_action,
			'action_value'				=> self::action_config_check,
			'items_name'					=> self::request_items,
			'items_value'					=> $items_value,
			'header'							=> mc_header_cfg,
			'intro'								=> $intro,
			'items'								=> $items,
			'btn_ok'							=> mc_btn_ok,
			'btn_abort'						=> mc_btn_abort,
			'abort_location'			=> $this->page_link
		);
		return $parser->get($this->template_path.'backend.cfg.htt', $data);
	} // dlgConfig()

	/**
	 * Ueberprueft Aenderungen die im Dialog dlgConfig() vorgenommen wurden
	 * und aktualisiert die entsprechenden Datensaetze.
	 * Fuegt neue Datensaetze ein.
	 *
	 * @return STR DIALOG dlgConfig()
	 */
	public function configCheck() {
		global $tools;
		global $dbCfg;
		$message = '';
		// ueberpruefen, ob ein Eintrag geaendert wurde
		if ((isset($_REQUEST[self::request_items])) && (!empty($_REQUEST[self::request_items]))) {
			$ids = explode(",", $_REQUEST[self::request_items]);
			foreach ($ids as $id) {
				if (isset($_REQUEST[dbMultipleChoiceCfg::field_value.'_'.$id])) {
					$value = $_REQUEST[dbMultipleChoiceCfg::field_value.'_'.$id];
					$where = array();
					$where[dbMultipleChoiceCfg::field_id] = $id;
					$config = array();
					if (!$dbCfg->sqlSelectRecord($where, $config)) {
						$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbCfg->getError()));
						return false;
					}
					if (sizeof($config) < 1) {
						$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, sprintf(mc_error_cfg_id, $id)));
						return false;
					}
					$config = $config[0];
					if ($config[dbMultipleChoiceCfg::field_value] != $value) {
						// Wert wurde geaendert
						if (!$dbCfg->setValue($value, $id) && $dbCfg->isError()) {
							$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbCfg->getError()));
							return false;
						}
						elseif ($dbCfg->isMessage()) {
							$message .= $dbCfg->getMessage();
						}
						else {
							// Datensatz wurde aktualisiert
							$message .= sprintf(mc_msg_cfg_id_updated, $id, $config[dbMultipleChoiceCfg::field_name]);
						}
					}
				}
			}
		}
		// ueberpruefen, ob ein neuer Eintrag hinzugefuegt wurde
		if ((isset($_REQUEST[dbMultipleChoiceCfg::field_name])) && (!empty($_REQUEST[dbMultipleChoiceCfg::field_name]))) {
			// pruefen ob dieser Konfigurationseintrag bereits existiert
			$where = array();
			$where[dbMultipleChoiceCfg::field_name] = $_REQUEST[dbMultipleChoiceCfg::field_name];
			$where[dbMultipleChoiceCfg::field_status] = dbMultipleChoiceCfg::status_active;
			$result = array();
			if (!$dbCfg->sqlSelectRecord($where, $result)) {
				$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbCfg->getError()));
				return false;
			}
			if (sizeof($result) > 0) {
				// Eintrag existiert bereits
				$message .= sprintf(mc_msg_cfg_add_exists, $where[dbMultipleChoiceCfg::field_name]);
			}
			else {
				// Eintrag kann hinzugefuegt werden
				$data = array();
				$data[dbMultipleChoiceCfg::field_name] = $_REQUEST[dbMultipleChoiceCfg::field_name];
				if (((isset($_REQUEST[dbMultipleChoiceCfg::field_type])) && ($_REQUEST[dbMultipleChoiceCfg::field_type] != dbMultipleChoiceCfg::type_undefined)) &&
						((isset($_REQUEST[dbMultipleChoiceCfg::field_value])) && (!empty($_REQUEST[dbMultipleChoiceCfg::field_value]))) &&
						((isset($_REQUEST[dbMultipleChoiceCfg::field_label])) && (!empty($_REQUEST[dbMultipleChoiceCfg::field_label]))) &&
						((isset($_REQUEST[dbMultipleChoiceCfg::field_description])) && (!empty($_REQUEST[dbMultipleChoiceCfg::field_description])))) {
					// Alle Daten vorhanden
					unset($_REQUEST[dbMultipleChoiceCfg::field_name]);
					$data[dbMultipleChoiceCfg::field_type] = $_REQUEST[dbMultipleChoiceCfg::field_type];
					unset($_REQUEST[dbMultipleChoiceCfg::field_type]);
					$data[dbMultipleChoiceCfg::field_value] = $_REQUEST[dbMultipleChoiceCfg::field_value];
					unset($_REQUEST[dbMultipleChoiceCfg::field_value]);
					$data[dbMultipleChoiceCfg::field_label] = $_REQUEST[dbMultipleChoiceCfg::field_label];
					unset($_REQUEST[dbMultipleChoiceCfg::field_label]);
					$data[dbMultipleChoiceCfg::field_description] = $_REQUEST[dbMultipleChoiceCfg::field_description];
					unset($_REQUEST[dbMultipleChoiceCfg::field_description]);
					$data[dbMultipleChoiceCfg::field_status] = dbMultipleChoiceCfg::status_active;
					$data[dbMultipleChoiceCfg::field_update_by] = $tools->getDisplayName();
					$data[dbMultipleChoiceCfg::field_update_when] = date('Y-m-d H:i:s');
					$id = -1;
					if (!$dbCfg->sqlInsertRecord($data, $id)) {
						$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbCfg->getError()));
						return false;
					}
					$message .= sprintf(mc_msg_cfg_add_success, $id, $data[dbMultipleChoiceCfg::field_name]);
				}
				else {
					// Daten unvollstaendig
					$message .= mc_msg_cfg_add_incomplete;
				}
			}
		}
		// Sollen Daten als CSV gesichert werden?
		if ((isset($_REQUEST[self::request_csv_export])) && ($_REQUEST[self::request_csv_export] == 1)) {
			// Daten sichern
			$where = array();
			$where[dbMultipleChoiceCfg::field_status] = dbMultipleChoiceCfg::status_active;
			$csv = array();
			$csvFile = WB_PATH.MEDIA_DIRECTORY.'/'.date('ymd-His').'-glossary-cfg.csv';
			if (!$dbCfg->csvExport($where, $csv, $csvFile)) {
				$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbCfg->getError()));
				return false;
			}
			$message .= sprintf(mc_msg_cfg_csv_export, basename($csvFile));
		}

		if (!empty($message)) $this->setMessage($message);
		return $this->dlgConfig();
	} // checkConfig()

	/**
	 * Übergeordneter Dialog, verwaltet die Aktionen und zeigt die TABs der untergeordneten Dialoge an
	 *
	 * @return STR dlgQuestion()
	 */
  public function dlgQuestion() {
  	$question_tab = '';
  	(isset($_REQUEST[self::request_question_tab])) ? $action = $_REQUEST[self::request_question_tab] : $action = self::action_question_tab_list;
  	foreach ($this->tab_question_array as $key => $value) {
  		($key== $action) ? $selected = ' class="selected"' : $selected = '';
  		$question_tab .= sprintf(	'<li%s><a href="%s">%s</a></li>',
	  														$selected,
	  														sprintf('%s&%s=%s&%s=%s', $this->page_link, self::request_action, self::action_question, self::request_question_tab, $key),
	  														$value
	  													);
  	}
  	$question_tab = sprintf('<ul class="nav_tab">%s</ul>', $question_tab);

  	switch ($action):
  	case self::action_question_tab_edit:
  		(isset($_REQUEST[self::request_sub_action])) ? $sub_action = $_REQUEST[self::request_sub_action] : $sub_action = self::action_default;
  		switch ($sub_action):
  		case self::action_question_edit_check:
  			$result = $this->checkQuestion();
  			break;
  		case self::action_question_edit:
  		default:
  			$result = $this->dlgQuestionEdit();
  		endswitch;
  		break;
  	case self::action_question_tab_list:
  	default:
  		$result = $this->dlgQuestionList();
  	endswitch;
  	$result = sprintf('<div class="question_container">%s%s</div>', $question_tab, $result);
  	return $result;
  } // dlgQuestion()

  /**
   * Zeigt eine Liste der existierenden Fragen an
   *
   * @return STR dlgQuestionList()
   */
	public function dlgQuestionList() {
		global $dbMCQuestion;
		global $parser;
		$SQL = sprintf(	"SELECT * FROM %s WHERE %s != '%s'",
										$dbMCQuestion->getTableName(),
										dbMultipleChoiceQuestion::field_status,
										dbMultipleChoiceQuestion::status_deleted);
		$list = array();
		if (!$dbMCQuestion->sqlExec($SQL, $list)) {
			$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCQuestion->getError()));
			return false;
		}
		$items = '';
		if (count($list) < 1) {
			// es sind noch keine Fragen definiert
			$this->setMessage(sprintf(mc_msg_question_no_questions, $this->tab_question_array[self::action_question_tab_edit]));
		}
		else {
			$data = array(
				'id'					=> mc_header_id,
				'name'				=> mc_header_name,
				'question'		=> mc_header_question,
				'groups'			=> mc_header_groups,
				'status'			=> mc_header_status
			);
			$items .= $parser->get($this->template_path.'backend.question.list.header.htt', $data);
		}
		$row = new Dwoo_Template_File($this->template_path.'backend.question.list.row.htt');
		$flipflop = true;
		foreach ($list as $item) {
			$flipflop ? $flipper = 'flip' : $flipper = 'flop';
  		$flipflop ? $flipflop = false : $flipflop = true;
  		(($item[dbMultipleChoiceQuestion::field_status] == dbMultipleChoiceQuestion::status_locked) ||
			 ($item[dbMultipleChoiceQuestion::field_status] == dbMultipleChoiceQuestion::status_locked_temp)) ?
			 $class = 'mc_item_locked' : $class = 'mc_item_normal';
			$data = array(
				'flipflop'		=> $flipper,
				'id'					=>	sprintf('<span class="%s">%s</span>',
																	$class,
																	sprintf('<a href="%s">%s</a>',
																					sprintf('%s&%s=%s&%s=%s&%s=%s&%s=%s',
																									$this->page_link,
																									self::request_action,
																									self::action_question,
																									self::request_question_tab,
																									self::action_question_tab_edit,
																									self::request_sub_action,
																									self::action_question_edit,
																									dbMultipleChoiceQuestion::field_id,
																									$item[dbMultipleChoiceQuestion::field_id]),
																					sprintf('%05d', $item[dbMultipleChoiceQuestion::field_id]))),
				'name'				=> sprintf('<span class="%s">%s</span>', $class, $item[dbMultipleChoiceQuestion::field_name]),
				'question'		=> sprintf('<span class="%s" title="%s">%s</span>', $class, strip_tags($item[dbMultipleChoiceQuestion::field_description]), $item[dbMultipleChoiceQuestion::field_question]),
				'groups'			=> sprintf('<span class="%s">%s</span>', $class, str_replace(',', ', ', $item[dbMultipleChoiceQuestion::field_groups])),
				'status'			=> sprintf('<span class="%s">%s</span>', $class, $dbMCQuestion->status_array[$item[dbMultipleChoiceQuestion::field_status]])
			);
			$items .= $parser->get($row, $data);
		}

		// Mitteilungen anzeigen
		if ($this->isMessage()) {
			$intro = sprintf('<div class="message">%s</div>', $this->getMessage());
		}
		else {
			$intro = sprintf('<div class="intro">%s</div>', mc_intro_question_list);
		}

		$data = array(
			'header'				=> mc_header_question_list,
			'intro'					=> $intro,
			'items'					=> $items
		);
		return $parser->get($this->template_path.'backend.question.list.htt', $data);
	} // dlgQuestionList()

	/**
	 * Dialog zum Erstellen und Bearbeiten von Fragen
	 *
	 * @return STR dlgQuestionEdit()
	 */
	public function dlgQuestionEdit() {
		global $dbMCQuestion;
		global $dbMCQuestionItem;
		global $dbCfg;
		global $parser;
		global $dbMCQuestionHint;

		(isset($_REQUEST[dbMultipleChoiceQuestion::field_id])) ? $qid = $_REQUEST[dbMultipleChoiceQuestion::field_id] : $qid = -1;

		// Gruppen auslesen
		$groups = $dbCfg->getValue(dbMultipleChoiceCfg::cfgGroups);
		// max. Anzahl an Fragen
		$max_items = $dbCfg->getValue(dbMultipleChoiceCfg::cfgMaxItems);
		// HTML Voreinstellung
		$preselect_html = $dbCfg->getValue(dbMultipleChoiceCfg::cfgPreselectHTML);

		if ($qid != -1) {
			// bestehender Datensatz
			$where = array();
			$where[dbMultipleChoiceQuestion::field_id] = $qid;
			$question = array();
			if (!$dbMCQuestion->sqlSelectRecord($where, $question)) {
				$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCQuestion->getError()));
				return false;
			}
			if (count($question) < 1) {
				// Datensatz nicht gefunden
				$this->setError(sprintf(mc_error_id_missing, $qid));
				return false;
			}
			$question = $question[0];
		}
		else {
			// neuer Datensatz
			$question = $dbMCQuestion->getFields();
			// Voreinstellungen
			$question[dbMultipleChoiceQuestion::field_id] = $qid;
			$question[dbMultipleChoiceQuestion::field_groups] = $groups[0]; // erste Gruppe = DEFAULT
			$question[dbMultipleChoiceQuestion::field_use_html]	= (int) $preselect_html;
			$question[dbMultipleChoiceQuestion::field_mode] = dbMultipleChoiceQuestion::mode_multiple;
			if ($dbCfg->getValue(dbMultipleChoiceCfg::cfgRememberQuestion)) {
				// Daten aus der letzten Frage als Vorgabe verwenden
				if (isset($_SESSION[dbMultipleChoiceQuestion::field_groups])) $question[dbMultipleChoiceQuestion::field_groups] = $_SESSION[dbMultipleChoiceQuestion::field_groups];
				if (isset($_SESSION[dbMultipleChoiceQuestion::field_name])) $question[dbMultipleChoiceQuestion::field_name] = $_SESSION[dbMultipleChoiceQuestion::field_name];
				if (isset($_SESSION[dbMultipleChoiceQuestion::field_question])) $question[dbMultipleChoiceQuestion::field_question] = $_SESSION[dbMultipleChoiceQuestion::field_question];
				$message = $this->getMessage();
				$message .= mc_msg_question_remembered;
				$this->setMessage($message);
			}
		}
		// $_REQUEST pruefen...
		foreach ($dbMCQuestion->getFields() as $key => $value) {
			if (isset($_REQUEST[$key])) {
				switch($key):
				case dbMultipleChoiceQuestion::field_groups:
					$question[$key] = implode(',', $_REQUEST[$key]);
					break;
				default:
					$question[$key] = $_REQUEST[$key];
				endswitch;
			}
		}
		$items = '';
		$row = new Dwoo_Template_File($this->template_path.'backend.question.edit.row.htt');
		$data = array(
			'label'		=> '',
			'value'		=> ($qid != -1) ? sprintf('ID %05d', $qid) : mc_label_new_id
		);
		$items .= $parser->get($row, $data);

		// Bezeichner
		$data = array(
			'label'		=> mc_label_name,
			'value'		=> sprintf('<input type="text" name="%s" value="%s" />', dbMultipleChoiceQuestion::field_name, $question[dbMultipleChoiceQuestion::field_name])
		);
		$items .= $parser->get($row, $data);

		// Frage
		$data = array(
			'label'		=> mc_label_question,
			'value'		=> sprintf('<input type="test" name="%s" value="%s" />', dbMultipleChoiceQuestion::field_question, $question[dbMultipleChoiceQuestion::field_question])
		);
		$items .= $parser->get($row, $data);

		// Beschreibung
		ob_start();
			show_wysiwyg_editor(dbMultipleChoiceQuestion::field_description,
													dbMultipleChoiceQuestion::field_description,
													stripslashes($question[dbMultipleChoiceQuestion::field_description]),
													'99%',
													'200px');
			$editor = ob_get_contents();
		ob_end_clean();
		$data = array(
			'label'		=> mc_label_description,
			'value'		=> $editor
		);
		$items .= $parser->get($row, $data);

		// Gruppen
		$select = '';
		$question_groups = explode(',', $question[dbMultipleChoiceQuestion::field_groups]);
		foreach ($groups as $group) {
			(in_array($group, $question_groups)) ? $selected = ' selected="selected"' : $selected = '';
			$select .= sprintf('<option value="%s"%s>%s</option>', $group, $selected, $group);
		}
		$data = array(
			'label'		=> mc_label_question_groups,
			'value'		=> sprintf('<select name="%s[]" multiple="multiple">%s</select>', dbMultipleChoiceQuestion::field_groups, $select)
		);
		$items .= $parser->get($row, $data);

		// Modus
		$select = '';
		foreach ($dbMCQuestion->mode_array as $key => $value) {
			($key == $question[dbMultipleChoiceQuestion::field_mode]) ? $selected = ' selected="selected"' : $selected = '';
			$select .= sprintf('<option value="%s"%s>%s</option>', $key, $selected, $value);
		}
		$data = array(
			'label'		=> mc_label_mode,
			'value'		=> sprintf('<select name="%s" size="1">%s</select>', dbMultipleChoiceQuestion::field_mode, $select)
		);
		$items .= $parser->get($row, $data);

		// HTML?
		($question[dbMultipleChoiceQuestion::field_use_html] == 1) ? $checked = ' checked="checked"' : $checked = '';
		$data = array(
			'label'		=> mc_label_use_html,
			'value'		=> sprintf(	'<input type="checkbox" name="%s" value="1"%s /> %s',
														dbMultipleChoiceQuestion::field_use_html,
														$checked,
														mc_hint_use_html)
		);
		$items .= $parser->get($row, $data);

		// Eingabefelder fuer die Fragen abfragen
		$dummy_array = explode(',', $question[dbMultipleChoiceQuestion::field_answers]);
		$dummy = '';
		foreach ($dummy_array as $i) {
			if (!empty($dummy)) $dummy .= ',';
			$dummy .= sprintf("'%s'", $i);
		}
		$SQL = sprintf(	"SELECT * FROM %s WHERE %s IN (%s) AND %s!='%s'",
										$dbMCQuestionItem->getTableName(),
										dbMultipleChoiceQuestionItem::field_id,
										$dummy,
										dbMultipleChoiceQuestionItem::field_status,
										dbMultipleChoiceQuestionItem::status_deleted);
		$answers = array();
		if (!$dbMCQuestionItem->sqlExec($SQL, $answers)) {
			$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCQuestionItem->getError()));
			return false;
		}
		$count_answers = count($answers);
		for ($i=0; $i < $max_items; $i++) {
			if (isset($answers[$i])) {
				// es existiert bereits eine Antwort
				$text = strip_tags($answers[$i][dbMultipleChoiceQuestionItem::field_question]);
				$is_correct = $answers[$i][dbMultipleChoiceQuestionItem::field_is_correct];
				$status = $answers[$i][dbMultipleChoiceQuestionItem::field_status];
				$hidden = sprintf(	'<input type="hidden" name="%s_%d" value="%d" />',
													dbMultipleChoiceQuestionItem::field_id,
													$i,
													$answers[$i][dbMultipleChoiceQuestionItem::field_id]);
			}
			else {
				$text = '';
				$is_correct = 0;
				$status = dbMultipleChoiceQuestionItem::status_active;
				$hidden = sprintf(	'<input type="hidden" name="%s_%d" value="%d" />',
													dbMultipleChoiceQuestionItem::field_id,	$i,	-1);
			}
			if ($question[dbMultipleChoiceQuestion::field_use_html] == 1) {
				// HTML Ausgabe
				ob_start();
					show_wysiwyg_editor(dbMultipleChoiceQuestionItem::field_question.'_'.$i,
															dbMultipleChoiceQuestionItem::field_question.'_'.$i,
															$text,
															'99%',
															'200px');
					$editor = ob_get_contents();
				ob_end_clean();
			}
			else {
				// NUR TEXT
				$editor = sprintf('<textarea name="%s_%d">%s</textarea>',
													dbMultipleChoiceQuestionItem::field_question, $i, strip_tags($text));
			}
			// Verstecktes Feld und Editor
			(isset($answers[$i])) ? $aid = sprintf('(<i>%05d</i>)', $answers[$i][dbMultipleChoiceQuestionItem::field_id]) : $aid = '';
			$data = array(
				'label'		=> sprintf(mc_label_answer_id, $aid),
				'value'		=> $hidden.$editor
			);
			$items .= $parser->get($row, $data);
			// Ist richtig? und Status
			$status = '';
			(isset($answers[$i])) ? $answer_status = $answers[$i][dbMultipleChoiceQuestionItem::field_status] : $answer_status = dbMultipleChoiceQuestionItem::status_active;
			foreach ($dbMCQuestionItem->status_array as $key => $value) {
				($answer_status == $key) ? $selected = ' selected="selected"' : $selected = '';
				$status .= sprintf(	'<option value="%s"%s>%s</option>',
														$key, $selected, $value);
			}
			$status = sprintf('<select class="mc_status" name="%s_%d">%s</select>', dbMultipleChoiceQuestionItem::field_status, $i, $status);
			(isset($answers[$i]) && ($answers[$i][dbMultipleChoiceQuestionItem::field_is_correct] == 1)) ? $checked = ' checked="checked"' : $checked = '';
			$data = array(
				'label'		=> '',
				'value'		=> sprintf(	'<input type="checkbox" name="%s_%d" value="1"%s /> %s - %s %s',
															dbMultipleChoiceQuestionItem::field_is_correct, $i,
															$checked,
															mc_label_answer_is_correct,
															mc_label_status,
															$status)
			);
			$items .= $parser->get($row, $data);
		}

		// Hinweis fuer die Antworten
		$prompt_array = array(dbMultipleChoiceQuestion::field_prompt_correct, dbMultipleChoiceQuestion::field_prompt_false, dbMultipleChoiceQuestion::field_prompt_partial);
		foreach ($prompt_array as $prompt) {
			// HTML Ausgabe
			$editor = '';
			ob_start();
				show_wysiwyg_editor($prompt,
														$prompt,
														$question[$prompt],
														'99%',
														'200px');
				$editor = ob_get_contents();
			ob_end_clean();
			switch ($prompt):
			case dbMultipleChoiceQuestion::field_prompt_correct:
				$label = mc_label_prompt_correct;
				$hint_grp = dbMultipleChoiceQuestionHint::group_correct; 
				$hint_select = self::request_hint_correct_select;
				$hint_name = self::request_hint_correct_name;
				$hint_save = self::request_hint_correct_save;
				$hint_delete = self::request_hint_correct_delete;
				break;
			case dbMultipleChoiceQuestion::field_prompt_false:
				$label = mc_label_prompt_false; 
				$hint_grp = dbMultipleChoiceQuestionHint::group_false;
				$hint_select = self::request_hint_false_select;
				$hint_name = self::request_hint_false_name;
				$hint_save = self::request_hint_false_save;
				$hint_delete = self::request_hint_false_delete;
				break;
			case dbMultipleChoiceQuestion::field_prompt_partial:
				$label = mc_label_prompt_partial; 
				$hint_grp = dbMultipleChoiceQuestionHint::group_partial;
				$hint_select = self::request_hint_partial_select;
				$hint_name = self::request_hint_partial_name;
				$hint_save = self::request_hint_partial_save;
				$hint_delete = self::request_hint_partial_delete;
				break;
			endswitch;
			// Hinweise auslesen
			$SQL = sprintf( "SELECT %s, %s FROM %s WHERE %s='%s' AND %s='%s'",
											dbMultipleChoiceQuestionHint::field_name,
											dbMultipleChoiceQuestionHint::field_id,
											$dbMCQuestionHint->getTableName(),
											dbMultipleChoiceQuestionHint::field_group,
											$hint_grp,
											dbMultipleChoiceQuestionHint::field_status,
											dbMultipleChoiceQuestionHint::status_active);
			$hints = array();
			if (!$dbMCQuestionHint->sqlExec($SQL, $hints)) {
				$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCQuestionHint->getError()));
				return false;
			}
			$select = sprintf('<option value="%s">%s</option>', -1, mc_text_create_new_hint);
			foreach ($hints as $hint) {
				$selected = (isset($_REQUEST[$hint_select]) && ($_REQUEST[$hint_select] == $hint[dbMultipleChoiceQuestionHint::field_id])) ? ' selected="selected"' : '';
				$select .= sprintf('<option value="%s"%s>%s</option>', $hint[dbMultipleChoiceQuestionHint::field_id], $selected, $hint[dbMultipleChoiceQuestionHint::field_name]);
			}
			$select = sprintf('<div class="%s">%s: <select name="%s">%s</select> <input type="checkbox" name="%s" /> %s</div>', 
												$hint_select,
												mc_label_hint_select,
												$hint_select, 
												$select,
												$hint_delete,
												mc_label_hint_delete);
			$name = sprintf('<div class="%s"><input type="checkbox" name="%s" /> %s <input type="text" name="%s" /></div>',
											$hint_save,
											$hint_save,
											mc_label_hint_save_as,
											$hint_name);
			
			$data = array(
				'label'		=> $label,
				'value'		=> $select.$editor.$name
			);
			$items .= $parser->get($row, $data);
		}

		// Status der Frage
		$select = '';
		$status_array = $dbMCQuestion->status_array;
		unset($status_array[dbMultipleChoiceQuestion::status_locked_temp]);
		foreach ($status_array as $key => $value) {
			($key == $question[dbMultipleChoiceQuestion::field_status]) ? $selected = ' selected="selected"' : $selected = '';
			$select .= sprintf('<option value="%s"%s>%s</option>', $key, $selected, $value);
		}
		$data = array(
			'label'		=> mc_label_status,
			'value'		=> sprintf('<select name="%s">%s</select>', dbMultipleChoiceQuestion::field_status, $select)
		);
		$items .= $parser->get($row, $data);

		// Mitteilungen anzeigen
		if ($this->isMessage()) {
			$intro = sprintf('<div class="message">%s</div>', $this->getMessage());
		}
		else {
			$intro = sprintf('<div class="intro">%s</div>', mc_intro_question_edit);
		}

		$data = array(
			'header'						=> mc_header_question_edit,
			'intro'							=> $intro,
			'form_name'					=> 'question_edit',
			'form_action'				=> $this->page_link,
			'action_name'				=> self::request_action,
			'action_value'			=> self::action_question,
			'tab_action_name'		=> self::request_question_tab,
			'tab_action_value'	=> self::action_question_tab_edit,
			'sub_action_name'		=> self::request_sub_action,
			'sub_action_value'	=> self::action_question_edit_check,
			'qid_name'					=> dbMultipleChoiceQuestion::field_id,
			'qid_value'					=> $qid,
			'items'							=> $items,
			'btn_ok'						=> mc_btn_ok,
			'btn_abort'					=> mc_btn_abort,
			'abort_location'		=> $this->page_link
		);
		return $parser->get($this->template_path.'backend.question.edit.htt', $data);
	} // dlgQuestionEdit()

	/**
	 * Frage prüfen und sichern
	 *
	 * @return STR dlgQuestionEdit()
	 */
	public function checkQuestion() {
		global $dbCfg;
		global $dbMCQuestion;
		global $dbMCQuestionItem;
		global $dbMCQuestionHint;
		global $tools;

		$message = '';
		$question = $dbMCQuestion->getFields();
		$minimum_checked = true;
		foreach ($dbMCQuestion->getFields() as $key => $value) {
			switch ($key):
			case dbMultipleChoiceQuestion::field_name:
				if (empty($_REQUEST[$key])) {
					$message .= mc_msg_question_name_missing;
					$minimum_checked = false;
				}
				else {
					$question[$key] = trim($_REQUEST[$key]);
				}
				break;
			case dbMultipleChoiceQuestion::field_question:
				if (empty($_REQUEST[$key])) {
					$message .= mc_msg_question_question_missing;
					$minimum_checked = false;
				}
				else {
					$question[$key] = stripslashes(str_replace('&quot;', '"', trim($_REQUEST[$key])));
				}
				break;
			case dbMultipleChoiceQuestion::field_groups:
				if (!isset($_REQUEST[$key])) {
					$message .= mc_msg_question_no_group;
					$minimum_checked = false;
				}
				else {
					$question[$key] = implode(',', $_REQUEST[$key]);
				}
				break;
			case dbMultipleChoiceQuestion::field_use_html:
				(isset($_REQUEST[$key])) ? $question[$key] = 1 : $question[$key] = 0;
				break;
			case dbMultipleChoiceQuestion::field_description:
				$question[$key] = stripslashes(trim($_REQUEST[$key]));
				break;
			case dbMultipleChoiceQuestion::field_id:
			case dbMultipleChoiceQuestion::field_mode:
			case dbMultipleChoiceQuestion::field_status:
			case dbMultipleChoiceQuestion::field_prompt_correct:
			case dbMultipleChoiceQuestion::field_prompt_false:
			case dbMultipleChoiceQuestion::field_prompt_partial:
				// optionale Felder bzw. ohne besondere Prüfung
				if (!empty($_REQUEST[$key])) $question[$key] = trim($_REQUEST[$key]);
			endswitch;
		}

		$max_items = $dbCfg->getValue(dbMultipleChoiceCfg::cfgMaxItems);
		$min_items = $dbCfg->getValue(dbMultipleChoiceCfg::cfgMinItems);
		$answers = array();
		$solutions = array();

		for ($i=0; $i < $max_items; $i++) {
			$delete = false;
			if (!empty($_REQUEST[dbMultipleChoiceQuestionItem::field_question.'_'.$i])) {
				// Antwort formuliert
				if (($_REQUEST[dbMultipleChoiceQuestionItem::field_id.'_'.$i] != -1) &&
						($_REQUEST[dbMultipleChoiceQuestionItem::field_status.'_'.$i] == dbMultipleChoiceQuestionItem::status_deleted)) {
					// Datensatz loeschen
					$delete = true;
				}
				elseif ($_REQUEST[dbMultipleChoiceQuestionItem::field_status.'_'.$i] == dbMultipleChoiceQuestionItem::status_active) {
					// Datensatz hinzufuegen oder aktualisieren
					$data = array();
					if ($question[dbMultipleChoiceQuestion::field_use_html] == 1) {
						$data[dbMultipleChoiceQuestionItem::field_question] = $_REQUEST[dbMultipleChoiceQuestionItem::field_question.'_'.$i];
					}
					else {
						$data[dbMultipleChoiceQuestionItem::field_question] = trim(strip_tags($_REQUEST[dbMultipleChoiceQuestionItem::field_question.'_'.$i]));
					}
//					$data[dbMultipleChoiceQuestionItem::field_question] = $_REQUEST[dbMultipleChoiceQuestionItem::field_question.'_'.$i];
					$data[dbMultipleChoiceQuestionItem::field_is_correct] = (isset($_REQUEST[dbMultipleChoiceQuestionItem::field_is_correct.'_'.$i])) ? 1 : 0;
					$data[dbMultipleChoiceQuestionItem::field_status] = dbMultipleChoiceQuestionItem::status_active;
					$data[dbMultipleChoiceQuestionItem::field_update_by] = $tools->getDisplayName();
					$data[dbMultipleChoiceQuestionItem::field_update_when] = date('Y-m-d H:i:s');
					if ($_REQUEST[dbMultipleChoiceQuestionItem::field_id.'_'.$i] != -1) {
						// UPDATE
						$where = array();
						$where[dbMultipleChoiceQuestionItem::field_id] = $_REQUEST[dbMultipleChoiceQuestionItem::field_id.'_'.$i];
						if (!$dbMCQuestionItem->sqlUpdateRecord($data, $where)) {
							$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCQuestionItem->getError()));
							return false;
						}
						$message .= sprintf(mc_msg_question_item_updated, $_REQUEST[dbMultipleChoiceQuestionItem::field_id.'_'.$i]);
						$answers[] = $_REQUEST[dbMultipleChoiceQuestionItem::field_id.'_'.$i];
						if ($data[dbMultipleChoiceQuestionItem::field_is_correct] == 1) $solutions[] = $_REQUEST[dbMultipleChoiceQuestionItem::field_id.'_'.$i];
					}
					else {
						// INSERT
						$id = -1;
						if (!$dbMCQuestionItem->sqlInsertRecord($data, $id)) {
							$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCQuestionItem->getError()));
							return false;
						}
						$message .= sprintf(mc_msg_question_item_inserted, $id);
						$answers[] = $id;
						if ($data[dbMultipleChoiceQuestionItem::field_is_correct] == 1) $solutions[] = $id;
					}
					// Richtige Antworten zaehlen

				}
			}
			elseif ($_REQUEST[dbMultipleChoiceQuestionItem::field_id.'_'.$i] != -1) {
				// Datensatz loeschen
				$delete = true;
			}
			if ($delete) {
				// Datensatz loeschen
				$where = array();
				$where[dbMultipleChoiceQuestionItem::field_id] = $_REQUEST[dbMultipleChoiceQuestionItem::field_id.'_'.$i];
				$data = array();
				$data[dbMultipleChoiceQuestionItem::field_status] = dbMultipleChoiceQuestionItem::status_deleted;
				$data[dbMultipleChoiceQuestionItem::field_update_by] = $tools->getDisplayName();
				$data[dbMultipleChoiceQuestionItem::field_update_when] = date('Y-m-d H:i:s');
				if (!$dbMCQuestionItem->sqlUpdateRecord($data, $where)) {
					$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCQuestionItem->getError()));
					return false;
				}
				$message .= sprintf(mc_msg_question_item_deleted, $_REQUEST[dbMultipleChoiceQuestionItem::field_id.'_'.$i]);
			}
			// $_REQUEST's zurücksetzen
			unset($_REQUEST[dbMultipleChoiceQuestionItem::field_id.'_'.$i]);
			unset($_REQUEST[dbMultipleChoiceQuestionItem::field_question.'_'.$i]);
			unset($_REQUEST[dbMultipleChoiceQuestionItem::field_is_correct.'_'.$i]);
			unset($_REQUEST[dbMultipleChoiceQuestionItem::field_status.'_'.$i]);
		} // for

		// erweiterte Pruefungen
		$is_checked = true;
		// Mindestanzahl der Antworten unterschritten?
		if (count($answers) < $min_items) {
			$message .= sprintf(mc_msg_question_items_min, count($answers), $min_items);
			$is_checked = false;
		}
		// Höchstanzahl der Antworten überschritten?
		if (count($answers) > $max_items) {
			$message .= sprintf(mc_msg_question_item_max, count($answers), $max_items);
			$is_checked = false;
		}
		// Anzahl der richtigen Antworten muss mit dem Modus übereinstimmen
		switch($question[dbMultipleChoiceQuestion::field_mode]):
			case dbMultipleChoiceQuestion::mode_multiple:
			case dbMultipleChoiceQuestion::mode_partial:
				// Multiple Choice: mindestens eine Antwort...
				if (count($solutions) < 1) {
					$message .= sprintf(mc_msg_question_item_count_multiple, $dbMCQuestion->mode_array[$question[dbMultipleChoiceQuestion::field_mode]]);
					$is_checked = false;
				}
				break;
			case dbMultipleChoiceQuestion::mode_single:
				if (count($solutions) != 1) {
					$message .= sprintf(mc_msg_question_item_count_single, $dbMCQuestion->mode_array[$question[dbMultipleChoiceQuestion::field_mode]], count($solutions));
					$is_checked = false;
				}
				break;
			case dbMultipleChoiceQuestion::mode_none:
				if (count($solutions) != 0) {
					$message .= sprintf(mc_msg_question_item_count_none, $dbMCQuestion->mode_array[$question[dbMultipleChoiceQuestion::field_mode]]);
					$is_checked = false;
				}
				break;
		endswitch;

		// Handling fuer HINTs
		$hints_selected = array(self::request_hint_correct_select, self::request_hint_false_select, self::request_hint_partial_select);
		foreach ($hints_selected as $hint_select) {
			switch ($hint_select):
			case self::request_hint_correct_select:
				$hint_delete = (isset($_REQUEST[self::request_hint_correct_delete])) ? true : false;
				$hint_save = (isset($_REQUEST[self::request_hint_correct_save])) ? true : false;
				$hint_name = (isset($_REQUEST[self::request_hint_correct_name])) ? $_REQUEST[self::request_hint_correct_name] : '';
				$hint_group = dbMultipleChoiceQuestionHint::group_correct;
				$prompt = dbMultipleChoiceQuestion::field_prompt_correct;
				break;
			case self::request_hint_false_select:
				$hint_delete = (isset($_REQUEST[self::request_hint_false_delete])) ? true : false;
				$hint_save = (isset($_REQUEST[self::request_hint_false_save])) ? true : false;
				$hint_name = (isset($_REQUEST[self::request_hint_false_name])) ? $_REQUEST[self::request_hint_false_name] : '';
				$hint_group = dbMultipleChoiceQuestionHint::group_false;
				$prompt = dbMultipleChoiceQuestion::field_prompt_false;
				break;
			case self::request_hint_partial_select:
				$hint_delete = (isset($_REQUEST[self::request_hint_partial_delete])) ? true : false;
				$hint_save = (isset($_REQUEST[self::request_hint_partial_save])) ? true : false;
				$hint_name = (isset($_REQUEST[self::request_hint_partial_name])) ? $_REQUEST[self::request_hint_partial_name] : '';
				$hint_group = dbMultipleChoiceQuestionHint::group_partial;
				$prompt = dbMultipleChoiceQuestion::field_prompt_partial;
				break;
			endswitch;
			if (isset($_REQUEST[$hint_select]) && ($_REQUEST[$hint_select] != -1)) { 
				// Hint verwenden
				$hint_id = $_REQUEST[$hint_select];
				if ($hint_delete) {
					// Eintrag soll geloescht werden
					$data = array(dbMultipleChoiceQuestionHint::field_status => dbMultipleChoiceQuestionHint::status_deleted);
					$where = array(dbMultipleChoiceQuestionHint::field_id => $hint_id);
					if (!$dbMCQuestionHint->sqlUpdateRecord($data, $where)) {
						$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCQuestionHint->getError()));
						return false;
					}
					$message .= sprintf(mc_msg_hint_deleted, $hint_id);
				}
				else { 
					// Hint soll verwendet werden
					$where = array(dbMultipleChoiceQuestionHint::field_id => $hint_id);
					$hint_data = array();
					if (!$dbMCQuestionHint->sqlSelectRecord($where, $hint_data)) {
						$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCQuestionHint->getError()));
						return false;
					}
					if (count($hint_data) < 1) {
						$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, sprintf(mc_error_id_missing, $hint_id)));
						return false;
					}
					if ($is_checked || ($question[dbMultipleChoiceQuestion::field_status] == dbMultipleChoiceQuestion::status_active)) {
						$question[$prompt] = $hint_data[0][dbMultipleChoiceQuestionHint::field_hint];
						unset($_REQUEST[$prompt]);
						unset($_REQUEST[$hint_select]);
					}
					else { 
						$message .= sprintf(mc_msg_hint_usage_locked, $hint_data[0][dbMultipleChoiceQuestionHint::field_name]);
					}
				}
			}
			elseif ($hint_save && !empty($hint_name)) {
				// Prompt soll als Hinweis gesichert werden
				$data = array(
					dbMultipleChoiceQuestionHint::field_group 	=> $hint_group,
					dbMultipleChoiceQuestionHint::field_hint 		=> $question[$prompt],
					dbMultipleChoiceQuestionHint::field_name 		=> $hint_name,
					dbMultipleChoiceQuestionHint::field_status 	=> dbMultipleChoiceQuestionHint::status_active
				);
				$hint_id = -1;
				if (!$dbMCQuestionHint->sqlInsertRecord($data, $hint_id)) {
					$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCQuestionHint->getError()));
					return false;
				}
				$message .= ($is_checked) ? sprintf(mc_msg_hint_inserted, $hint_name) : sprintf(mc_msg_hint_inserted_locked, $hint_name);
			}
		} 
		// Datensatz uebernehmen
		$question[dbMultipleChoiceQuestion::field_answers] = implode(',', $answers);
		$question[dbMultipleChoiceQuestion::field_solutions] = implode(',', $solutions);
		if (!$is_checked) {
			// Fehler bei der Prüfung, Datensatz vorläufig sperren
			$question[dbMultipleChoiceQuestion::field_status] = dbMultipleChoiceQuestion::status_locked_temp;
			$message .= mc_msg_question_locked_temp;
		}
		$question[dbMultipleChoiceQuestion::field_update_by] = $tools->getDisplayName();
		$question[dbMultipleChoiceQuestion::field_update_when] = date('Y-m-d H:i:s');

		if ($_REQUEST[dbMultipleChoiceQuestion::field_id] == -1) {
			// neuer Datensatz
			$id = -1;
			if (!$dbMCQuestion->sqlInsertRecord($question, $id)) {
				$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCQuestion->getError()));
				return false;
			}
			$_REQUEST[dbMultipleChoiceQuestion::field_id] = $id;
			$message .= sprintf(mc_msg_question_inserted, $id);
		}
		else {
			// Datensatz aktualisieren
			$where = array();
			$where[dbMultipleChoiceQuestion::field_id] = $_REQUEST[dbMultipleChoiceQuestion::field_id];
			if (!$dbMCQuestion->sqlUpdateRecord($question, $where)) {
				$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCQuestion->getError()));
				return false;
			}
			$message .= sprintf(mc_msg_question_updated, $_REQUEST[dbMultipleChoiceQuestion::field_id]);
		}

		// Sollen Fragen gemerkt werden?
		if ($dbCfg->getValue(dbMultipleChoiceCfg::cfgRememberQuestion)) {
			$_SESSION[dbMultipleChoiceQuestion::field_groups] = $question[dbMultipleChoiceQuestion::field_groups];
			$_SESSION[dbMultipleChoiceQuestion::field_name] = $question[dbMultipleChoiceQuestion::field_name];
			$_SESSION[dbMultipleChoiceQuestion::field_question] = $question[dbMultipleChoiceQuestion::field_question];
		}
		
		$this->setMessage($message);

		// $_REQUEST zuruecksetzen?
		return $this->dlgQuestionEdit();
	} // checkQuestion()

	/**
	 * Übergeordneter Dialog für die Fragebögen, verwaltet die Aktionen und zeigt die TABs der untergeordneten Dialoge an
	 *
	 * @return STR dlgQuestionaire()
	 */
  public function dlgQuestionaire() {
  	$questionaire_tab = '';
  	(isset($_REQUEST[self::request_questionaire_tab])) ? $action = $_REQUEST[self::request_questionaire_tab] : $action = self::action_questionaire_tab_list;
  	foreach ($this->tab_questionaire_array as $key => $value) {
  		($key== $action) ? $selected = ' class="selected"' : $selected = '';
  		$questionaire_tab .= sprintf(	'<li%s><a href="%s">%s</a></li>',
	  																$selected,
	  																sprintf('%s&%s=%s&%s=%s', $this->page_link, self::request_action, self::action_questionaire, self::request_questionaire_tab, $key),
	  																$value
	  															);
  	}
  	$questionaire_tab = sprintf('<ul class="nav_tab">%s</ul>', $questionaire_tab);

  	switch ($action):
  	case self::action_questionaire_tab_edit:
  		(isset($_REQUEST[self::request_sub_action])) ? $sub_action = $_REQUEST[self::request_sub_action] : $sub_action = self::action_default;
  		switch ($sub_action):
  			case self::action_questionaire_edit_check:
  				$result = $this->checkQuestionaire();
  				break;
  			case self::action_questionaire_edit:
  			default:
  				$result = $this->dlgQuestionaireEdit();
  				break;
  		endswitch;
  		break;
  	case self::action_questionaire_tab_list:
  	default:
  		$result = $this->dlgQuestionaireList();
  	endswitch;
  	return sprintf('<div class="questionaire_container">%s%s</div>', $questionaire_tab, $result);
  } // dlgQuestionaire()

  /**
   * Gibt eine Liste mit den vorhandenen Fragebögen aus
   *
   * @return STR dlgQuestionaireList()
   */
  public function dlgQuestionaireList() {
  	global $dbMCQuestionaire;
  	global $parser;
  	global $dbCfg;
  	global $dbMCTableSort;
  	
  	// Gruppen auslesen
  	$groups = $dbCfg->getValue(dbMultipleChoiceCfg::cfgGroups);
  	asort($groups);
  	$sorter_active = 1;
  	$sorter_table = str_replace(TABLE_PREFIX, '', $dbMCQuestionaire->getTableName());
  	if (isset($_REQUEST[self::request_questionaire_filter]) && ($_REQUEST[self::request_questionaire_filter] != -1)) {
  		// nach Gruppe filtern - keine Sortierung aktivieren!
  		$sorter_active = 0;
  		$group = $groups[$_REQUEST[self::request_questionaire_filter]];
  		$sql = sprintf( "SELECT * FROM %s WHERE %s!='%s' AND (%s LIKE '%s' OR %s LIKE '%s,%%' OR %s LIKE '%%,%s' OR %s LIKE '%%,%s,%%')",
  										$dbMCQuestionaire->getTableName(),
  										dbMultipleChoiceQuestionaire::field_status,
  										dbMultipleChoiceQuestionaire::status_deleted,
  										dbMultipleChoiceQuestionaire::field_groups,
  										$group,
  										dbMultipleChoiceQuestionaire::field_groups,
  										$group,
  										dbMultipleChoiceQuestionaire::field_groups,
  										$group,
  										dbMultipleChoiceQuestionaire::field_groups,
  										$group  										
  									);
	  	$questionaires = array();
	  	if (!$dbMCQuestionaire->sqlExec($sql, $questionaires)) {
	  		$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCQuestionaire->getError()));
	  		return false;
	  	}
  	}
  	else {
  		// zusaetzliches Handling um die Sortierung der Tabelle zu ermoeglichen
  		$sql = sprintf( "SELECT %s FROM %s WHERE %s!='%s'",
  										dbMultipleChoiceQuestionaire::field_id,
	  									$dbMCQuestionaire->getTableName(),
	  									dbMultipleChoiceQuestionaire::field_status,
	  									dbMultipleChoiceQuestionaire::status_deleted);
	  	$quests = array();
	  	if (!$dbMCQuestionaire->sqlExec($sql, $quests)) {
	  		$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCQuestionaire->getError()));
	  		return false;
	  	}
	  	$must_ids = array();
	  	foreach ($quests as $id) {
	  		$must_ids[] = $id[dbMultipleChoiceQuestionaire::field_id];
	  	}
	  	$where = array(dbMultipleChoiceTableSort::field_table => $sorter_table);
	  	$sorter = array();
			if (!$dbMCTableSort->sqlSelectRecord($where, $sorter)) {
				$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCTableSort->getError()));
				return false;
			}
			if (count($sorter) < 1) {
				// Eintrag existiert noch nicht!
				$sorter_set = implode(',', $must_ids);
				$data = array(
					dbMultipleChoiceTableSort::field_table => $sorter_table,
					dbMultipleChoiceTableSort::field_value => -1,
					dbMultipleChoiceTableSort::field_order => $sorter_set
				);
				if (!$dbMCTableSort->sqlInsertRecord($data)) {
					$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCTableSort->getError()));
					return false;
				} 
			}
			else {
				// Eintrag auf Unterschiede pruefen und Datensatz wieder aktualisieren
				$sorter_ids = explode(',', $sorter[0][dbMultipleChoiceTableSort::field_order]);
				$old = $sorter_ids;
				$check = array_diff($must_ids, $sorter_ids);
				foreach ($check as $id) $sorter_ids[] = $id;
				$check = array_diff($sorter_ids, $must_ids);
				foreach ($check as $id) unset($sorter_ids[$id]);
				$sorter_set = implode(',', $sorter_ids);
				if ($old !== $sorter_ids) {
					$where = array(dbMultipleChoiceTableSort::field_id => $sorter[0][dbMultipleChoiceTableSort::field_id]);
					$data = array(dbMultipleChoiceTableSort::field_order => $sorter_set);
					if (!$dbMCTableSort->sqlUpdateRecord($data, $where)) {
						$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCTableSort->getError()));
						return false;
					}
				}
			}
			$sql = sprintf(	"SELECT * FROM %s WHERE %s!='%s' ORDER BY FIND_IN_SET(%s, '%s')",
											$dbMCQuestionaire->getTableName(),
											dbMultipleChoiceQuestionaire::field_status,
											dbMultipleChoiceQuestionaire::status_deleted,
											dbMultipleChoiceQuestionaire::field_id,
											$sorter_set);
			$questionaires = array();
			if (!$dbMCQuestionaire->sqlExec($sql, $questionaires)) {
				$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCQuestionaire->getError()));
				return false;
			}	  	
  	}
  	
  	// Filter
  	$filter = sprintf('<option value="%s">%s</option>', -1, mc_text_select_filter);
  	foreach ($groups as $key => $value) {
  		$selected = (isset($_REQUEST[self::request_questionaire_filter]) && ($_REQUEST[self::request_questionaire_filter] == $key)) ? ' selected="selected"' : '';
  		$filter .= sprintf('<option value="%s"%s>%s</option>', $key, $selected, $value);
  	}
  	
  	$filter = sprintf('<div class="%s">%s: <select name="%s" onchange="javascript: window.location = \'%s\'+this.value; return false;">%s</select></div>', 
  										self::request_questionaire_filter,
  										mc_label_filter,
  										self::request_questionaire_filter, 
  										sprintf('%s&%s=%s&%s=%s&%s=',
  														$this->page_link,
  														self::request_action,
  														self::action_questionaire,
  														self::request_questionaire_tab,
  														self::action_question_tab_list,
  														self::request_questionaire_filter),
  										$filter);
  	
  	$items = '';
		if (count($questionaires) < 1) {
			// es sind noch keine Fragebögen definiert
			$this->setMessage(sprintf(mc_msg_questionaire_list_empty, $this->tab_questionaire_array[self::action_questionaire_tab_edit]));
		}
		else {
			$data = array(
				'id'						=> mc_header_id,
				'name'					=> mc_header_name,
				'title'					=> mc_header_title,
				'questions_sum'	=> mc_header_questions_sum,
				'groups'				=> mc_header_groups,
				'status'				=> mc_header_status
			);
			$header = $parser->get($this->template_path.'backend.questionaire.list.header.htt', $data);
		}
		$row = new Dwoo_Template_File($this->template_path.'backend.questionaire.list.row.htt');
		$flipflop = true;
		foreach ($questionaires as $questionaire) {
			$flipflop ? $flipper = 'flip' : $flipper = 'flop';
  		$flipflop ? $flipflop = false : $flipflop = true;
  		($questionaire[dbMultipleChoiceQuestionaire::field_status] == dbMultipleChoiceQuestionaire::status_locked) ?
				$class = 'mc_item_locked' : $class = 'mc_item_normal';
			$sum_arr = explode(',', $questionaire[dbMultipleChoiceQuestionaire::field_questions]);
			(!empty($sum_arr[0])) ? $count = count($sum_arr) : $count = 0;
			$data = array(
				'row_id'			=> 'rowID_'.$questionaire[dbMultipleChoiceQuestionaire::field_id],
				'flipflop'		=> $flipper,
				'id'					=> sprintf('<span class="%s">%s</span>',
																	$class,
																	sprintf('<a href="%s">%s</a>',
																					sprintf('%s&%s=%s&%s=%s&%s=%s&%s=%s',
																									$this->page_link,
																									self::request_action,
																									self::action_questionaire,
																									self::request_questionaire_tab,
																									self::action_questionaire_tab_edit,
																									self::request_sub_action,
																									self::action_questionaire_edit,
																									dbMultipleChoiceQuestionaire::field_id,
																									$questionaire[dbMultipleChoiceQuestionaire::field_id]),
																					sprintf('%05d', $questionaire[dbMultipleChoiceQuestionaire::field_id]))),
				'name'				=> sprintf('<span class="%s">%s</span>', $class, $questionaire[dbMultipleChoiceQuestionaire::field_name]),
				'title'				=> sprintf('<span class="%s" title="%s">%s</span>', $class, strip_tags($questionaire[dbMultipleChoiceQuestionaire::field_description]), $questionaire[dbMultipleChoiceQuestionaire::field_title]),
				'questions_sum'	=> sprintf('<span class="%s">%s</span>', $class, sprintf('%5d', $count)),
				'groups'			=> sprintf('<span class="%s">%s</span>', $class, str_replace(',', ', ', $questionaire[dbMultipleChoiceQuestionaire::field_groups])),
				'status'			=> sprintf('<span class="%s">%s</span>', $class, $dbMCQuestionaire->status_array[$questionaire[dbMultipleChoiceQuestionaire::field_status]])
			);
			$items .= $parser->get($row, $data);
		} // foreach
		// Mitteilungen anzeigen
		if ($this->isMessage()) {
			$intro = sprintf('<div class="message">%s</div>', $this->getMessage());
		}
		else {
			$intro = sprintf('<div class="intro">%s</div>', sprintf(mc_intro_questionaire_list, $this->tab_questionaire_array[self::action_questionaire_tab_edit]));
		}

		$data = array(
			'header'				=> mc_header_questionaire_list,
			'intro'					=> $intro,
			'head_row'			=> $header,
			'items'					=> $items,
			'filter'				=> $filter,
			'sorter_table'	=> $sorter_table,
			'sorter_value'	=> -1,
			'sorter_active'	=> $sorter_active
		);
		return $parser->get($this->template_path.'backend.questionaire.list.htt', $data);
  } // dlgQuestionaireList()

  /**
   * Dialog zum Bearbeiten von Fragebögen
   *
   * @return STR dlgQuestionaireEdit()
   */
  public function dlgQuestionaireEdit() {
  	global $parser;
  	global $dbMCQuestionaire;
  	global $dbMCQuestion;
  	global $dbCfg;
  	global $dbMCTableSort;

  	(isset($_REQUEST[dbMultipleChoiceQuestionaire::field_id])) ? $qid = $_REQUEST[dbMultipleChoiceQuestionaire::field_id] : $qid = -1;

		// Gruppen auslesen
		$groups = $dbCfg->getValue(dbMultipleChoiceCfg::cfgGroups);

		if ($qid != -1) {
			// bestehender Datensatz
			$where = array();
			$where[dbMultipleChoiceQuestionaire::field_id] = $qid;
			$questionaire = array();
			if (!$dbMCQuestionaire->sqlSelectRecord($where, $questionaire)) {
				$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCQuestionaire->getError()));
				return false;
			}
			if (count($questionaire) < 1) {
				// Datensatz nicht gefunden
				$this->setError(sprintf(mc_error_id_missing, $qid));
				return false;
			}
			$questionaire = $questionaire[0];
		}
		else {
			// neuer Datensatz
			$questionaire = $dbMCQuestionaire->getFields();
			// Voreinstellungen
			$questionaire[dbMultipleChoiceQuestionaire::field_id] = $qid;
			//$questionaire[dbMultipleChoiceQuestionaire::field_groups] = $groups[0]; // erste Gruppe = DEFAULT
			$questionaire[dbMultipleChoiceQuestionaire::field_mode] = dbMultipleChoiceQuestionaire::mode_mixed;
			$questionaire[dbMultipleChoiceQuestionaire::field_shuffle_questions] = 1;
			$questionaire[dbMultipleChoiceQuestionaire::field_shuffle_answers] = 1;
			$questionaire[dbMultipleChoiceQuestionaire::field_status] = dbMultipleChoiceQuestionaire::status_active;
		}
		// $_REQUEST pruefen...
		foreach ($dbMCQuestionaire->getFields() as $key => $value) {
			if (isset($_REQUEST[$key])) {
				switch($key):
				case dbMultipleChoiceQuestionaire::field_groups:
				case dbMultipleChoiceQuestionaire::field_questions:
					$questionaire[$key] = implode(',', $_REQUEST[$key]);
					break;
				default:
					$questionaire[$key] = $_REQUEST[$key];
				endswitch;
			}
		}

  	$items = '';
  	$row = new Dwoo_Template_File($this->template_path.'backend.question.edit.row.htt');

  	// ID
		$data = array(
			'label'		=> '',
			'value'		=> ($qid != -1) ? sprintf('ID %05d', $qid) : mc_label_new_id
		);
		$items .= $parser->get($row, $data);

		// Bezeichner
		$data = array(
			'label'		=> mc_label_name,
			'value'		=> sprintf('<input type="text" name="%s" value="%s" />', dbMultipleChoiceQuestionaire::field_name, $questionaire[dbMultipleChoiceQuestionaire::field_name])
		);
		$items .= $parser->get($row, $data);

		// Überschrift
		$data = array(
			'label'		=> mc_label_title,
			'value'		=> sprintf('<input type="text" name="%s" value="%s" />', dbMultipleChoiceQuestionaire::field_title, $questionaire[dbMultipleChoiceQuestionaire::field_title])
		);
		$items .= $parser->get($row, $data);

		// Einleitung/Beschreibung
		ob_start();
			show_wysiwyg_editor(dbMultipleChoiceQuestionaire::field_description,
													dbMultipleChoiceQuestionaire::field_description,
													$questionaire[dbMultipleChoiceQuestionaire::field_description],
													'99%',
													'200px');
			$editor = ob_get_contents();
		ob_end_clean();
		$data = array(
			'label'		=> mc_label_description,
			'value'		=> $editor
		);
		$items .= $parser->get($row, $data);

		// Gruppen
		$select = '';
		$question_groups = explode(',', $questionaire[dbMultipleChoiceQuestionaire::field_groups]);
		foreach ($groups as $group) {
			(in_array($group, $question_groups)) ? $selected = ' selected="selected"' : $selected = '';
			$select .= sprintf('<option value="%s"%s>%s</option>', $group, $selected, $group);
		}
		$data = array(
			'label'		=> mc_label_question_groups,
			'value'		=> sprintf('<select name="%s[]" multiple="multiple">%s</select>', dbMultipleChoiceQuestionaire::field_groups, $select)
		);
		$items .= $parser->get($row, $data);

		// Modus
		$select = '';
		foreach ($dbMCQuestionaire->mode_array as $key => $value) {
			($key == $questionaire[dbMultipleChoiceQuestionaire::field_mode]) ? $selected = ' selected="selected"' : $selected = '';
			$select .= sprintf('<option value="%s"%s>%s</option>', $key, $selected, $value);
		}
		$data = array(
			'label'		=> mc_label_behaviour,
			'value'		=> sprintf('<select name="%s" size="1">%s</select>', dbMultipleChoiceQuestionaire::field_mode, $select)
		);
		$items .= $parser->get($row, $data);

		// Shuffle Questions?
		($questionaire[dbMultipleChoiceQuestionaire::field_shuffle_questions] == 1) ? $checked = ' checked="checked"' : $checked = '';
		$data = array(
			'label'		=> '',
			'value'		=> sprintf(	'<input type="checkbox" name="%s" value="1"%s /> %s',
														dbMultipleChoiceQuestionaire::field_shuffle_questions,
														$checked,
														mc_hint_shuffle_questions)
		);
		$items .= $parser->get($row, $data);

		// Shuffle Answers?
		($questionaire[dbMultipleChoiceQuestionaire::field_shuffle_answers] == 1) ? $checked = ' checked="checked"' : $checked = '';
		$data = array(
			'label'		=> '',
			'value'		=> sprintf(	'<input type="checkbox" name="%s" value="1"%s /> %s',
														dbMultipleChoiceQuestionaire::field_shuffle_answers,
														$checked,
														mc_hint_shuffle_answers)
		);
		$items .= $parser->get($row, $data);

		// Status des Fragebogens
		$select = '';
		$status_array = $dbMCQuestionaire->status_array;
		foreach ($status_array as $key => $value) {
			($key == $questionaire[dbMultipleChoiceQuestionaire::field_status]) ? $selected = ' selected="selected"' : $selected = '';
			$select .= sprintf('<option value="%s"%s>%s</option>', $key, $selected, $value);
		}
		$data = array(
			'label'		=> mc_label_status,
			'value'		=> sprintf('<select name="%s">%s</select>', dbMultipleChoiceQuestionaire::field_status, $select)
		);
		$items .= $parser->get($row, $data);

		// Intro fuer die Fragenauswahl
		$quests_intro = sprintf('<div class="intro">%s</div>', mc_intro_questionaire_questions);
		
  	$quests = '';
  	if (empty($questionaire[dbMultipleChoiceQuestionaire::field_groups])) {
  		// keine Gruppen festgelegt
  		$quests = sprintf('<div class="info">%s</div>', mc_msg_questionaire_groups_empty);
  	}
  	else {
  		// Gruppen auslesen und Fragen zusammenstellen
  		if ($questionaire[dbMultipleChoiceQuestionaire::field_mode] == dbMultipleChoiceQuestionaire::mode_mixed) {
  			// im MIXED MODE sind alle Fragetypen zugelassen
  			$add = '';
  		}
  		else {
  			// ... in allen anderen Modi sind nur die entsprechenden Fragen zulässig
  			$add = sprintf(" AND %s='%s'", dbMultipleChoiceQuestion::field_mode, $questionaire[dbMultipleChoiceQuestionaire::field_mode]);
  		}
  		$like = '';
  		$sGroups = explode(',', $questionaire[dbMultipleChoiceQuestionaire::field_groups]);
  		foreach ($sGroups as $grp) {
  			if (!empty($like)) $like .= ' OR ';
  			$like .= sprintf(	'%1$s LIKE \'%2$s\' OR %1$s LIKE \'%2$s,%%\' OR %1$s LIKE \'%%,%2$s\' OR %1$s LIKE \'%%,%2$s,%%\'',
  												dbMultipleChoiceQuestion::field_groups, $grp); 
  		}
  		// zusaetzliches Handling fuer die Drag & Drop Sortierung
  		$sorter_table = 'mod_mc_question';
  		$sorter_value = $qid;
  		$sorter_active = 1;
  		$SQL = sprintf(	"SELECT %s FROM %s WHERE %s='%s'%s AND (%s)",
  										dbMultipleChoiceQuestion::field_id,
  										$dbMCQuestion->getTableName(),
  										dbMultipleChoiceQuestion::field_status,
  										dbMultipleChoiceQuestion::status_active,
  										$add,
  										$like);
  		$questions = array();
  		if (!$dbMCQuestion->sqlExec($SQL, $questions)) {
  			$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCQuestion->getError()));
  			return false;
  		}
  		$must_ids = array();
	  	foreach ($questions as $id) {
	  		$must_ids[] = $id[dbMultipleChoiceQuestion::field_id];
	  	}
	  	$where = array(
	  		dbMultipleChoiceTableSort::field_table 	=> $sorter_table,
	  		dbMultipleChoiceTableSort::field_value	=> $sorter_value);
	  	$sorter = array();
			if (!$dbMCTableSort->sqlSelectRecord($where, $sorter)) {
				$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCTableSort->getError()));
				return false;
			}
			if (count($sorter) < 1) {
				// Eintrag existiert noch nicht!
				$sorter_set = implode(',', $must_ids);
				$data = array(
					dbMultipleChoiceTableSort::field_table => $sorter_table,
					dbMultipleChoiceTableSort::field_value => $sorter_value,
					dbMultipleChoiceTableSort::field_order => $sorter_set
				);
				if (!$dbMCTableSort->sqlInsertRecord($data)) {
					$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCTableSort->getError()));
					return false;
				} 
			}
			else {
				// Eintrag auf Unterschiede pruefen und Datensatz aktualisieren
				$sorter_ids = explode(',', $sorter[0][dbMultipleChoiceTableSort::field_order]);
				$old = $sorter_ids;
				$check = array_diff($must_ids, $sorter_ids);
				foreach ($check as $id) $sorter_ids[] = $id;
				$check = array_diff($sorter_ids, $must_ids);
				foreach ($check as $id) unset($sorter_ids[$id]);
				$sorter_set = implode(',', $sorter_ids);
				if ($old !== $sorter_ids) {
					$where = array(dbMultipleChoiceTableSort::field_id => $sorter[0][dbMultipleChoiceTableSort::field_id]);
					$data = array(dbMultipleChoiceTableSort::field_order => $sorter_set);
					if (!$dbMCTableSort->sqlUpdateRecord($data, $where)) {
						$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCTableSort->getError()));
						return false;
					}
				}
			}
			$SQL = sprintf(	"SELECT * FROM %s WHERE %s='%s'%s AND (%s) ORDER BY FIND_IN_SET(%s, '%s')",
  										$dbMCQuestion->getTableName(),
  										dbMultipleChoiceQuestion::field_status,
  										dbMultipleChoiceQuestion::status_active,
  										$add,
  										$like,
  										dbMultipleChoiceQuestion::field_id,
  										$sorter_set);
  		$questions = array();
  		if (!$dbMCQuestion->sqlExec($SQL, $questions)) {
  			$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCQuestion->getError()));
  			return false;
  		}
  		
  		
  		$data = array(
  			'checkbox'		=> '',
  			'id'					=> mc_header_id,
  			'name'				=> mc_header_name,
  			'question'		=> mc_header_question,
  			'groups'			=> mc_header_groups
  		);
  		$quests_header = $parser->get($this->template_path.'backend.questionaire.edit.questions.header.htt', $data);
  		$row = new Dwoo_Template_File($this->template_path.'backend.questionaire.edit.questions.row.htt');
  		$flipflop = true;
  		$selected_questions = explode(',', $questionaire[dbMultipleChoiceQuestionaire::field_questions]);
  		foreach ($questions as $question) {
  			$flipflop ? $flipper = 'flip' : $flipper = 'flop';
  			$flipflop ? $flipflop = false : $flipflop = true;
  			// Fragen durchlaufen
  			(in_array($question[dbMultipleChoiceQuestion::field_id], $selected_questions)) ? $checked = ' checked="checked"' : $checked = '';
  			$data = array(
  				'row_id'			=> 'rowID_'.$question[dbMultipleChoiceQuestion::field_id],
  				'flipflop'		=> $flipper,
  				'checkbox'		=> sprintf(	'<input type="checkbox" name="%s[]" value="%s"%s />', 
  																	dbMultipleChoiceQuestionaire::field_questions, 
  																	$question[dbMultipleChoiceQuestion::field_id], 
  																	$checked),
  				'id'					=> sprintf(	'%05d', $question[dbMultipleChoiceQuestion::field_id]),
  				'name'				=> $question[dbMultipleChoiceQuestion::field_name],
  				'question'		=> sprintf(	'<span title="%s">%s</span>', 
  																	strip_tags($question[dbMultipleChoiceQuestion::field_description]),
  																	$question[dbMultipleChoiceQuestion::field_question]),
  				'groups'			=> str_replace(',', ', ', $question[dbMultipleChoiceQuestion::field_groups])
  			);
  			$quests .= $parser->get($row, $data);
  		}		
  	} // Gruppen

  	// Fragen nach Gruppen selektieren
  	$select = sprintf('<option value="%s">%s</option>', -1, mc_text_select);
  	$grps = explode(',', $questionaire[dbMultipleChoiceQuestionaire::field_groups]);
  	sort($grps);
  	foreach ($grps as $grp) {
  		$select .= sprintf('<option value="%s">%s</option>', $grp, $grp);
  	}
  	$select = sprintf('<select style="width:250px;" name="%s" size="1">%s</select>', self::request_question_select_grps, $select);
  	$select_grps = sprintf(	'<div class="%s">%s</div>',
  													self::request_question_select_grps,
  													sprintf(mc_text_question_grps_select, $select));
  	
  	// Mitteilungen anzeigen
		if ($this->isMessage()) {
			$intro = sprintf('<div class="message">%s</div>', $this->getMessage());
		}
		else {
			$intro = sprintf('<div class="intro">%s</div>', mc_intro_questionaire_edit);
		}

  	$data = array(
			'header'						=> mc_header_questionaire_edit,
			'intro'							=> $intro,
			'form_name'					=> 'questionaire_edit',
			'form_action'				=> $this->page_link,
			'action_name'				=> self::request_action,
			'action_value'			=> self::action_questionaire,
			'tab_action_name'		=> self::request_questionaire_tab,
			'tab_action_value'	=> self::action_questionaire_tab_edit,
			'sub_action_name'		=> self::request_sub_action,
			'sub_action_value'	=> self::action_questionaire_edit_check,
			'qid_name'					=> dbMultipleChoiceQuestionaire::field_id,
			'qid_value'					=> $qid,
			'items'							=> $items,
  		'questions_intro'		=> $quests_intro,
  		'questions_header'	=> $quests_header,
  		'questions'					=> $quests,
  		'question_groups'		=> $select_grps,
  		'sorter_table'			=> $sorter_table,
  		'sorter_value'			=> $qid,
  		'sorter_active'			=> 1,
			'btn_ok'						=> mc_btn_ok,
			'btn_abort'					=> mc_btn_abort,
			'abort_location'		=> $this->page_link
		);
		return $parser->get($this->template_path.'backend.questionaire.edit.htt', $data);
  } // dlgQuestionaireEdit()

  /**
   * Prüft den Datensatz und speichert den Fragebogen
   * 
   * @return STR dlgQuestionaireEdit()
   */
  public function checkQuestionaire() {
  	global $dbMCQuestionaire;
  	global $tools;
  	global $dbMCQuestion;
  	
  	(isset($_REQUEST[dbMultipleChoiceQuestionaire::field_id])) ? $qid = $_REQUEST[dbMultipleChoiceQuestionaire::field_id] : $qid = -1;
  	
  	$message = '';
  	// Mindestbedingungen prüfen
  	if (!isset($_REQUEST[dbMultipleChoiceQuestionaire::field_name]) || empty($_REQUEST[dbMultipleChoiceQuestionaire::field_name])) {
  		$message .= mc_msg_questionaire_name_missing;
  	}
  	if (!isset($_REQUEST[dbMultipleChoiceQuestionaire::field_title]) || empty($_REQUEST[dbMultipleChoiceQuestionaire::field_title])) {
  		$message .= mc_msg_questionaire_title_missing;
  	}
  	if (!isset($_REQUEST[dbMultipleChoiceQuestionaire::field_groups]) || empty($_REQUEST[dbMultipleChoiceQuestionaire::field_groups])) {
  		$message .= mc_msg_questionaire_groups_empty;
  	}
  	if (!empty($message)) {
  		// Mindesbedingungen nicht erfüllt, zurück zum Dialog
  		$this->setMessage($message);
  		return $this->dlgQuestionaireEdit();
  	}
  	
  	// Daten zusammenstellen
  	$data = array();
  	foreach ($dbMCQuestionaire->getFields() as $key => $value) {
  		switch ($key):
  		case dbMultipleChoiceQuestionaire::field_groups:
  		case dbMultipleChoiceQuestionaire::field_questions:
  			isset($_REQUEST[$key]) ? $data[$key] = implode(',', $_REQUEST[$key]) : $data[$key] = $value;
  			break;
  		case dbMultipleChoiceQuestionaire::field_update_by:
  			$data[$key] = $tools->getDisplayName(); 
  			break;
  		case dbMultipleChoiceQuestionaire::field_update_when:
  			$data[$key] = date('Y-m-d H:i:s');
  			break;
  		default:
  			isset($_REQUEST[$key]) ? $data[$key] = $_REQUEST[$key] : $data[$key] = $value;
  		endswitch;
  	}
  	
  	// pruefen ob eine Gruppe von Fragen uebernommen werden soll
  	if (isset($_REQUEST[self::request_question_select_grps]) && ($_REQUEST[self::request_question_select_grps])) {
  		$grp = $_REQUEST[self::request_question_select_grps];
  		unset($_REQUEST[self::request_question_select_grps]);
  		unset($_REQUEST[dbMultipleChoiceQuestionaire::field_questions]);
  		$like = sprintf('%1$s LIKE \'%2$s\' OR %1$s LIKE \'%2$s,%%\' OR %1$s LIKE \'%%,%2$s\' OR %1$s LIKE \'%%,%2$s,%%\'',
  										dbMultipleChoiceQuestion::field_groups, $grp);
  		$SQL = sprintf(	"SELECT %s FROM %s WHERE %s='%s' AND (%s)",
  										dbMultipleChoiceQuestion::field_id,
  										$dbMCQuestion->getTableName(),
  										dbMultipleChoiceQuestion::field_status,
  										dbMultipleChoiceQuestion::status_active,
  										$like);
  		$quests = array();
  		if (!$dbMCQuestion->sqlExec($SQL, $quests)) {
  			$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCQuestion->getError()));
  			return false;
  		}
  		$act_grps = explode(',', $data[dbMultipleChoiceQuestionaire::field_questions]);
  		$add = $data[dbMultipleChoiceQuestionaire::field_questions];
  		foreach ($quests as $item) {
  			if (!in_array($item[dbMultipleChoiceQuestion::field_id], $act_grps)) {
  				if (!empty($add)) $add .= ',';
  				$add .= $item[dbMultipleChoiceQuestion::field_id];
  			}
  		}
  		$data[dbMultipleChoiceQuestionaire::field_questions] = $add;
  	}
  	
  	if ($qid == -1) {
  		// neuer Datensatz
  		if (!$dbMCQuestionaire->sqlInsertRecord($data, $qid)) {
  			$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCQuestionaire->getError()));
  			return false;
  		}
  		// $_REQUEST setzen
  		$_REQUEST[dbMultipleChoiceQuestionaire::field_id] = $qid;
  		$message .= sprintf(mc_msg_questionaire_inserted, $qid);
  	}
  	else {
  		// Datensatz aktualisieren
  		$where = array();
  		$where[dbMultipleChoiceQuestionaire::field_id] = $qid;
  		if (!$dbMCQuestionaire->sqlUpdateRecord($data, $where)) {
  			$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCQuestionaire->getError()));
  			return false;
  		}
  		$message .= sprintf(mc_msg_questionaire_updated, $qid);
  	}
  	
  	$this->setMessage($message);
  	return $this->dlgQuestionaireEdit();
  } // checkQuestionaire()

  public function dlgHelp() {
  	global $parser;
  	$data = array(
  		'release'		=> $this->getVersion(),
  		'img'				=> WB_URL.'/modules/multiplechoice/img/multiplechoice-logo-450.gif',
  		'qrcode'		=> WB_URL.'/modules/'.basename(dirname(__FILE__)).'/img/qr-phpmanufaktur-135.png',
  	);
  	return $parser->get($this->template_path.'backend.about.htt', $data);
  }
  
} // class mcBackend

?>