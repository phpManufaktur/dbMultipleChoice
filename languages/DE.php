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

  $Id: DE.php 2 2010-06-14 18:34:44Z ralf $

	IMPORTANT NOTE:

  If you are editing this file or creating a new language file
  you must ensure that you SAVE THIS FILE UTF-8 ENCODED.
  Otherwise all special chars will be destroyed and displayed improper!

	It is NOT NECESSARY to mask special chars as HTML entities!

	The Original Source is GERMAN (DE) by Ralf Hertsch, please use
  always this file as reference for translations.

**/

// Deutsche Modulbeschreibung
$module_description 	= 'dbMultipleChoice ermöglicht die Erstellung und Durchführung von Multiple Choice Tests';

// name of the person(s) who translated and edited this language file
$module_translation_by = 'Ralf Hertsch (phpManufaktur)';

define('mc_btn_abort',													'Abbruch');
define('mc_btn_export',													'Exportieren');
define('mc_btn_import',													'Importieren');
define('mc_btn_ok',															'Übernehmen');
define('mc_btn_reset',													'Fragebogen zurücksetzen');
define('mc_btn_submit',													'Fragebogen übermitteln');

define('mc_desc_cfg_groups',										'Sie können die Fragen in beliebigen Gruppen organisieren. Trennen Sie die Gruppen mit einem Komma, der erste Eintrag ist der Vorgabewert.');
define('mc_desc_cfg_max_items',									'Legen Sie die maximale Anzahl an möglichen Antworten je Frage fest (Limit: <b>15</b>).');
define('mc_desc_cfg_min_items',									'Legen Sie die minimale Anzahl an möglichen Antworten je Frage fest.');
define('mc_desc_cfg_preselect_html',						'Sie können festlegen, ob bei dem Anlegen von neuen Fragen HTML <i>für die Antworten</i> voreingestellt ist oder nicht (0=NEIN, 1=JA)');

define('mc_error_cfg_id',												'<p>Der Konfigurationsdatensatz mit der <b>ID %05d</b> konnte nicht ausgelesen werden!</p>');
define('mc_error_cfg_name',											'<p>Zu dem Bezeichner <b>%s</b> wurde kein Konfigurationsdatensatz gefunden!</p>');
define('mc_error_id_missing',										'<p>Der Datensatz mit der <b>ID %05d</b> wurde nicht gefunden.</p>');
define('mc_error_id_invalid',										'<p>Es wurde eine ungültige <b>ID</b> übergeben, es können keine Daten ausgelesen werden.</p>');
define('mc_error_questionaire_id',							'<p>Der angeforderte Fragebogen mit der <b>ID %05d</b> ist gesperrt oder existiert nicht.</p>');
define('mc_error_questions',										'<p>Die dem Fragebogen <b>ID %05d</b> zugeordneten Fragen konnten nicht ausgelesen werden.</p>');
define('mc_error_answers',											'<p>Die der Frage <b>ID %05d</b> zugeordneten Antworten konnten nicht ausgelesen werden.</p>');

define('mc_header_cfg',													'Einstellungen');
define('mc_header_cfg_description',							'Beschreibung');
define('mc_header_cfg_identifier',							'Bezeichner');
define('mc_header_cfg_label',										'Label');
define('mc_header_cfg_typ',											'Typ');
define('mc_header_cfg_value',										'Wert');
define('mc_header_groups',											'Gruppen');
define('mc_header_id',													'ID');
define('mc_header_name',												'Bezeichner');
define('mc_header_question',										'Frage');
define('mc_header_question_edit',								'Eine Frage erstellen oder bearbeiten');
define('mc_header_question_list',								'Liste der verfügbaren Fragen');
define('mc_header_questions_sum',								'∑');
define('mc_header_questionaire_edit',						'Erstellen oder Bearbeiten Sie einen Fragebogen');
define('mc_header_questionaire_list',						'Liste der verfügbaren Fragebögen');
define('mc_header_status',											'Status');
define('mc_header_title',												'Titel');

define('mc_hint_shuffle_answers',								'Antworten in zufälliger Reihenfolge ausgeben');
define('mc_hint_shuffle_questions',							'Fragen in zufälliger Reihenfolge ausgeben');
define('mc_hint_use_html',											'<span class="small">Speichern Sie den Datensatz um die geänderte Einstellung zu übernehmen und zu aktivieren!</span>');

