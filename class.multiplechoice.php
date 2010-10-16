<?php

/**
  Module developed for the Open Source Content Management System Website Baker (http://websitebaker.org)
  Copyright (c) 2010, Ralf Hertsch
  Contact me: ralf.hertsch@phpManufaktur.de, http://phpManufaktur.de

  This module is free software. You can redistribute it and/or modify it
  under the terms of the GNU General Public License  - version 2 or later,
  as published by the Free Software Foundation: http://www.gnu.org/licenses/gpl.html.

  This module is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  $Id: class.multiplechoice.php 5 2010-07-17 14:29:51Z ralf $

**/

// prevent this file from being accessed directly
if (!defined('WB_PATH')) die('invalid call of '.$_SERVER['SCRIPT_NAME']);

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

?>