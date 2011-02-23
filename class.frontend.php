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
  
  $Id$
  
**/

// prevent this file from being accessed directly
if (!defined('WB_PATH')) die('invalid call of '.$_SERVER['SCRIPT_NAME']);

if (!defined('DEBUG_MODE')) define('DEBUG_MODE', true);

if (DEBUG_MODE) {
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
}
else {
	ini_set('display_errors', 0);
	error_reporting(E_ERROR);
}

require_once(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/initialize.php');

global $parser;
global $tools;
global $dbCfg;
global $dbMCQuestion;
global $dbMCQuestionaire;
global $dbMCQuestionItem;
global $dbMCTableSort;

if (!is_object($parser)) $parser = new Dwoo();
if (!is_object($tools)) $tools = new rhTools();
if (!is_object($dbCfg)) $dbCfg = new dbMultipleChoiceCfg();
if (!is_object($dbMCQuestion)) $dbMCQuestion = new dbMultipleChoiceQuestion();
if (!is_object($dbMCQuestionaire)) $dbMCQuestionaire = new dbMultipleChoiceQuestionaire();
if (!is_object($dbMCQuestionItem)) $dbMCQuestionItem = new dbMultipleChoiceQuestionItem();
if (!is_object($dbMCTableSort)) $dbMCTableSort = new dbMultipleChoiceTableSort();

class multipleChoiceFrontend {
	
	const request_action		= 'act';
	
	const action_default		= 'def';
	const action_submit			= 'sub';
	const action_check			= 'chk';

	private $qid = -1;
	private $message;
	private $error;
	private $page_link;
	private $template_path;
	private $show_answer = false;
	
	/**
	 * Konstruktor
	 */
	public function __construct($id=-1, $show_answer=false, $use_url='') {
		global $tools;
		$this->qid = $id;
		$this->show_answer = (bool) $show_answer;
		if (!empty($use_url)) {
			$this->page_link = $use_url;
		}
		else {
			$tools->getPageLinkByPageID(PAGE_ID, $this->page_link);
		}
		$this->template_path = WB_PATH . '/modules/' . basename(dirname(__FILE__)) . '/htt/' ;
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
  	$html_allowed = array();
  	foreach ($_REQUEST as $key => $value) {
  		if (!in_array($key, $html_allowed)) {
  			$_REQUEST[$key] = $this->xssPrevent($value);
  		}
  	}
    isset($_REQUEST[self::request_action]) ? $action = $_REQUEST[self::request_action] : $action = self::action_default;
  	switch ($action):
  	default:
  		return $this->show($this->showQuestionaire());
  		break;
  	endswitch;
  } // action
  
  public function show($content) {
  	global $parser;
  	if ($this->isError()) {
  		$content = $this->getError();
  		$class = ' class="error"';
  	}
  	else {
  		$class = '';
  	}
  	$data = array(
  		'class'						=> $class,
  		'content'					=> $content,
  	);
  	return $parser->get($this->template_path.'frontend.body.htt', $data);
  }
  
  public function showQuestionaire() {
  	global $dbMCQuestionaire;
  	global $dbMCQuestionItem;
  	global $dbMCQuestion;
  	global $parser;
  	global $dbMCTableSort;
  	
  	if ($this->qid < 1) {
  		// keine ID angegeben
  		$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, mc_error_id_invalid));
  		return false;
  	}
  	$where = array();
  	$where[dbMultipleChoiceQuestionaire::field_id] = $this->qid;
  	$where[dbMultipleChoiceQuestionaire::field_status] = dbMultipleChoiceQuestionaire::status_active;
  	$questionaire = array();
  	if (!$dbMCQuestionaire->sqlSelectRecord($where, $questionaire)) {
  		$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCQuestionaire->getError()));
  		return false;
  	}
  	if (count($questionaire) < 1) {
  		$this->setError(sprintf(mc_error_questionaire_id, $this->qid));
  		return false;
  	}
  	$questionaire = $questionaire[0];
  	
  	(isset($_REQUEST[self::request_action]) && $_REQUEST[self::request_action] == self::action_check) ? $check_result = true : $check_result = false;
  	
  	$items = '';
  	$questions_array = explode(',', $questionaire[dbMultipleChoiceQuestionaire::field_questions]);
  	$in = '';
  	foreach ($questions_array as $question) {
  		if (!empty($in)) $in .= ',';
  		$in .= sprintf("'%s'", $question); 
  	}
  	if ($questionaire[dbMultipleChoiceQuestionaire::field_shuffle_questions] == 1) {
  		// Fragen durcheinanderwürfeln
  		$order = ' ORDER BY RAND()';
  	}
  	else { 
  		$order = '';	
  		$where = array(
  			dbMultipleChoiceTableSort::field_table	=> 'mod_mc_question',
  			dbMultipleChoiceTableSort::field_value	=> $this->qid
  		);
  		$sorter = array();
  		if (!$dbMCTableSort->sqlSelectRecord($where, $sorter)) {
  			$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCTableSort->getError()));
  			return false;
  		}
  		if (count($sorter) > 0) {
  			$order = sprintf(" ORDER BY FIND_IN_SET(%s,'%s')", dbMultipleChoiceQuestion::field_id, $sorter[0][dbMultipleChoiceTableSort::field_order]);
  		}
  	}
  	$SQL = sprintf(	"SELECT * FROM %s WHERE %s IN (%s) AND %s='%s'%s",
  									$dbMCQuestion->getTableName(),
  									dbMultipleChoiceQuestion::field_id,
  									$in,
  									dbMultipleChoiceQuestion::field_status,
  									dbMultipleChoiceQuestion::status_active,
  									$order);
  	$questions = array();
  	if (!$dbMCQuestion->sqlExec($SQL, $questions)) {
  		$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCQuestion->getError()));
  		return false;
  	}
  	if (count($questions) < 1) {
  		$this->setError(sprintf(mc_error_questions, $this->qid));
  		return false;
  	}
  	
  	$row = new Dwoo_Template_File($this->template_path.'frontend.questionaire.row.htt');
  	$row_span = new Dwoo_Template_File($this->template_path.'frontend.questionaire.row.span.htt');
  	
  	$solutions_total = 0;
  	$solutions_correct = 0;
  	$solutions_false = 0;
  	
  	foreach ($questions as $question) {
  		$answers_array = explode(',', $question[dbMultipleChoiceQuestion::field_answers]);
  		$solutions_array = explode(',', $question[dbMultipleChoiceQuestion::field_solutions]);
  		
  		switch ($questionaire[dbMultipleChoiceQuestionaire::field_mode]):
  		case dbMultipleChoiceQuestionaire::mode_mixed:
  		case dbMultipleChoiceQuestionaire::mode_partial:
  			// alle richtigen Antwortmöglichkeiten zählen
  			$solutions_total += count($solutions_array);
  			$mixed_mode = true;
  			break;
  		default:
  			// jede Frage zählt einfach
  			$solutions_total++;
  			$mixed_mode = false;
  			break;
  		endswitch;
  		
  		$in = '';
  		foreach ($answers_array as $answer) {
  			if (!empty($in)) $in .= ',';
  			$in .= sprintf("'%s'", $answer);
  		}
  		if ($questionaire[dbMultipleChoiceQuestionaire::field_shuffle_answers] == 1) {
  			// Fragen durcheinanderwürfeln
  			$order = ' ORDER BY RAND()';
  		}
  		else { 
  			$order = '';	
  		}
  		$SQL = sprintf(	"SELECT * FROM %s WHERE %s IN (%s) AND %s='%s'%s",
  										$dbMCQuestionItem->getTableName(),
  										dbMultipleChoiceQuestionItem::field_id,
  										$in,
  										dbMultipleChoiceQuestionItem::field_status,
  										dbMultipleChoiceQuestionItem::status_active,
  										$order);
  		$answers = array();
  		if (!$dbMCQuestionItem->sqlExec($SQL, $answers)) {
  			$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbMCQuestionItem->getError()));
  			return false;
  		}
  		if (count($answers) < 1) {
  			$this->setError(sprintf(mc_error_answers, $question[dbMultipleChoiceQuestion::field_id]));
  			return false;
  		}
  		$ans = '';
  		$correct = 0;
  		$false = 0; 
  		foreach ($answers as $answer) {
  			if (isset($_REQUEST[dbMultipleChoiceQuestion::field_answers.'_'.$question[dbMultipleChoiceQuestion::field_id]])) {
  				$ans_array = $_REQUEST[dbMultipleChoiceQuestion::field_answers.'_'.$question[dbMultipleChoiceQuestion::field_id]];
  			}
  			else {
  				$ans_array = array();
  			}
  			if ($check_result) {
  				if (($answer[dbMultipleChoiceQuestionItem::field_is_correct] == 1) && 
  				    (in_array($answer[dbMultipleChoiceQuestionItem::field_id], $ans_array))) {
  					// richtige Antwort
  					$class = "mc_answer_correct";	
  					$correct++;	
  			  }
  				elseif (($answer[dbMultipleChoiceQuestionItem::field_is_correct] == 0) && 
  			      		(in_array($answer[dbMultipleChoiceQuestionItem::field_id], $ans_array))) {
  			    // falsche Antwort
  			    $class = "mc_answer_false";
  			    $false++;
  			  }
  			  elseif (($this->show_answer) && 
  			  				($answer[dbMultipleChoiceQuestionItem::field_is_correct] == 1) &&
  			  				(!empty($ans_array))) {
  			  	$class = "mc_answer_hint_correct";
  			  }
  			  else {
  			  	$class = "mc_answer";
  			  }
  			}
  			else {
  				$class = 'mc_answer';
  			}
  			
  			(in_array($answer[dbMultipleChoiceQuestionItem::field_id], $ans_array)) ? $checked = ' checked="checked"' : $checked = '';
  			$data = array(
  				'class'				=> $class,
  				'checkbox'		=> sprintf('<input type="checkbox" name="%s[]" value="%s"%s />',
  																	dbMultipleChoiceQuestion::field_answers.'_'.$question[dbMultipleChoiceQuestion::field_id],
  																	$answer[dbMultipleChoiceQuestionItem::field_id],
  																	$checked),
  				'answer'			=> $answer[dbMultipleChoiceQuestionItem::field_question],
  				'info'				=> ''
  			);
  			$ans .= $parser->get($row, $data);
  		} // foreach
  		
  		
  		$data = array(
  			'class'	=> 'mc_question',
  			'item' 	=> $question[dbMultipleChoiceQuestion::field_question]
  		);
  		$items .= $parser->get($row_span, $data);
  		$data = array(
  			'class'	=> 'mc_description',
  			'item' 	=> $question[dbMultipleChoiceQuestion::field_description]
  		);
  		$items .= $parser->get($row_span, $data);
  		$items .= $ans;
  		
  		if ($check_result) {
  			// Auswertung
  			$max_solutions = count(explode(',', $question[dbMultipleChoiceQuestion::field_solutions]));
  			if ($mixed_mode) {
  				// prozentuale Auswertung
  				$x = $correct-$false; 
  				if ($x < 0) $x = 0; 
  				$solutions_correct += $x;
  				if (($max_solutions == $correct) && ($false == 0)) {
  					// Frage richtig beantwortet
  					if (!empty($question[dbMultipleChoiceQuestion::field_prompt_correct])) {
  						$items .= $parser->get($row_span, array('item' => sprintf('<div class="mc_answer_explain">%s</div>', $question[dbMultipleChoiceQuestion::field_prompt_correct])));
  					}
  				}
  				elseif ($correct == 0) {
  					// Frage falsch beantwortet
  					if (!empty($question[dbMultipleChoiceQuestion::field_prompt_false])) {
  						$items .= $parser->get($row_span, array('item' => sprintf('<div class="mc_answer_explain">%s</div>', $question[dbMultipleChoiceQuestion::field_prompt_false])));
  					}
  				}
  				else {
  					// Frage teilweise beantwortet
  					if (!empty($question[dbMultipleChoiceQuestion::field_prompt_partial])) {
  						$items .= $parser->get($row_span, array('item' => sprintf('<div class="mc_answer_explain">%s</div>', $question[dbMultipleChoiceQuestion::field_prompt_partial])));
  					}
  				}
  			}
  			elseif (($false == 0) && ($max_solutions == $correct)) {
  				// strikte Auswertung, Frage richtig beantwortet	
  				$solutions_correct++;
  				if (!empty($question[dbMultipleChoiceQuestion::field_prompt_correct])) {
  					$items .= $parser->get($row_span, array('item' => sprintf('<div class="mc_answer_explain">%s</div>', $question[dbMultipleChoiceQuestion::field_prompt_correct])));
  				}
  			}
  			else {
  				// Falsche Antwort
  				if (!empty($question[dbMultipleChoiceQuestion::field_prompt_false])) {
  					$items .= $parser->get($row_span, array('item' => sprintf('<div class="mc_answer_explain">%s</div>', $question[dbMultipleChoiceQuestion::field_prompt_false])));
  				}
  			}
  		}
  		
  	}
  	
  	if ($check_result) {
  		// Auswertung anzeigen
  		if ($mixed_mode) {
  			$percent = (100/$solutions_total) * $solutions_correct;
  			$sol = sprintf(mc_msg_solutions_mixed, $percent);
  		}
  		else {
  			$sol = sprintf(mc_msg_solutions_strict, $solutions_correct, $solutions_total);
  		}
  		$data = array(
  			'item' => $sol
  		);
  		$items .= $parser->get($row_span, $data);
  	}
  	
  	// Mitteilungen anzeigen
		if ($this->isMessage()) {
			$intro = sprintf('<div class="message">%s</div>', $this->getMessage());
		}
		elseif (!empty($questionaire[dbMultipleChoiceQuestionaire::field_description])) {
			$intro = sprintf('<div class="intro">%s</div>', $questionaire[dbMultipleChoiceQuestionaire::field_description]);
		}
		else {
			$intro = '';
		}
  	
  	$data = array(
  		'form_name'				=> 'mc_questionaire',
  		'form_action'			=> $this->page_link,
  		'action_name'			=> self::request_action,
  		'action_value'		=> self::action_check,
  		'qid_name'				=> dbMultipleChoiceQuestionaire::field_id,
  		'qid_value'				=> $this->qid,
  		'header'					=> $questionaire[dbMultipleChoiceQuestionaire::field_title],
  		'intro'						=> $intro,
  		'items'						=> $items,
  		'btn_submit'			=> mc_btn_submit,
  		'btn_reset'				=> mc_btn_reset
  	);
  	return $parser->get($this->template_path.'frontend.questionaire.htt', $data);
  }
	
} // class multipleChoiceFrontend

?>