define('mc_intro_cfg',													'<p>Bearbeiten Sie die Einstellungen für dbMultipleChoice.</p>');
define('mc_intro_cfg_add_item',									'<p>Das Hinzufügen von Einträgen zur Konfiguration ist nur sinnvoll, wenn die angegebenen Werte mit dem Programm korrespondieren.</p>');
define('mc_intro_question_edit',								'<p>Erstellen oder Bearbeiten Sie die einzelnen Fragen und Ordnen Sie die Frage einer beliebigen Gruppe zu.</p>');
define('mc_intro_question_list',								'<p>Wählen Sie die Frage aus, die Sie bearbeiten möchten.</p><p>Um eine neue Frage zu erstellen, klicken Sie auf den Tab <b>Bearbeiten</b>.<p>');
define('mc_intro_questionaire_edit',						'<p>Stellen Sie einen Fragebogen mit den vorhandenen Fragen zusammen und legen Sie sein Verhalten fest.</p><p>Die zur Verfügung stehenden Fragen sind abhängig von den gewählten Gruppen und dem von Ihnen festgelegten Verhalten des Fragebogen. Sie sollten deshalb jedesmal, wenn Sie das Verhalten ändern, den Fragebogen speichern und erst anschließend die Fragen auswählen.</p>');
define('mc_intro_questionaire_list',						'<p>Wählen Sie den Fragebogen aus, den Sie bearbeiten möchten.</p><p>Um einen neuen Fragebogen zu erstellen, klicken Sie auf den Tab <b>%s</b>.</p>');

define('mc_label_answer_id',										'Antwort %s');
define('mc_label_answer_is_correct',						'die Antwort ist <b>richtig</b>');
define('mc_label_behaviour',										'Verhalten');
define('mc_label_cfg_groups',										'Gruppen');
define('mc_label_cfg_max_items',								'Max. Anzahl an Vorschlägen');
define('mc_label_cfg_min_items',								'Min. Anzahl an Vorschlägen');
define('mc_label_cfg_preselect_html',						'Voreinstellung: HTML');
define('mc_label_csv_import',										'CSV Import');
define('mc_label_csv_export',										'CSV Export');
define('mc_label_description',									'Beschreibung');
define('mc_label_mode',													'Verhalten');
define('mc_label_name',													'Bezeichner');
define('mc_label_new_id',												'- <i>neuer Datensatz</i> -');
define('mc_label_prompt_correct',								'Hinweis:<br />Frage <b>richtig</b> beantwortet');
define('mc_label_prompt_false',									'Hinweis:<br />Frage <b>falsch</b> beantwortet');
define('mc_label_prompt_partial',								'Hinweis:<br />Frage <b>teilweise</b> beantwortet');
define('mc_label_question',											'Frage');
define('mc_label_question_groups',							'Gruppe(n)<br /><span class="small">Mehrfachauswahl möglich</span>');
define('mc_label_status',												'Status');
define('mc_label_title',												'Überschrift');
define('mc_label_undefined',										'- nicht definiert -');
define('mc_label_use_html',											'HTML verwenden');

define('mc_mode_qn_mixed',											'MIXED MODUS - es werden alle Fragen verwendet');
define('mc_mode_qn_multiple',										'MULTIPLER MODUS, STRIKT - es werden nur Fragen verwendet, die mehrere richtige Antworten zulassen');
define('mc_mode_qn_none',												'NONE MODUS - es werden nur Fragen verwendet, bei denen keine Antwort richtig ist');
define('mc_mode_qn_single',											'SINGLE MODUS - es werden nur Fragen verwendet, die nur eine richtige Antwort zulassen');
define('mc_mode_qn_partial',										'MULTIPLER MODUS, PROZENTUAL - es werden nur Fragen verwendet, die mehrere richtige Antworten zulassen');
define('mc_mode_multiple',											'MEHRERE Antworten sind möglich, STRIKTE Auswertung');
define('mc_mode_none',													'KEINE Antwort ist richtig');
define('mc_mode_single',												'Es ist nur EINE Antwort richtig');
define('mc_mode_partial',												'MEHRERE Antworten sind möglich, PROZENTUALE Auswertung');

