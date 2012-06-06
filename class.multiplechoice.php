<?php

/**
 * dbMultipleChoice
 *
 * @author Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @link http://phpmanufaktur.de
 * @copyright 2010 - 2012
 * @license http://www.gnu.org/licenses/gpl.html GNU Public License (GPL)
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

if (!defined('DEBUG_MODE')) define('DEBUG_MODE', true);

class dbMultipleChoiceQuestionaire extends dbConnectLE {

	const field_id										= 'qn_id';
	const field_name									= 'qn_name';
	const field_title									= 'qn_title';
	const field_description						= 'qn_desc';
	const field_mode									= 'qn_mode';
	const field_groups								= 'qn_groups';
	const field_shuffle_questions			= 'qn_shuffle_quest';
	const field_shuffle_answers				= 'qn_shuffle_answ';
	const field_questions							= 'qn_questions';
	const field_status								= 'qn_status';
	const field_update_when						= 'qn_update_when';
	const field_update_by							= 'qn_update_by';

	const mode_mixed									= 'modeMixed';
	const mode_multiple								= 'modeMultiple';
	const mode_partial								= 'modePartial';
	const mode_single									= 'modeSingle';
	const mode_none										= 'modeNone';

	public $mode_array = array(
		self::mode_mixed				=> mc_mode_qn_mixed,
		self::mode_multiple			=> mc_mode_qn_multiple,
		self::mode_partial			=> mc_mode_qn_partial,
		self::mode_single				=> mc_mode_qn_single,
		self::mode_none					=> mc_mode_qn_none
	);

	const status_active								= 'statusActive';
	const status_locked								= 'statusLocked';
	const status_deleted							= 'statusDeleted';

	public $status_array = array(
		self::status_active			=> mc_status_active,
		self::status_locked			=> mc_status_locked,
		self::status_deleted		=> mc_status_deleted
	);

	private $create_tables 			= false;

	public function __construct($create_tables=false) {
		$this->create_tables = $create_tables;
		parent::__construct();
		$this->setTableName('mod_mc_questionaire');
		$this->addFieldDefinition(self::field_id, "INT(11) NOT NULL AUTO_INCREMENT", true);
		$this->addFieldDefinition(self::field_name, "VARCHAR(80) NOT NULL DEFAULT ''");
		$this->addFieldDefinition(self::field_title, "VARCHAR(80) NOT NULL DEFAULT ''");
		$this->addFieldDefinition(self::field_description, "TEXT NOT NULL DEFAULT ''", false, false, true);
		$this->addFieldDefinition(self::field_mode, "VARCHAR(20) NOT NULL DEFAULT '".self::mode_mixed."'");
		$this->addFieldDefinition(self::field_groups, "VARCHAR(255) NOT NULL DEFAULT ''");
		$this->addFieldDefinition(self::field_shuffle_questions, "TINYINT NOT NULL DEFAULT '0'");
		$this->addFieldDefinition(self::field_shuffle_answers, "TINYINT NOT NULL DEFAULT '0'");
		$this->addFieldDefinition(self::field_questions, "VARCHAR(255) NOT NULL DEFAULT ''");
		$this->addFieldDefinition(self::field_status, "VARCHAR(20) NOT NULL DEFAULT '".self::status_active."'");
		$this->addFieldDefinition(self::field_update_when, "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'");
		$this->addFieldDefinition(self::field_update_by, "VARCHAR(80) NOT NULL DEFAULT 'SYSTEM'");
		$this->checkFieldDefinitions();
		if ($this->create_tables) {
			if (!$this->sqlTableExists()) {
				if (!$this->sqlCreateTable()) {
					$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $this->getError()));
					return false;
				}
			}
		}
		// important: switch decoding OFF.
		$this->setDecodeSpecialChars(false);
	} // __construct()

} // class dbMultipleChoiceQuestionaire

class dbMultipleChoiceQuestion extends dbConnectLE {

	const field_id										= 'quest_id';
	const field_name									= 'quest_name';
	const field_question							= 'quest_question';
	const field_description						= 'quest_desc';
	const field_groups								= 'quest_groups';
	const field_use_html							= 'quest_use_html';
	const field_answers								= 'quest_answers';
	const field_solutions							= 'quest_solutions';
	const field_mode									= 'quest_mode';
	const field_prompt_correct				= 'quest_prompt_correct';
	const field_prompt_partial				= 'quest_prompt_partial';
	const field_prompt_false					= 'quest_prompt_false';
	const field_status								= 'quest_status';
	const field_update_when						= 'quest_update_when';
	const field_update_by							= 'quest_update_by';

	const status_active								= 'statusActive';
	const status_locked								= 'statusLocked';
	const status_deleted							= 'statusDeleted';
	const status_locked_temp					= 'statusLockedTemp';

	public $status_array = array(
		self::status_active			 => mc_status_active,
		self::status_locked			 => mc_status_locked,
		self::status_locked_temp => mc_status_locked,
		self::status_deleted		 => mc_status_deleted
	);

	const mode_multiple								= 'modeMultiple';
	const mode_partial								= 'modePartial';
	const mode_single									= 'modeSingle';
	const mode_none										= 'modeNone';

	public $mode_array = array(
		self::mode_multiple			=> mc_mode_multiple,
		self::mode_partial			=> mc_mode_partial,
		self::mode_single				=> mc_mode_single,
		self::mode_none					=> mc_mode_none
	);

	private $create_tables 			= false;

	public function __construct($create_tables=false) {
		$this->create_tables = $create_tables;
		parent::__construct();
		$this->setTableName('mod_mc_question');
		$this->addFieldDefinition(self::field_id, "INT(11) NOT NULL AUTO_INCREMENT", true);
		$this->addFieldDefinition(self::field_name, "VARCHAR(80) NOT NULL DEFAULT ''");
		$this->addFieldDefinition(self::field_question, "VARCHAR(80) NOT NULL DEFAULT ''");
		$this->addFieldDefinition(self::field_description, "TEXT NOT NULL DEFAULT ''", false, false, true);
		$this->addFieldDefinition(self::field_groups, "VARCHAR(255) NOT NULL DEFAULT ''");
		$this->addFieldDefinition(self::field_use_html, "TINYINT NOT NULL DEFAULT '0'");
		$this->addFieldDefinition(self::field_answers, "VARCHAR(255) NOT NULL DEFAULT ''");
		$this->addFieldDefinition(self::field_solutions, "VARCHAR(255) NOT NULL DEFAULT ''");
		$this->addFieldDefinition(self::field_mode, "VARCHAR(20) NOT NULL DEFAULT '".self::mode_multiple."'");
		$this->addFieldDefinition(self::field_prompt_correct, "TEXT NOT NULL DEFAULT ''", false, false, true);
		$this->addFieldDefinition(self::field_prompt_partial, "TEXT NOT NULL DEFAULT ''", false, false, true);
		$this->addFieldDefinition(self::field_prompt_false, "TEXT NOT NULL DEFAULT ''", false, false, true);
		$this->addFieldDefinition(self::field_status, "VARCHAR(20) NOT NULL DEFAULT '".self::status_active."'");
		$this->addFieldDefinition(self::field_update_when, "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'");
		$this->addFieldDefinition(self::field_update_by, "VARCHAR(80) NOT NULL DEFAULT 'SYSTEM'");
		$this->checkFieldDefinitions();
		if ($this->create_tables) {
			if (!$this->sqlTableExists()) {
				if (!$this->sqlCreateTable()) {
					$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $this->getError()));
					return false;
				}
			}
		}
		// important: switch decoding OFF.
		$this->setDecodeSpecialChars(false);
	} // __construct()

} // class dbMultipleChoiceQuestion

class dbMultipleChoiceQuestionItem extends dbConnectLE {

	const field_id										= 'qi_id';
	const field_question_id						= 'quest_id';
	const field_question							= 'qi_question';
	const field_is_correct						= 'qi_is_correct';
	const field_status								= 'qi_status';
	const field_update_when						= 'qi_update_when';
	const field_update_by							= 'qi_update_by';

	const status_active								= 'statusActive';
	//const status_locked								= 'statusLocked';
	const status_deleted							= 'statusDeleted';

	public $status_array = array(
		self::status_active			=> mc_status_active,
		//self::status_locked			=> mc_status_locked,
		self::status_deleted		=> mc_status_deleted
	);

	private $create_tables 			= false;

	public function __construct($create_tables=false) {
		$this->create_tables = $create_tables;
		parent::__construct();
		$this->setTableName('mod_mc_question_item');
		$this->addFieldDefinition(self::field_id, "INT(11) NOT NULL AUTO_INCREMENT", true);
		$this->addFieldDefinition(self::field_question_id, "INT(11) NOT NULL DEFAULT '-1'");
		$this->addFieldDefinition(self::field_question, "TEXT NOT NULL DEFAULT ''", false, false, true);
		$this->addFieldDefinition(self::field_is_correct, "TINYINT NOT NULL DEFAULT '0'");
		$this->addFieldDefinition(self::field_status, "VARCHAR(20) NOT NULL DEFAULT '".self::status_active."'");
		$this->addFieldDefinition(self::field_update_when, "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'");
		$this->addFieldDefinition(self::field_update_by, "VARCHAR(80) NOT NULL DEFAULT 'SYSTEM'");
		$this->checkFieldDefinitions();
		if ($this->create_tables) {
			if (!$this->sqlTableExists()) {
				if (!$this->sqlCreateTable()) {
					$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $this->getError()));
					return false;
				}
			}
		}
		// important: switch decoding OFF.
		$this->setDecodeSpecialChars(false);
	} // __construct()

} // class dbMultipleChoiceQuestionItem

class dbMultipleChoiceQuestionHint extends dbConnectLE {

	const field_id					= 'qh_id';
	const field_group				= 'qh_group';
	const field_name				= 'qh_name';
	const field_hint				= 'qh_hint';
	const field_status			= 'qh_status';
	const field_timestamp		= 'qh_timestamp';

	const group_correct			= 1;
	const group_false				= 2;
	const group_partial			= 3;
	const group_undefined		= 0;

	const status_active								= 1;
	const status_deleted							= 0;

	public $status_array = array(
		self::status_active			=> mc_status_active,
		self::status_deleted		=> mc_status_deleted
	);

	private $create_tables 			= false;

	public function __construct($create_tables=false) {
		$this->create_tables = $create_tables;
		parent::__construct();
		$this->setTableName('mod_mc_question_hint');
		$this->addFieldDefinition(self::field_id, "INT(11) NOT NULL AUTO_INCREMENT", true);
		$this->addFieldDefinition(self::field_group, "TINYINT NOT NULL DEFAULT '".self::group_undefined."'");
		$this->addFieldDefinition(self::field_name, "VARCHAR(128) NOT NULL DEFAULT ''");
		$this->addFieldDefinition(self::field_hint, "TEXT NOT NULL DEFAULT ''", false, false, true);
		$this->addFieldDefinition(self::field_status, "TINYINT NOT NULL DEFAULT '".self::status_active."'");
		$this->addFieldDefinition(self::field_timestamp, "TIMESTAMP");
		$this->checkFieldDefinitions();
		if ($this->create_tables) {
			if (!$this->sqlTableExists()) {
				if (!$this->sqlCreateTable()) {
					$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $this->getError()));
					return false;
				}
			}
		}
	} // __construct()

} // class dbMultipleChoiceQuestionHint

class dbMultipleChoiceTableSort extends dbConnectLE {

	const field_id				= 'sort_id';
	const field_table			= 'sort_table';
	const field_value			= 'sort_value';
	const field_order			= 'sort_order';
	const field_timestamp	= 'sort_timestamp';

	private $create_tables = false;

	public function __construct($create_tables=false) {
		$this->create_tables = $create_tables;
		parent::__construct();
		$this->setTableName('mod_mc_table_sort');
		$this->addFieldDefinition(self::field_id, "INT(11) NOT NULL AUTO_INCREMENT", true);
		$this->addFieldDefinition(self::field_table, "VARCHAR(64) NOT NULL DEFAULT ''");
		$this->addFieldDefinition(self::field_value, "VARCHAR(255) NOT NULL DEFAULT ''");
		$this->addFieldDefinition(self::field_order, "TEXT NOT NULL DEFAULT ''");
		$this->addFieldDefinition(self::field_timestamp, "TIMESTAMP");
		$this->checkFieldDefinitions();
		if ($this->create_tables) {
			if (!$this->sqlTableExists()) {
				if (!$this->sqlCreateTable()) {
					$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $this->getError()));
					return false;
				}
			}
		}
	} // __construct()

}

?>