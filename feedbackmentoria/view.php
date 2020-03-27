<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Prints an instance of mod_feedbackmentoria.
 *
 * @package     mod_feedbackmentoria
 * @copyright   2020 Eric Bernardo <eric.sousa@cwi.com.br>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id.
$f  = optional_param('f', 0, PARAM_INT);

if ($id) {
    $cm             = get_coursemodule_from_id('feedbackmentoria', $id, 0, false, MUST_EXIST);
    $course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('feedbackmentoria', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($f) {
    $moduleinstance = $DB->get_record('feedbackmentoria', array('id' => $n), '*', MUST_EXIST);
    $course         = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm             = get_coursemodule_from_instance('feedbackmentoria', $moduleinstance->id, $course->id, false, MUST_EXIST);
} else {
    print_error(get_string('missingidandcmid', 'mod_feedbackmentoria'));
}

require_login($course, true, $cm);

$modulecontext = context_module::instance($cm->id);

$event = \mod_feedbackmentoria\event\course_module_viewed::create(array(
    'objectid' => $moduleinstance->id,
    'context' => $modulecontext
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('feedbackmentoria', $moduleinstance);
$event->trigger();

$PAGE->set_url('/mod/feedbackmentoria/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

$PAGE->requires->css('/mod/feedbackmentoria/default.css?time=' . time());

$PAGE->requires->js('/mod/feedbackmentoria/default.js?time=' . time());

// var_dump($cm->id);
echo $OUTPUT->header();

echo '<h3><p>' . ($moduleinstance->name) . '</p></h3>';
echo '<p>' . ($moduleinstance->intro) . '</p>';

echo '
	<p>
		<form id="filter">

			<div class="form-control select-teachers">
    			<label>Professor</label>
				<select class="select" name="teacher"></select>
			</div>
			<div class="form-control select-students">
				<label>Aluno</label>
				<select class="select" name="student"></select>
			</div>
			<div class="form-control button-filter">		
				<button class="btn btn-default">Pesquisar</button>
			</div>
			
		</form>
	</p>
';
/*
echo '
	<div class="feedback">
		<div class="panel panel-default">
		  <div class="panel-heading">Feedback</div>
		  <div class="panel-body">
			<div class="overflow">
				<p>
					<b>Mentorado 22/03/2019 11:50</b>
					Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
					tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
					quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
					consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
					cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
					proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
				</p>
				<hr />
				<p>
					<b>Mentor 22/03/2019 11:55</b>
					Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
					tempor incididunt ut labore et dolore magna aliqua.
				</p>
				<hr />
				<p>
					<b>Mentorado 25/03/2019 10:0H</b>
					Lorem ipsum dolor sit amet??
				</p>
			</div>
			<hr/>
			<div class="form-submit">
				<label>Adicionar comentário</label>
				<textarea></textarea>
				<input type="submit" />
			</div>
		  </div>
		</div>
	</div>
';

echo '
	<div class="acoes">
		<div class="panel panel-default">
		  <div class="panel-heading">Ações</div>
		  <div class="panel-body">
		  	<div class="overflow">
				<div class="checkbox">
					<label><input type="checkbox" value="">Lorem ipsum dolor 1</label>
				</div>
				<div class="checkbox">
					<label><input type="checkbox" value="">Lorem ipsum dolor 2</label>
				</div>
				<div class="checkbox">
					<label><input type="checkbox" value="" checked>Lorem ipsum dolor 3</label>
				</div>
		  	</div>
		  	<hr/>
		  	<div class="form-submit">
		  		<label>Adicionar ações</label>
				<textarea></textarea>
				<input type="submit" />
			</div>
		  </div>
		</div>
	</div>
';
*/

include($CFG->dirroot.'/mod/feedbackmentoria/studentsview.php');

echo $OUTPUT->footer();