define('mc_msg_cfg_add_exists',									'<p>Der Konfigurationsdatensatz mit dem Bezeichner <b>%s</b> existiert bereits und kann nicht noch einmal hinzugefügt werden!</p>');
define('mc_msg_cfg_add_incomplete',							'<p>Der neu hinzuzufügende Konfigurationsdatensatz ist unvollständig! Bitte prüfen Sie Ihre Angaben!</p>');
define('mc_msg_cfg_add_success',								'<p>Der Konfigurationsdatensatz mit der <b>ID #%05d</b> und dem Bezeichner <b>%s</b> wurde hinzugefügt.</p>');
define('mc_msg_cfg_csv_export',									'<p>Die Konfigurationsdaten wurden als <b>%s</b> im /MEDIA Verzeichnis gesichert.</p>');
define('mc_msg_cfg_id_updated',									'<p>Der Konfigurationsdatensatz mit der <b>ID #%05d</b> und dem Bezeichner <b>%s</b> wurde aktualisiert.</p>');
define('mc_msg_invalid_email',									'<p>Die E-Mail Adresse <b>%s</b> ist nicht gültig, bitte prüfen Sie Ihre Eingabe.</p>');
define('mc_msg_question_item_count_multiple',		'<p>In dem von Ihnen gewählten Modus <b>%s</b> müssen Sie mind. <b>eine richtige</b> Antwort definieren.</p>');
define('mc_msg_question_item_count_none',				'<p>In dem von Ihnen gewählten Modus <b>%s</b> ist <b>keine Antwort</b> zulässig, bitte prüfen Sie Ihre Eingaben.</p>');
define('mc_msg_question_item_count_single',			'<p>In dem von Ihnen gewählten Modus <b>%s</b> ist exakt eine richtige Antwort erlaubt, momentan sind <b>%d</b> Antworten definiert.</p>');
define('mc_msg_question_item_deleted',					'<p>Die Antwort mit der <b>ID %05d</b> wurde gelöscht!</p>');
define('mc_msg_question_item_inserted',					'<p>Die Antwort mit der <b>ID %05d</b> wurde neu hinzugefügt.</p>');
define('mc_msg_question_items_max',							'<p>Sie haben <b>%d</b> mögliche Antworten definiert, es sind jedoch max. <b>%d</b> Antworten zulässig.</p>');
define('mc_msg_question_items_min',							'<p>Sie haben <b>%d</b> mögliche Antworten definiert, es werden jedoch mind. <b>%d</b> Antworten benötigt.</p>');
define('mc_msg_question_item_updated',					'<p>Die Antwort mit der <b>ID %05d</b> wurde aktualisiert.</p>');
define('mc_msg_question_locked_temp',						'<p>Der Datensatz enthält Fehler und wurde vorläufig gesperrt, die Entsperrung erfolgt automatisch, sobald die Fehler behoben sind.</p>');
define('mc_msg_question_no_group',							'<p>Sie müssen mindestens eine <b>Gruppe</b> auswählen!</p>');
define('mc_msg_question_no_questions',					'<p>Es sind noch keine Fragen definiert, erstellen Sie über <b>%s</b> zunächst eine oder mehrere Fragen.</p>');
define('mc_msg_question_name_missing',					'<p>Sie müssen einen <b>Bezeichner</b> für die Frage angeben!</p>');
define('mc_msg_question_question_missing',			'<p>Sie müssen eine <b>Frage</b> formulieren!</p>');
define('mc_msg_question_inserted',							'<p>Der Datensatz mit der <b>ID %05d</b> wurde neu hinzugefügt.</p>');
define('mc_msg_question_updated',								'<p>Der Datensatz mit der <b>ID %05d</b> wurde aktualisiert.</p>');
define('mc_msg_questionaire_groups_empty',			'<p>Es sind keine <b>Gruppen</b> ausgewählt.</p><p>Bitte wählen Sie eine oder mehrere Gruppen aus. legen Sie das Verhalten des Fragebogen fest und speichern Sie den Fragebogen, damit die zur Verfügung stehenden Fragen entsprechend ausgewählt werden können.</p>');
define('mc_msg_questionaire_inserted',					'<p>Der Fragebogen mit der <b>ID %05d</b> wurd neu hinzugefügt.</p>');
define('mc_msg_questionaire_name_missing',			'<p>Sie müssen einen <b>Bezeichner</b> für den Fragebogen festlegen!</p>');
define('mc_msg_questionaire_list_empty',				'<p>Es sind noch keine Fragebögen definiert, erstellen Sie über <b>%s</b> zunächst einen oder mehrere Fragebögen.</p>');
define('mc_msg_questionaire_title_missing',			'<p>Sie müssen einen <b>Titel</b> für den Fragebogen festlegen!</p>');
define('mc_msg_questionaire_updated',						'<p>Der Fragebogen mit der <b>ID %05d</b> wurde aktualisiert.</p>');
define('mc_msg_solutions_mixed',								'<p>Sie haben %01.2f%% der Fragen richtig beantwortet.</p>');
define('mc_msg_solutions_strict',								'<p>Sie haben %d von %d Fragen richtig beantwortet.</p>');

define('mc_status_active',											'Aktiv');
define('mc_status_deleted',											'Gelöscht');
define('mc_status_locked',											'Gesperrt');

define('mc_tab_config',													'Einstellungen');
define('mc_tab_question',												'Fragen');
define('mc_tab_question_edit',									'Frage bearbeiten');
define('mc_tab_question_list',									'Übersicht');
define('mc_tab_questionaire',										'Fragebögen');
define('mc_tab_questionaire_edit',							'Fragebogen bearbeiten');
define('mc_tab_questionaire_list',							'Übersicht');
define('mc_tab_report',													'Auswertung');
define('mc_tab_help',														'?');



?>