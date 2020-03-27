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

define('AJAX_SCRIPT', true);

require(__DIR__.'/../../config.php');
require_once(__DIR__ . '/lib.php');


$action       = optional_param('action', '', PARAM_TEXT);
$id      = required_param('id', PARAM_ALPHANUM);

// if (!confirm_sesskey()) {
//     throw new moodle_exception('invalidsesskey', 'error');
// }

if (!$cm = get_coursemodule_from_id('feedbackmentoria', $id, 0, false, MUST_EXIST)) {
    throw new moodle_exception('invalidcoursemoduleid', 'error');
}

if (!$feedbackmentoria = $DB->get_record('feedbackmentoria', array('id' => $cm->instance))) {
    throw new moodle_exception('notlogged', 'feedbackmentoria');
}

if (!$course = $DB->get_record('course', array('id' => $feedbackmentoria->course))) {
    throw new moodle_exception('invalidcourseid', 'error');
}

if (!$cm = get_coursemodule_from_instance('feedbackmentoria', $feedbackmentoria->id, $course->id)) {
    throw new moodle_exception('invalidcoursemodule', 'error');
}

// if (!isloggedin()) {
//     throw new moodle_exception('notlogged', 'feedbackmentoria');
// }

ob_start();
header('Expires: Sun, 28 Dec 1997 09:32:45 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=utf-8');

switch ($action) {
    
    case 'actions':
        $actions = feedbackmentoria_get_actions($course->id);
        $actions = feedbackmentoria_format_actionlist($actions);
        $response['actions'] = $actions;
        echo json_encode($response);
    break;

    case 'options_filter':

        $students = feedbackmentoria_get($course->id, 'student');        
        $students = feedbackmentoria_format_list($students);
        $teachers = feedbackmentoria_get($course->id, 'teacher');        
        $teachers = feedbackmentoria_format_list($teachers);
        $response['students'] = $students;
        $response['teachers'] = $teachers;
        echo json_encode($response);
    break;

    default:
        break;
}
